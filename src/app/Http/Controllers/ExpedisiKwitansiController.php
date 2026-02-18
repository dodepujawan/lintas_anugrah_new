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
                'USERINV',
                'JENIS'
            ])
            ->where('JENIS', 'EKS')
            ->whereNotNull('INVOICE')
            ->whereNull('kwt')
            // ->where('kwt', '=', '')
            ->where('GRAND', '>', 0)// ambil master saja
            ->latest();

        return DataTables::of($query)

            ->addIndexColumn()

            ->editColumn('TGLINVOICE', function ($row) {
                return \Carbon\Carbon::parse($row->TGLINVOICE)
                    ->format('d-m-Y');
            })

            ->editColumn('GRAND', function ($row) {
                return number_format($row->GRAND, 0, ',', '.');
            })

            ->addColumn('action', function ($row) {
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

                'nomor_sj'     => $details->pluck('NOSJ')->implode(', ')
            ]
        ]);
    }

    public function prosesKwitansiStore(Request $request){
        try {
            // untuk mengambil nilai dalam transaction dibawah supaya bisa kirim ke json respon karna json respon diluar transaction
            $invoice = null;
            DB::transaction(function () use ($request, &$invoice) {

                $invoice = $request->invoice;
                $bayar   = str_replace('.', '', $request->bayar);
                $top     = $request->top;
                $tglJtp  = $request->tgl_jtp;

                // 🔹 Generate Nomor KW
                $noKw = $this->generateKW();

                // =======================
                // UPDATE ARH
                // =======================
                Arh::where('NOFAKTUR', $invoice)
                    ->update([
                        'BAYAR' => $bayar,
                        'SALDO' => $top
                    ]);

                // =======================
                // UPDATE EXPEDISI
                // =======================
                Expedisi::where('INVOICE', $invoice)
                    ->update([
                        'kwt'    => $noKw,
                        'TGLKW' => $tglJtp
                    ]);

            });

            return response()->json([
                'status' => true,
                'message' => 'Kwitansi berhasil diproses',
                'redirect' => route('expedisiKwitansi.pdfKwitansi', ['invoiceNo' => $invoice])
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
