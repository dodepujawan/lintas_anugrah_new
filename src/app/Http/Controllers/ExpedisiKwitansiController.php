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
                'USERINV'
            ])
            ->whereNotNull('INVOICE')
            ->where('GRAND', '>', 0); // ambil master saja

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
                'piutang'      => $master->PIUTANG,

                'nomor_sj'     => $details->pluck('NOSJ')->implode(', ')
            ]
        ]);
    }

}
