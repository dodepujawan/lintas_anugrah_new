<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Expedisi;
use App\Models\Rekening;
use App\Models\Signature;
use App\Models\Mcustomer;
use App\Models\Arh;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Carbon\Carbon;
use Exception;

class ExpedisiGenerateInvoiceController extends Controller
{
    public function index()
    {
        return view('expedisiInvoieGen.expedisi-invoice-gen');
    }

    public function getDataInvoiceGen(Request $request){
        $query = Expedisi::select([
                'INVOICE',
                'TGLINVOICE',
                'CUSTOMER',
                'GRAND',
                'PIUTANG',
                'GB',
                'NOMUAT',
                'TGLMUAT',
                'NOSJ'
            ])
            ->where('JENIS', 'EKS')
            ->where(function($q){
                $q->whereNotNull('NOMUAT')
                ->orWhere('NOMUAT', '!=', '');
            });

        // =============================
        // FILTER STATUS INVOICE
        // =============================
        if ($request->status_invoice == 'belum') {
            $query->where(function($q){
                $q->whereNull('INVOICE')
                ->orWhere('INVOICE', '');
            });
        }

        if ($request->status_invoice == 'sudah') {
            $query->where(function($q){
                $q->whereNotNull('INVOICE')
                ->where('INVOICE', '!=', '');
            })
            ->where('GRAND', '>', 0);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->editColumn('TGLINVOICE', function ($row) {
                return $row->TGLINVOICE
                    ? \Carbon\Carbon::parse($row->TGLINVOICE)->format('d-m-Y')
                    : '-';
            })

            ->editColumn('GRAND', function ($row) {
                return number_format($row->GRAND ?? 0, 0, ',', '.');
            })

            ->addColumn('gb', function($row){
                return $row->GB ?: '-';
            })

            ->addColumn('action', function ($row) use ($request) {

                // =============================
                // SUDAH INVOICE
                // =============================
                if ($request->status_invoice == 'sudah') {
                    return '
                        <button
                            class="btn btn-sm btn-primary btn-buat-invoice"
                            data-nomuat="'.$row->NOMUAT.'">
                            Edit
                        </button>
                    ';
                }

                // =============================
                // BELUM INVOICE
                // =============================
                return '
                    <button
                        class="btn btn-sm btn-success btn-buat-invoice"
                        data-nomuat="'.$row->NOMUAT.'">
                        Buat Invoice
                    </button>
                ';
            })

            ->rawColumns(['action'])
            ->make(true);
    }

    public function showInvoiceGabung($muatNo){
        $rows = Expedisi::where('NOMUAT', $muatNo)->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        // 🔥 ambil baris pertama (untuk identitas)
        $firstRow = $rows->first();

        // =============================
        // DETEKSI GB ATAU TIDAK
        // =============================
        $hasGB = $rows->whereNotNull('GB')->where('GB', '!=', '')->count() > 0;

        if ($hasGB) {
            $master = $rows->firstWhere('GRAND', '>', 0);
        } else {
            $master = $firstRow;
        }

        if (!$master) {
            return response()->json([
                'status' => false,
                'message' => 'Data master tidak ditemukan'
            ]);
        }

        // =============================
        // DETAIL
        // =============================
        $details = $rows->sortBy('NOSJ');

        // =============================
        // ARH
        // =============================
        $arh = null;
        if (!empty($master->INVOICE)) {
            $arh = Arh::where('NOFAKTUR', $master->INVOICE)->first();
        }

        // =============================
        // JATUH TEMPO
        // =============================
        $kodeCustomer = $firstRow->CUSTOMER_KODE ?? null;
        $topKredit = 0;

        if ($kodeCustomer) {
            $customer = Mcustomer::where('kode_cus', $kodeCustomer)->first();
            $topKredit = $customer->TOPKREDIT ?? 0;
        }

        $tglInvoice = $master->TGLINVOICE
            ? Carbon::parse($master->TGLINVOICE)
            : now();

        $tglJatuhTempo = $tglInvoice->copy()->addDays((int)$topKredit);

        return response()->json([
            'status' => true,
            'data' => [
                'invoice'      => $master->INVOICE ?? '',
                'tgl_invoice'  => $master->TGLINVOICE,

                // 🔥 IDENTITAS dari firstRow
                'customer'     => $firstRow->CUSTOMER,
                'nomor_muat'   => $firstRow->NOMUAT ?? '',
                'tgl_muat'   => $firstRow->TGLMUAT ?? '',
                'kendaraan'    => $firstRow->NAMA_KENDARAAN ?? '',

                // 🔥 NILAI dari master
                'sub_total'    => $master->HARGA ?? 0,
                'disc_persen'  => $master->DISC ?? 0,
                'disc_rp'      => $master->NDISC ?? 0,
                'd_charge'     => $master->DC ?? 0,
                'total'        => $master->TOTAL ?? 0,
                'ppn'          => $master->PPN ?? 0,
                'grand'        => $master->GRAND ?? 0,
                'piutang'      => (int) ceil($master->PIUTANG ?? 0),

                'nomor_sj'     => $details->pluck('NOSJ')->implode(', '),

                // ARH
                'tgl_jt'       => $arh->TGLJT ?? $tglJatuhTempo->format('Y-m-d'),
                'piutang_arh'  => $arh->PIUTANG ?? 0,
                'bayar'        => $arh->BAYAR ?? 0,
                'saldo'        => $arh->SALDO ?? 0,
            ]
        ]);
    }

    public function prosesInvoiceStore(Request $request){
        try {

            $invoiceNo = null;

            DB::transaction(function () use ($request, &$invoiceNo) {

                // =============================
                // 🔥 AMBIL NOMUAT DARI REQUEST
                // =============================
                $nomuat = $request->nomuat;

                if (!$nomuat) {
                    throw new \Exception('Nomor muat tidak ditemukan');
                }

                // parsing angka
                $bayar = (int) preg_replace('/[^0-9]/', '', $request->bayar);
                $top   = (int) preg_replace('/[^0-9]/', '', $request->top);
                $tglJtp = $request->tgl_jtp;

                // =============================
                // 1. Ambil data berdasarkan NOMUAT
                // =============================
                $rows = Expedisi::where('NOMUAT', $nomuat)
                    ->lockForUpdate()
                    ->get();

                if ($rows->isEmpty()) {
                    throw new \Exception('Data tidak ditemukan');
                }

                // =============================
                // 2. Cek GB
                // =============================
                $hasGB = $rows->whereNotNull('GB')
                            ->where('GB', '!=', '')
                            ->count() > 0;

                if ($hasGB) {
                    $gb = $rows->firstWhere('GB', '!=', '')->GB;

                    $targetRows = Expedisi::where('GB', $gb)
                        ->lockForUpdate()
                        ->get();
                } else {
                    $targetRows = $rows;
                }

                // =============================
                // 3. Validasi (sudah invoice?)
                // =============================
                foreach ($targetRows as $row) {
                    if (!is_null($row->INVOICE) && trim($row->INVOICE) !== '') {
                        throw new \Exception("Sudah ada invoice pada data ini");
                    }
                }

                // =============================
                // 4. Ambil MASTER (yang GRAND > 0)
                // =============================
                $master = $targetRows->firstWhere('GRAND', '>', 0)
                        ?? $targetRows->first();

                $grand = (int) ($master->GRAND ?? 0);

                // =============================
                // 5. Validasi bayar
                // =============================
                if ($bayar < 0) {
                    throw new \Exception('Nominal bayar tidak valid');
                }

                if ($bayar > $grand) {
                    throw new \Exception('Bayar tidak boleh lebih besar dari total');
                }

                $piutang = $grand - $bayar;

                // =============================
                // 6. Generate INVOICE
                // =============================
                $invoiceNo = $this->generateInvoiceOnline();

                // =============================
                // 7. Update EXPEDISI
                // =============================
                foreach ($targetRows as $row) {

                    $row->INVOICE    = $invoiceNo;
                    $row->TGLINVOICE = now();
                    $row->STS        = 'INVOICE';
                    $row->TGLJT      = $tglJtp;

                    $row->USERINV = auth()->user()->user_id . '-' . now()->format('d-m-Y h:i:s A');

                    $row->save();
                }

                // =============================
                // 8. INSERT ARH
                // =============================
                Arh::create([
                    'NOFAKTUR'   => $invoiceNo,
                    'TGLFAKTUR'  => now(),
                    'CUSTOMER'   => $master->CUSTOMER,
                    'PIUTANG'    => $piutang,
                    'DISCOUNT'   => $master->NDISC ?? 0,
                    'BAYAR'      => $bayar,
                    'SALDO'      => $top,
                    'TGLJT'      => $tglJtp,
                    'CABANG'     => $master->CABANG ?? '',
                    'KETERANGAN' => 'INVOICE DARI EXPEDISI',
                    'USER'       => auth()->user()->user_id,
                ]);
            });

            return response()->json([
                'status' => true,
                'message' => 'Invoice berhasil dibuat',
                'invoiceNo' => $invoiceNo
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateInvoice(Request $request){
        try {
            $invoice = $request->invoice;
            DB::transaction(function () use ($request, $invoice) {
                if (!$invoice) {
                    throw new \Exception('Invoice tidak ditemukan');
                }

                // parsing angka
                $bayar = (int) preg_replace('/[^0-9]/', '', $request->bayar);
                $top   = (int) preg_replace('/[^0-9]/', '', $request->top);
                $tglJtp = $request->tgl_jtp;

                // =============================
                // 1. Ambil MASTER dari EXPEDISI
                // =============================
                $master = Expedisi::where('INVOICE', $invoice)
                    ->where('GRAND', '>', 0)
                    ->first();

                if (!$master) {
                    throw new \Exception('Data invoice tidak ditemukan');
                }

                $grand = (int) $master->GRAND;

                // =============================
                // 2. VALIDASI
                // =============================
                if ($bayar < 0) {
                    throw new \Exception('Nominal bayar tidak valid');
                }

                if ($bayar > $grand) {
                    throw new \Exception('Bayar tidak boleh lebih besar dari total');
                }

                // =============================
                // 3. HITUNG PIUTANG
                // =============================
                $piutang = $grand - $bayar;

                // =============================
                // 4. UPDATE ARH
                // =============================
                Arh::where('NOFAKTUR', $invoice)
                    ->update([
                        'BAYAR'       => $bayar,
                        'PIUTANG'     => $piutang,
                        'SALDO'       => $top,
                        'TGLJT'       => $tglJtp,
                        'USER_UPDATE' => auth()->user()->user_id,
                        'updated_at'  => now()
                    ]);

                // =============================
                // 5. UPDATE EXPEDISI
                // =============================
                Expedisi::where('INVOICE', $invoice)
                    ->update([
                        'TGLJT' => $tglJtp
                    ]);
            });

            return response()->json([
                'status' => true,
                'message' => 'Invoice berhasil diupdate',
                'invoiceNo' => $invoice
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function prosesKwitansiDelete(Request $request){
        try {

            DB::transaction(function () use ($request) {

                $invoice = $request->invoice;

                $expedisi = Expedisi::where('INVOICE', $invoice)->first();

                if (!$expedisi) {
                    throw new \Exception('Invoice tidak ditemukan');
                }

                $grand = $expedisi->GRAND;

                // ===============================
                // Reverse ARH
                // ===============================
                Arh::where('NOFAKTUR', $invoice)
                    ->update([
                        'BAYAR'       => 0,
                        'SALDO'       => 0,
                        'PIUTANG'     => $grand, // kembali seperti awal
                        'TGLJT'       => null,
                        'USER_UPDATE' => auth()->user()->user_id,
                        'updated_at'  => now()
                    ]);

                // ===============================
                // Reverse EXPEDISI
                // ===============================
                Expedisi::where('INVOICE', $invoice)
                    ->update([
                        'kwt'   => null,
                        'TGLKW' => null,
                        'TGLJT' => null
                    ]);
            });

            return response()->json([
                'status' => true,
                'message' => 'Kwitansi berhasil dihapus / direverse'
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function pdfInvoiceKwitansi($invoice){
        $master = Expedisi::where('INVOICE', $invoice)
            ->where('GRAND', '>', 0)
            ->firstOrFail();

        $details = Expedisi::where('INVOICE', $invoice)
            ->orderBy('NOSJ')
            ->get();

        $arh = Arh::where('NOFAKTUR', $invoice)
            ->first();

        $signature = Signature::orderByDesc('id')->first();

        $html = view('expedisiKwitansi.expedisi-kwitansi-pdf', compact('master','details','arh','signature'))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 20,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_right' => 15,
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('Invoice-'.$invoice.'.pdf', 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    private function generateInvoiceOnline(): string{
        $tahun = now()->format('Y');

        $last = Expedisi::where('INVOICE', 'like', "FJO{$tahun}%")
            ->orderBy('INVOICE', 'desc')
            ->lockForUpdate()
            ->first();

        $lastNo = $last
            ? intval(substr($last->INVOICE, -6))
            : 0;

        return 'FJO' . $tahun . str_pad($lastNo + 1, 6, '0', STR_PAD_LEFT);
    }

    private function generateKW()
    {
        $year = now()->format('Y');

        $last = Expedisi::where('kwt', 'like', 'KW'.$year.'%')
            ->lockForUpdate()
            ->orderByDesc('kwt')
            ->value('kwt');

        if (!$last) {
            return 'KW' . $year . '0000001';
        }

        $number = (int) substr($last, 7);
        $number++;

        return 'KW' . $year . str_pad($number, 7, '0', STR_PAD_LEFT);
    }

}
