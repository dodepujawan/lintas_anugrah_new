<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Coolroom;
use App\Models\Arh;
use App\Models\Kwitansi;
use App\Models\Mcustomer;

use Illuminate\Support\Facades\DB;

class CoolroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('coolroom.coolroom');
    }

    /**
     * Store transaksi coolroom.
     */
    public function store(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                // ======================
                // PARSING
                // ======================
                $jumlah = (float) $request->jumlah;

                $harga = (int) preg_replace(
                    '/[^0-9]/',
                    '',
                    $request->harga
                );

                $discPersen = (float) $request->disc;

                $ppnPersen = (float) $request->ppn;

                $boxing = $request->boxing ? true : false;

                // ======================
                // SUBTOTAL
                // ======================
                if ($boxing) {

                    // harga flat
                    $subtotal = $harga;

                } else {

                    // qty x harga
                    $subtotal = $jumlah * $harga;

                }

                // ======================
                // DISCOUNT
                // ======================
                $ndisc = ($subtotal * $discPersen) / 100;

                // ======================
                // DPP
                // ======================
                $dpp = $subtotal - $ndisc;

                // ======================
                // PPN
                // ======================
                $nppn = ($dpp * $ppnPersen) / 100;

                // ======================
                // GRAND TOTAL
                // ======================
                $grand = $dpp + $nppn;

                // ======================
                // SIMPAN
                // ======================
                Coolroom::create([

                    'CUSTOMER_KODE' => $request->customer_kode,
                    'CUSTOMER'      => $request->customer,

                    'TGL'           => now(),

                    'JUMLAH'        => $jumlah,
                    'UNIT'          => 'KG',

                    'HARGA'         => $harga,

                    'BOXING'        => $boxing,

                    'SUBTOTAL'      => $subtotal,

                    'DISC'          => $discPersen,
                    'NDISC'         => $ndisc,

                    'DPP'           => $dpp,

                    'PPN'           => $ppnPersen,
                    'NPPN'          => $nppn,

                    'TOTAL'         => $dpp,
                    'GRAND'         => $grand,

                    'KETERANGAN'    => $request->keterangan,

                    'STS'           => 'OPEN',

                    'USERINPUT'     => auth()->user()->user_id,

                    'CABANG'        => auth()->user()->cabang ?? null,

                ]);

            });

            return response()->json([

                'status' => true,
                'message' => 'Transaksi coolroom berhasil disimpan'

            ]);

        } catch (\Throwable $e) {

            return response()->json([

                'status' => false,
                'message' => $e->getMessage()

            ], 500);

        }
    }
}
