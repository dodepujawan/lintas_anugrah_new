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
use App\Exports\InvoiceDgnExport;
use Maatwebsite\Excel\Facades\Excel;

class RentPendinginGenerateInvoiceController extends Controller
{
    // ### PERUBAHAN DARI SEBELUMNYA CONTROLER KWITANSI SEKARANG KE INVOICE GENERATE
    public function index()
    {
        return view('rentPendinginInvoiceGen.rentPendingin-gen-invoice');
    }

    public function getDataInvoiceGen(Request $request)
    {
        $query = Expedisi::select([
                'INVOICE',
                'TGLINVOICE',
                'CUSTOMER',
                'GRAND',
                'PIUTANG',
                'USERINV',
                'JENIS',
                'NOSJ'
            ])
            ->where('JENIS', 'REN')
            ->where(function($q){
                $q->whereNotNull('NOMUAT')
                ->where('NOMUAT', '!=', '');
            })
            // ->whereNotNull('INVOICE')
            // ->whereNull('kwt')
            // ->where('kwt', '=', '')
            // ->where('GRAND', '>', 0)// ambil master saja
            ->latest();

            if($request->status_inv_gen == 'belum'){
                $query->whereNull('INVOICE');
            }

            if($request->status_inv_gen == 'sudah'){
                $query->where(function($q){
                    $q->whereNotNull('INVOICE')
                    ->where('INVOICE', '!=', '');
                });
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

            ->addColumn('action', function ($row) use ($request) {

                if($request->status_inv_gen == 'sudah'){
                    return '
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-warning d-flex align-items-center justify-content-center btn-edit-inv-gen-dgn" style="width:32px;height:32px;" title="Edit" data-nosj="'.$row->NOSJ.'">
                                <i class="bx bx-pencil"></i>
                            </button>
                        </div>
                    ';
                }
                // <button class="btn btn-sm btn-danger d-flex align-items-center justify-content-center btn-hapus-inv-gen-dgn" style="width:32px;height:32px;" title="Hapus" data-invoice="'.$row->INVOICE.'">
                //                 <i class="bx bx-trash"></i>
                //             </button>

                return '
                    <button
                        class="btn btn-sm btn-primary btn-show-invoice-dgn-gen"
                        data-nosj="'.$row->NOSJ.'">
                        Proses
                    </button>
                ';
            })

            ->rawColumns(['action'])
            ->make(true);
    }

    public function showInvoiceDetail($nosj)
    {
        // =====================================
        // AMBIL SJ
        // =====================================
        $row = Expedisi::where('NOSJ', $nosj)->first();
        if (!$row) {
            return response()->json([
                'status' => false,
                'message' => 'Data SJ tidak ditemukan'
            ]);
        }
        // =====================================
        // DATA ARH
        // =====================================
        $arh = null;
        if (!empty($row->INVOICE)) {
            $arh = Arh::where(
                'NOFAKTUR',
                $row->INVOICE
            )->first();
        }
        // =====================================
        // TOP KREDIT CUSTOMER
        // =====================================
        $kodeCustomer = $row->CUSTOMER_KODE ?? null;
        $topKredit = 0;
        if ($kodeCustomer) {
            $customer = Mcustomer::where(
                'kode_cus',
                $kodeCustomer
            )->first();
            $topKredit = $customer->TOPKREDIT ?? 0;
        }
        // =====================================
        // HITUNG JATUH TEMPO
        // =====================================
        $tglInvoice = $row->TGLINVOICE
            ? Carbon::parse($row->TGLINVOICE)
            : now();
        $tglJatuhTempo = $tglInvoice
            ->copy()
            ->addDays((int) $topKredit);
        // =====================================
        // RESPONSE
        // =====================================
        return response()->json([
            'status' => true,
            'data' => [
                'invoice'      => $row->INVOICE ?? '',
                'tgl_invoice'  => $row->TGLINVOICE,
                'customer'     => $row->CUSTOMER,
                'nomor_muat'   => $row->NOMUAT ?? '',
                'kendaraan'    => $row->NAMA_KENDARAAN ?? '',
                'sub_total'    => $row->HARGA ?? 0,
                'disc_persen'  => $row->DISC ?? 0,
                'disc_rp'      => $row->NDISC ?? 0,
                'd_charge'     => $row->DC ?? 0,
                'total'        => $row->TOTAL ?? 0,
                'ppn'          => $row->PPN ?? 0,
                'grand'        => $row->GRAND ?? 0,
                'piutang'      => (int) ceil(
                    $row->PIUTANG ?? 0
                ),
                // langsung single SJ
                'nomor_sj'     => $row->NOSJ,
                // =============================
                // DATA ARH
                // =============================
                'tgl_jt'       => $arh->TGLJT ?? $tglJatuhTempo->format('Y-m-d'),
                'piutang_arh'  => $arh->PIUTANG ?? 0,
                'bayar'        => $row->BAYAR ?? 0,
                'saldo'        => $arh->SALDO ?? 0,
            ]
        ]);
    }

    public function prosesInvoicePembayaran(Request $request)
    {
        try {
            $invoice = $request->invoice;
            DB::transaction(function () use ($request, &$invoice) {
                // =====================================
                // PARSING
                // =====================================
                $bayar = (int) preg_replace(
                    '/[^0-9]/',
                    '',
                    $request->bayar
                );
                $top = (int) preg_replace(
                    '/[^0-9]/',
                    '',
                    $request->top
                );
                $tglJtp = $request->tgl_jtp;
                $nosj   = $request->nosj;
                // =====================================
                // STORE BARU
                // =====================================
                if (empty($invoice)) {
                    // generate invoice baru
                    $invoice = $this->generateInvoiceOnline();
                    // ambil data SJ
                    $row = Expedisi::where('NOSJ', $nosj)
                        ->lockForUpdate()
                        ->first();
                    if (!$row) {
                        throw new \Exception('SJ tidak ditemukan');
                    }
                    // assign invoice baru
                    $row->INVOICE    = $invoice;
                    $row->TGLINVOICE = now();
                    $row->STS        = 'INVOICE';
                } else {
                    // =================================
                    // UPDATE EXISTING
                    // =================================
                    $row = Expedisi::where('INVOICE', $invoice)
                        ->where('GRAND', '>', 0)
                        ->lockForUpdate()
                        ->first();
                    if (!$row) {
                        throw new \Exception('Invoice tidak ditemukan');
                    }
                }

                // =====================================
                // GRAND
                // =====================================
                $grand = (int) ($row->GRAND ?? 0);
                // =====================================
                // VALIDASI
                // =====================================
                if ($bayar < 0) {
                    throw new \Exception(
                        'Nominal bayar tidak valid'
                    );
                }
                if ($bayar > $grand) {
                    throw new \Exception(
                        'Bayar tidak boleh lebih besar dari total'
                    );
                }
                // =====================================
                // HITUNG PIUTANG
                // =====================================
                $piutangBaru = $grand - $bayar;
                // =====================================
                // UPDATE EXPEDISI
                // =====================================
                $row->BAYAR   = $bayar;
                $row->PIUTANG = $piutangBaru;
                $row->TGLJT   = $tglJtp;
                $row->save();
                // =====================================
                // HANDLE ARH
                // =====================================

                // MASIH ADA PIUTANG
                if ($piutangBaru > 0) {
                    Arh::updateOrCreate(
                        [
                            'NOFAKTUR' => $invoice
                        ],
                        [
                            'TGLFAKTUR'   => $row->TGLINVOICE ?? now(),
                            'CUSTOMER'    => $row->CUSTOMER,
                            'SALDO'       => $top,
                            'PIUTANG'     => $piutangBaru,
                            'DISCOUNT'    => $row->NDISC ?? 0,
                            'TGLJT'       => $tglJtp,
                            'CABANG'      => $row->CABANG ?? '',
                            'KETERANGAN'  => 'INVOICE DARI EXPEDISI',
                            'USER'        => auth()->user()->user_id,
                            'USER_UPDATE' => auth()->user()->user_id,
                            'updated_at'  => now()
                        ]
                    );
                } else {
                    // =================================
                    // LUNAS -> HAPUS ARH
                    // =================================
                    Arh::where(
                        'NOFAKTUR',
                        $invoice
                    )->delete();
                }
            });
            return response()->json([
                'status'  => true,
                'message' => 'Invoice berhasil diproses',
                'redirect' => route(
                    'rentPendinginGenerate.pdfGenerate',
                    [
                        'invoiceNo' => $invoice
                    ]
                )
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
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

    // ############## EDIT INVOICE
    public function indexEdit()
    {
        return view('rentPendinginInvoiceGen.rentPendingin-gen-edit');
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

    public function export(Request $request)
    {
        $request->validate([
            'tanggal_dari'   => 'required|date',
            'tanggal_sampai' => 'required|date|after_or_equal:tanggal_dari',
            'filter_kwt_dgn' => 'required'
        ]);

        $tanggalDari   = $request->tanggal_dari;
        $tanggalSampai = $request->tanggal_sampai;
        $status        = $request->filter_kwt_dgn;

        $filename =
            'laporan_invoice_pendingin_' .
            $status . '_' .
            $tanggalDari . '_sd_' .
            $tanggalSampai .
            '.xlsx';

        return Excel::download(

            new InvoiceDgnExport(
                $tanggalDari,
                $tanggalSampai,
                $status
            ),

            $filename
        );
    }

    public function pdfInvoiceGenerate($invoice){
        $master = Expedisi::where('INVOICE', $invoice)
            ->where('GRAND', '>', 0)
            ->firstOrFail();

        $details = Expedisi::where('INVOICE', $invoice)
            ->orderBy('NOSJ')
            ->get();

        $arh = Arh::where('NOFAKTUR', $invoice)
            ->first();

        $signature = Signature::orderByDesc('id')->first();

        $html = view('rentPendinginInvoiceGen.rentPendingin-gen-pdf', compact('master','details','arh','signature'))->render();

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
