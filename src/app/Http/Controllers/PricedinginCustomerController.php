<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Pricedingincus;
use App\Models\Pricedinginhis;
use App\Models\Pricedingin;
use App\Models\Mcustomer;
use App\Models\Kendaraan;

class PricedinginCustomerController extends Controller
{
    public function index(){
        return view('priceDinginCustomer.priceDinginCustomer');
    }

    public function getData(){
        $customers = Mcustomer::select(['id', 'kode', 'nama', 'jenis_usaha', 'telepon', 'email', 'created_at']);

        return DataTables::of($customers)
            ->addIndexColumn()
            ->addColumn('action', function($customer) {
                return '
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info view-btn-customer-price-dingin" data-id="'.$customer->kode.'" data-bs-toggle="tooltip" title="View">
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
        // 1️⃣ PRIORITAS pricedingincus (punya KODECUS asli)
        // ============================================================

        $pricedingincus = DB::table('pricedingincus as pc')
            ->leftJoin('kendaraan as k', 'pc.KODE', '=', 'k.kode')
            ->select('pc.*', 'k.nama as nama_kendaraan')
            ->where('pc.KODECUS', $kodecus)
            ->get();

        foreach ($pricedingincus as $pc) {
            $pc->source = 'pricedingincus';
            // pc sudah punya KODECUS bawaan → aman
            $final->push($pc);
        }

        $usedCodes = $pricedingincus->pluck('KODE')->toArray();



        // ============================================================
        // 2️⃣ PRIORITAS PRICEDINGINHIS (HARUS ditambah KODECUS)
        // ============================================================

        $allCodes = DB::table('pricedingin')->pluck('KODE')->unique();

        foreach ($allCodes as $kd) {

            if (in_array($kd, $usedCodes)) continue;

            $his = DB::table('pricedinginhis as ph')
                ->leftJoin('kendaraan as k', 'ph.KODE', '=', 'k.kode')
                ->select('ph.*', 'k.nama as nama_kendaraan')
                ->where('ph.KODE', $kd)
                ->where('ph.created_at', '>=', $customerCreatedAt)
                ->orderBy('ph.created_at', 'asc')
                ->first();

            if ($his) {
                $his->source = 'pricedinginhis';
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

            $default = DB::table('pricedingin as p')
                ->leftJoin('kendaraan as k', 'p.KODE', '=', 'k.kode')
                ->select('p.*', 'k.nama as nama_kendaraan')
                ->where('p.KODE', $kd)
                ->first();

            if ($default) {
                $default->source = 'pricedingin';
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
            ->addColumn('harga_html', function ($row) {
                return '<span class="editable-price-dingin"
                    contenteditable="true"
                    data-original="'.$row->HARGA.'"
                    data-kode="'.$row->KODEDGN.'"
                    data-kodecus="'.$row->KODECUS.'"
                    style="background:#fff7d1; padding:6px; border-radius:4px;">
                    '.$row->HARGA.'</span>';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-success btn-sm save-price-dingin"
                    data-id="'.$row->id.'"
                    data-kode="'.$row->KODEDGN.'"
                    data-source="'.$row->source.'"
                    data-original="'.$row->HARGA.'">
                    <i class="bx bx-save"></i></button>';
            })
            ->rawColumns(['harga_html', 'action'])
            ->make(true);
    }

    public function saveCustomerRow(Request $request){
        $request->validate([
            'kode' => 'required|string',
            'harga' => 'required|numeric',
            'kodecus' => 'required|string',
        ]);

        $kode = $request->kode;
        $hargaBaru = $request->harga;
        $kodecus = $request->kodecus;

        // 1. Cek apakah kode sudah ada di pricecus
        $existing = Pricedingincus::where('KODEDGN', $kode)
                            ->where('KODECUS', $kodecus)
                            ->first();

        if ($existing) {
            // -------------------------
            //   UPDATE HARGA SAJA
            // -------------------------
            $existing->update([
                'HARGA' => $hargaBaru,
                'USEREDIT' => Auth::check() ? Auth::user()->user_id : 'System',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Harga berhasil diupdate untuk customer ini'
            ]);
        }

        // -------------------------
        //   INSERT BARU
        // -------------------------

        // Ambil data dari harga umum
        $price = Pricedingin::where('KODEDGN', $kode)->first();

        if (!$price) {
            return response()->json([
                'success' => false,
                'message' => 'Kode harga tidak ditemukan di tabel Prices'
            ]);
        }

        // Buat insert ke pricecus
        Pricedingincus::create([
            'KODECUS' => $kodecus,
            'KODEDGN' => $price->KODEDGN,
            'KODE' => $price->KODE,
            'PERIODE' => $price->PERIODE,
            'PLAT' => $price->PLAT,
            'JENIS' => $price->JENIS,
            'ITEM' => $price->ITEM,

            // Harga khusus customer
            'HARGA' => $hargaBaru,


            'USER' => Auth::check() ? Auth::user()->user_id : 'System',
            'USEREDIT' => Auth::check() ? Auth::user()->user_id : 'System',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Harga khusus customer berhasil ditambahkan'
        ]);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'KODECUS' => 'required|string|max:50',
            'KODE' => 'required|string|max:50',
            'PERIODE' => 'required|string|max:50',
            'PLAT' => 'required|string|max:20',
            'ITEM' => 'required|string|max:255',
            'HARGA' => 'required|integer|min:0',
            'USER' => 'required|string|max:100',
        ]);

        // Ambil data kendaraan
        $kode = $request->input('KODE');
        $kendaraan = Kendaraan::where('kode', $kode)->first();

        // Kalau kendaraan ada, tambahkan kolom jenis ke validated
        if ($kendaraan) {
            $validated['JENIS'] = $kendaraan->jenis;
        } else {
            // Optional: kalau mau error kalau kendaraan tidak ditemukan
            return response()->json([
                'success' => false,
                'message' => 'Kendaraan tidak ditemukan',
            ], 404);
        }

        // Generate KODE
        $lastCode = Pricedingincus::orderBy('id', 'desc')->value('KODEDGN');
        $number = $lastCode ? (int) substr($lastCode, 4) + 1 : 1;
        $kodedgn = 'PRDK' . str_pad($number, 7, '0', STR_PAD_LEFT);
        $validated['KODEDGN'] = $kodedgn;

        // Simpan
        $pricedingin = Pricedingincus::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $pricedingin
        ]);
    }
}
