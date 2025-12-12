<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Pricedingincus;
use App\Models\Priceinginhis;
use App\Models\Pricedingin;
use App\Models\Mcustomer;

class PricedinginCustomerController extends Controller
{
    public function index(){
        return view('priceCustomer.priceCustomer');
    }

    public function getData(){
        $customers = Mcustomer::select(['id', 'kode', 'nama', 'jenis_usaha', 'telepon', 'email', 'created_at']);

        return DataTables::of($customers)
            ->addIndexColumn()
            ->addColumn('action', function($customer) {
                return '
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info view-btn-customer-price-dingin" id="show_price_dingin_cus" data-id="'.$customer->kode.'" data-bs-toggle="tooltip" title="View">
                            <i class="bx bx-show"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
