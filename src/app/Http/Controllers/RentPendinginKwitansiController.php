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

class RentPendinginKwitansiController extends Controller
{
    public function index()
    {
        return view('rentPendinginKwitansi.rentPendingin-kwitansi');
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
                'JENIS',
                'kwt'
            ])
            ->where('JENIS', 'REN')
            ->whereNotNull('INVOICE')
            // ->whereNull('kwt')
            // ->where('kwt', '=', '')
            // ->where('GRAND', '>', 0)// ambil master saja
            ->latest();

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
                            <button class="btn btn-sm btn-warning d-flex align-items-center justify-content-center btn-edit-kwt-dgn" style="width:32px;height:32px;" title="Edit" data-invoice="'.$row->INVOICE.'">
                                <i class="bx bx-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger d-flex align-items-center justify-content-center btn-hapus-kwt-dgn" style="width:32px;height:32px;" title="Hapus" data-invoice="'.$row->INVOICE.'">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    ';
                }

                return '
                    <button
                        class="btn btn-sm btn-primary btn-show-invoice-dgn-kwt"
                        data-invoice="'.$row->INVOICE.'">
                        Proses
                    </button>
                ';
            })

            ->rawColumns(['action'])
            ->make(true);
    }

    public function showInvoiceDetail($invoice)
    {
        $master = Expedisi::where('INVOICE', $invoice)
            // ->where('GRAND', '>', 0)
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
