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
                'JENIS'
            ])
            ->where('JENIS', 'REN')
            ->whereNotNull('INVOICE')
            ->whereNull('kwt')
            // ->where('kwt', '=', '')
            // ->where('GRAND', '>', 0)// ambil master saja
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
                        class="btn btn-sm btn-primary btn-show-invoice-dgn-kwt"
                        data-invoice="'.$row->INVOICE.'">
                        Proses
                    </button>
                ';
            })

            ->rawColumns(['action'])
            ->make(true);
    }

}
