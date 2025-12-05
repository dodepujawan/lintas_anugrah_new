<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Pricecus;
use App\Models\Pricecushis;
use App\Models\Prices;
use App\Models\Mcustomer;


class PricesCustomerController extends Controller
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
                        <button class="btn btn-sm btn-info view-btn-customer" id="show_price_cus" data-id="'.$customer->kode.'" data-bs-toggle="tooltip" title="View">
                            <i class="bx bx-show"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getPrice(Request $request, $kodecus){
        // Ambil data customer
        $customer = DB::table('mcustomer as c')
            ->where('c.kode', $kodecus)
            ->first();

        // Siapkan metadata customer
        $customerData = [
            'customer_kode' => $customer->kode ?? null,
            'customer_nama' => $customer->nama ?? null,
            'jenis_usaha'   => $customer->jenis_usaha ?? null,
            'alamat'        => $customer->alamat ?? null,
            'pemilik_nama'  => $customer->pemilik_nama ?? null,
        ];

        // Jika customer tidak ditemukan → return kosong tapi metadata tetap ada
        if (!$customer) {
            return DataTables::of(collect([]))
                ->with($customerData)
                ->make(true);
        }

        $customerCreatedAt = $customer->created_at;
        $final = collect();


        // ============================================================
        // 1️⃣ PRIORITAS PRICECUS (punya KODECUS asli)
        // ============================================================

        $pricecus = DB::table('pricecus as pc')
            ->leftJoin('rute as r', 'pc.RUTE', '=', 'r.id')
            ->select('pc.*', 'r.RUTE as nama_rute')
            ->where('pc.KODECUS', $kodecus)
            ->get();

        foreach ($pricecus as $pc) {
            $pc->source = 'pricecus';
            // pc sudah punya KODECUS bawaan → aman
            $final->push($pc);
        }

        $usedCodes = $pricecus->pluck('KODE')->toArray();



        // ============================================================
        // 2️⃣ PRIORITAS PRICECUSHIS (HARUS ditambah KODECUS)
        // ============================================================

        $allCodes = DB::table('prices')->pluck('KODE')->unique();

        foreach ($allCodes as $kd) {

            if (in_array($kd, $usedCodes)) continue;

            $his = DB::table('pricecushis as ph')
                ->leftJoin('rute as r', 'ph.RUTE', '=', 'r.id')
                ->select('ph.*', 'r.RUTE as nama_rute')
                ->where('ph.KODE', $kd)
                ->where('ph.created_at', '>=', $customerCreatedAt)
                ->orderBy('ph.created_at', 'asc')
                ->first();

            if ($his) {
                $his->source = 'pricecushis';
                $his->KODECUS = $kodecus; // FIX WAJIB
                $final->push($his);
                $usedCodes[] = $kd;
            }
        }



        // ============================================================
        // 3️⃣ DEFAULT PRICES (HARUS ditambah KODECUS)
        // ============================================================

        foreach ($allCodes as $kd) {

            if (in_array($kd, $usedCodes)) continue;

            $default = DB::table('prices as p')
                ->leftJoin('rute as r', 'p.RUTE', '=', 'r.id')
                ->select('p.*', 'r.RUTE as nama_rute')
                ->where('p.KODE', $kd)
                ->first();

            if ($default) {
                $default->source = 'prices';
                $default->KODECUS = $kodecus; // FIX WAJIB
                $final->push($default);
                $usedCodes[] = $kd;
            }
        }



        // ============================================================
        // RETURN KE DATATABLES
        // ============================================================

        return DataTables::of($final)
            ->addIndexColumn()
            ->with($customerData)
            ->addColumn('jenis_text', function ($row) {
                return $row->JENIS == 1 ? 'Eceran' : 'Booking';
            })
            ->addColumn('harga_html', function ($row) {
                return '<span class="editable-price"
                    contenteditable="true"
                    data-original="'.$row->HARGA.'"
                    data-kode="'.$row->KODE.'"
                    data-kodecus="'.$row->KODECUS.'"
                    style="background:#fff7d1; padding:6px; border-radius:4px;">
                    '.$row->HARGA.'</span>';
            })
            ->addColumn('aksi', function ($row) {
                return '<button class="btn btn-success btn-sm save-price"
                    data-id="'.$row->id.'"
                    data-kode="'.$row->KODE.'"
                    data-source="'.$row->source.'"
                    data-original="'.$row->HARGA.'">
                    <i class="bx bx-save"></i></button>';
            })
            ->rawColumns(['harga_html', 'aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_val_pricecus' => 'required|string|max:50',
            'keterangan_pricecus' => 'required|string|max:50',
            'dari_pricecus' => 'required|numeric',
            'sampai_pricecus' => 'required|numeric',
            'rute_pricecus' => 'required|string|max:30',
            'harga_pricecus' => 'required|numeric',
            'jenis_valcus' => 'required|string|max:1',
            'rute_val_pricecus' => 'required'
        ]);

        //  Generate Kode
        $lastCode = Pricecus::where('KODE', 'LIKE', 'PRCK%')
                    ->orderBy('KODE', 'desc')
                    ->value('KODE');
        $number = $lastCode ? (int) substr($lastCode, 4) + 1 : 1;
        $kode = 'PRCK' . str_pad($number, 7, '0', STR_PAD_LEFT);


        $Prices = Pricecus::create([
            'KODECUS' =>  $request->kode_val_pricecus,
            'KODE' => $kode,
            'KETERANGAN' => $request->keterangan_pricecus,
            'DARI' => $request->dari_pricecus,
            'SAMPAI' => $request->sampai_pricecus,
            'RUTE' => $request->rute_val_pricecus,
            'HARGA' => $request->harga_pricecus,
            'HV' => 0,
            'HKG' => 0,
            'HBOK' => 0,
            'JENIS' => $request->jenis_valcus,
            'USER' => Auth::check() ? Auth::user()->user_id : 'System',
            'USEREDIT' => Auth::check() ? Auth::user()->user_id : 'System',
            'KUNCI' => $request->keterangan_pricecus . $request->jenis_valcus . $request->dari_pricecus . $request->sampai_pricecus,
            'HG' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $Prices
        ]);
    }

}
