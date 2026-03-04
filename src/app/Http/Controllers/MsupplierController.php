<?php

namespace App\Http\Controllers;

use App\Models\Msupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MsupplierController extends Controller
{
    public function index_supplier(){
        return view('supplier.supplier');
    }

    public function store(Request $request){
        try {

            DB::beginTransaction();

            $kategori = $request->kategori_supplier;

            $last = Msupplier::where('KATEGORI', $kategori)
                ->orderBy('SUPPLIER', 'desc')
                ->lockForUpdate()
                ->first();

            if ($last) {
                $lastNumber = (int) substr($last->SUPPLIER, 3);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            $kode = $kategori . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

            Msupplier::create([
                'SUPPLIER' => $kode,
                'KATEGORI' => $kategori,
                'NAMA' => $request->nama_supplier,
                'ALAMAT1' => $request->alamat1_supplier,
                'ALAMAT2' => $request->alamat2_supplier,
                'KOTA' => $request->kota_supplier,
                'TELEPON' => $request->telepon_supplier,
                'FAX' => $request->fax_supplier,
                'EMAIL' => $request->email_supplier,
                'KONTAK' => $request->kontak_supplier,
                'NOREK' => $request->norek_supplier,
                'BANK' => $request->bank_supplier,
                'ATASNAMA' => $request->atasnama_supplier,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan',
                'kode' => $kode
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
