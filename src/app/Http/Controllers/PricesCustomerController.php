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
        // Ambil data customer berdasar kode
        $customer = MCustomer::where('kode', $kodecus)->first();

        if (!$customer) {
            return DataTables::of(collect([]))->make(true);
            return response()->json([
                'customer_kode' => null,
                'customer_nama' => null,
                'data' => []
            ]);
        }

        $customerCreatedAt = $customer->created_at;

        $final = collect(); // hasil akhir

        // ============================
        // 1️⃣ PRICECUS (PRIORITAS 1)
        // ============================
        $pricecus = Pricecus::where('KODECUS', $kodecus)->get();

        foreach ($pricecus as $pc) {
            $pc->source = 'pricecus'; // beri tag sumber
            $final->push($pc);
        }

        // List kode yang sudah dipakai
        $usedCodes = $pricecus->pluck('KODE')->toArray();


        // ============================
        // 2️⃣ PRICECUSHIS (PRIORITAS 2)
        // ============================
        $allCodes = Prices::pluck('KODE')->unique(); // semua kode master harga

        foreach ($allCodes as $kd) {

            // Skip jika kode sudah ada di pricecus
            if (in_array($kd, $usedCodes)) continue;

            // Cari history setelah tanggal daftar customer
            $his = Pricecushis::where('KODE', $kd)
                ->where('created_at', '>=', $customerCreatedAt)
                ->orderBy('created_at', 'asc') // ambil yg paling dekat setelah daftar
                ->first();

            if ($his) {
                $his->source = 'pricecushis';
                $final->push($his);

                // tandai kode sudah diambil
                $usedCodes[] = $kd;
            }
        }


        // ============================
        // 3️⃣ PRICES (PRIORITAS 3 — DEFAULT)
        // ============================
        foreach ($allCodes as $kd) {

            // Skip jika sudah muncul di pricecus / pricecushis
            if (in_array($kd, $usedCodes)) continue;

            $default = Prices::where('KODE', $kd)->first();
            if ($default) {
                $default->source = 'prices';
                $final->push($default);

                $usedCodes[] = $kd;
            }
        }


        // ======================================
        // RETURN TO DATATABLES (YAJRA)
        // ======================================

        return DataTables::of($final)
            ->addIndexColumn()
            ->with([
                'customer_kode' => $customer->kode,
                'customer_nama' => $customer->nama,
                'jenis_usaha' => $customer->jenis_usaha,
                'alamat' => $customer->alamat,
                'pemilik_nama' => $customer->pemilik_nama,
            ])
            ->addColumn('jenis_text', function($row) {
                return $row->JENIS == 1 ? 'Eceran' : 'Booking';
            })
            ->addColumn('harga_html', function($row) {
                return '<span class="editable-price"
                        contenteditable="true"
                        data-original="'.$row->HARGA.'"
                        data-kode="'.$row->KODE.'"
                        data-kodecus="'.$row->KODECUS.'"
                        style="background:#fff7d1; padding:6px; border-radius:4px;">
                        '.$row->HARGA.'</span>';
            })
            ->addColumn('aksi', function($row) {
                return '<button class="btn btn-success btn-sm save-price"
                        data-id="'.$row->id.'"
                        data-kode="'.$row->KODE.'"
                        data-source="'.$row->source.'"
                        data-original="'.$row->HARGA.'">
                        <i class="bx bx-save"></i></button>';
            })
            ->rawColumns(['harga_html','aksi'])
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
        $existing = Pricecus::where('KODE', $kode)
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
        $price = Prices::where('KODE', $kode)->first();

        if (!$price) {
            return response()->json([
                'success' => false,
                'message' => 'Kode harga tidak ditemukan di tabel Prices'
            ]);
        }

        // Buat insert ke pricecus
        Pricecus::create([
            'KODECUS' => $kodecus,
            'KODE' => $price->KODE,
            'KETERANGAN' => $price->KETERANGAN,
            'DARI' => $price->DARI,
            'SAMPAI' => $price->SAMPAI,
            'RUTE' => $price->RUTE,

            // Harga khusus customer
            'HARGA' => $hargaBaru,

            'HV' => $price->HV,
            'HKG' => $price->HKG,
            'HBOK' => $price->HBOK,
            'JENIS' => $price->JENIS,
            'KUNCI' => $price->KUNCI,
            'HG' => $price->HG,

            'USER' => Auth::check() ? Auth::user()->user_id : 'System',
            'USEREDIT' => Auth::check() ? Auth::user()->user_id : 'System',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Harga khusus customer berhasil ditambahkan'
        ]);
    }

}
