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

class ExpedisiKwitansiController extends Controller
{
    public function index()
    {
        return view('expedisiKwitansi.expedisi-kwitansi');
    }

    public function getDataInvoiceKwt(Request $request)
    {
        $query = Expedisi::select([
                'INVOICE',
                'TGLINVOICE',
                'CUSTOMER',
                'GRAND',
                'PIUTANG',
                'kwt'
            ])
            ->where('JENIS', 'EKS')
            ->whereNotNull('INVOICE')
            ->where('GRAND', '>', 0);

        if($request->status_kwt == 'belum'){
            $query->whereNull('kwt');
        }

        if($request->status_kwt == 'sudah'){
            $query->whereNotNull('kwt');
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->editColumn('TGLINVOICE', function ($row) {
                return \Carbon\Carbon::parse($row->TGLINVOICE)
                    ->format('d-m-Y');
            })

            ->editColumn('GRAND', function ($row) {
                return number_format($row->GRAND, 0, ',', '.');
            })

            ->addColumn('no_kwt', function($row){
                return $row->kwt ?? '-';
            })

            ->addColumn('action', function ($row) use ($request) {

                if($request->status_kwt == 'sudah'){
                    return '
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-warning d-flex align-items-center justify-content-center btn-edit-kwt-exp" style="width:32px;height:32px;" title="Edit" data-invoice="'.$row->INVOICE.'">
                                <i class="bx bx-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger d-flex align-items-center justify-content-center btn-hapus-kwt-exp" style="width:32px;height:32px;" title="Hapus" data-invoice="'.$row->INVOICE.'">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    ';
                }

                return '
                    <button
                        class="btn btn-sm btn-primary btn-show-invoice-kwt"
                        data-invoice="'.$row->INVOICE.'">
                        Proses
                    </button>
                ';
            })

            ->rawColumns(['action'])
            ->make(true);
    }

    public function showInvoiceGabung($invoice)
    {
        $master = Expedisi::where('INVOICE', $invoice)
            ->where('GRAND', '>', 0)
            ->first();

        if (!$master) {
            return response()->json([
                'status' => false,
                'message' => 'Invoice tidak ditemukan'
            ]);
        }

        // ambil semua SJ yang tergabung
        $details = Expedisi::where('INVOICE', $invoice)
            ->orderBy('NOSJ')
            ->get();

        // =============================
        // Tambahan untuk mode edit ambil nilai arh
        // =============================

        $arh = Arh::where('NOFAKTUR', $invoice)->first();

        // =============================
        // HITUNG TGL JATUH TEMPO
        // =============================

        // ambil kode customer dari expedisi
        $kodeCustomer = $master->CUSTOMER_KODE ?? null;

        $topKredit = 0;

        if ($kodeCustomer) {
            $customer = Mcustomer::where('kode_cus', $kodeCustomer)->first();
            $topKredit = $customer->TOPKREDIT ?? 0;
        }

        // hitung jatuh tempo dari TGLINVOICE
        $tglInvoice = Carbon::parse($master->TGLINVOICE);
        $tglJatuhTempo = $tglInvoice->copy()->addDays((int)$topKredit);

        return response()->json([
            'status' => true,
            'data' => [
                'invoice'      => $master->INVOICE,
                'tgl_invoice'  => $master->TGLINVOICE,
                'customer'     => $master->CUSTOMER,
                'nomor_muat'   => $master->NOMUAT ?? '',
                'kendaraan'    => $master->NAMA_KENDARAAN ?? '',

                'sub_total'    => $master->HARGA,
                'disc_persen'  => $master->DISC,
                'disc_rp'      => $master->NDISC,
                'd_charge'     => $master->DC,
                'total'        => $master->TOTAL,
                'ppn'          => $master->PPN,
                'grand'        => $master->GRAND,
                // 'piutang'      => $master->PIUTANG,
                // dikarenakan tipe data double jadi yang tersimpan malah isi koma
                'piutang' => (int) ceil($master->PIUTANG),

                'nomor_sj'     => $details->pluck('NOSJ')->implode(', '),

                // ========= DATA ARH =========
                'tgl_jt'       => $arh->TGLJT ?? $tglJatuhTempo->format('Y-m-d'),
                'piutang_arh'  => $arh->PIUTANG ?? null,
                'bayar'        => $arh->BAYAR ?? null,
                'saldo'        => $arh->SALDO ?? null,
            ]
        ]);
    }

    public function prosesKwitansiStore(Request $request)
    {
        try {

            $invoice = null;

            DB::transaction(function () use ($request, &$invoice) {

                $invoice  = $request->invoice;
                $bayar = (int) preg_replace('/[^0-9]/', '', $request->bayar);
                $top   = (int) preg_replace('/[^0-9]/', '', $request->top);
                $tglJtp   = $request->tgl_jtp;
                $flag     = $request->kwt_flag;

                // ambil GRAND dari expedisi
                $expedisi = Expedisi::where('INVOICE', $invoice)->first();

                if (!$expedisi) {
                    throw new \Exception('Invoice tidak ditemukan');
                }
                $grand = $expedisi->GRAND;

                // 🔹 hitung PIUTANG baru
                $piutangBaru = $grand - $bayar;

                // ===============================
                // MODE STORE (BARU)
                // ===============================
                if ($flag == 0) {

                    $noKw = $this->generateKW();

                    // Update ARH
                    Arh::where('NOFAKTUR', $invoice)
                        ->update([
                            'BAYAR'  => $bayar,
                            'SALDO'  => $top,
                            'PIUTANG' => $piutangBaru,
                            'TGLJT'  => $tglJtp,
                            'USER_UPDATE' => auth()->user()->user_id,
                        ]);

                    // Update EXPEDISI
                    Expedisi::where('INVOICE', $invoice)
                        ->update([
                            'kwt'   => $noKw,
                            'TGLKW' => now(),
                            'TGLJT' => $tglJtp
                        ]);
                }

                // ===============================
                // MODE EDIT
                // ===============================
                if ($flag == 1) {

                    Arh::where('NOFAKTUR', $invoice)
                        ->update([
                            'BAYAR'       => $bayar,
                            'SALDO'       => $top,
                            'PIUTANG' => $piutangBaru,
                            'USER_UPDATE' => auth()->user()->user_id,
                            'updated_at'  => now()
                        ]);

                    // Expedisi tidak perlu ubah TGLJT
                    // kwt juga tidak perlu diganti
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'Kwitansi berhasil diproses',
                'redirect' => route('expedisiKwitansi.pdfKwitansi', [
                    'invoiceNo' => $invoice
                ])
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

        $signature = Signature::orderByDesc('id')->first();

        $html = view('expedisiKwitansi.expedisi-kwitansi-pdf', compact('master','details','signature'))->render();

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
