<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class KendaraanController extends Controller
{
    public function index()
    {
        return view('kendaraan.kendaraan');
    }

    public function data()
    {
        $kendaraan = Kendaraan::select('*')
        ->orderBy('id', 'desc');;

        return DataTables::of($kendaraan)
            ->addIndexColumn()
            ->addColumn('action', function($kendaraan) {
                return '
                <div class="btn-group">
                    <button class="btn btn-sm btn-warning edit" data-id="'.$kendaraan->id.'"><i class="bx bx-edit"></i></button>
                    <button class="btn btn-sm btn-danger delete" data-id="'.$kendaraan->id.'"><i class="bx bx-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'plat' => 'required|max:50',
            'jenis' => 'required|max:50',
            'fno_prk_b' => 'required|max:20',
            'fno_prk_p' => 'required|max:20',
            'fno_prk_s' => 'required|max:20',
            'fno_prk_o' => 'required|max:20',
            'fno_prk_m' => 'required|max:20',
        ]);

        try {
            // Generate kode
            $kode = $this->kendaraan_kode_store();

            // Merge kode ke data request
            $data = $request->all();
            $data['kode'] = $kode;

            // Tetap bisa pakai create dengan all data
            $kendaraan = Kendaraan::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Data kendaraan berhasil disimpan',
                'data' => $kendaraan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        return response()->json($kendaraan);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|max:20|unique:kendaraan,kode,'.$id,
            'nama' => 'required|max:100',
            'plat' => 'required|max:50',
            'jenis' => 'required|max:50',
            'fno_prk_b' => 'required|max:20',
            'fno_prk_p' => 'required|max:20',
            'fno_prk_s' => 'required|max:20',
            'fno_prk_o' => 'required|max:20',
            'fno_prk_m' => 'required|max:20',
        ]);
        try {
            $kendaraan = Kendaraan::findOrFail($id);
            $kendaraan->update($request->all());
            return response()->json(['success' => 'Data berhasil diupdate!']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        Kendaraan::destroy($id);
        return response()->json(['success' => 'Data berhasil dihapus!']);
    }

    // Fungsi Calback
    public function kendaraan_kode() {
        $role = 'LML'; // Default prefix untuk customer

        $lastUser = DB::table('kendaraan')
            ->where('kode', 'LIKE', $role . '%')
            ->orderBy('kode', 'desc')
            ->first();

        if ($lastUser) {
            // Ambil angka dari kode terakhir, contoh: CST000001 -> 000001 -> 1
            $lastNumber = (int) substr($lastUser->kode, strlen($role));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format: CST + 6 digit angka (contoh: CST000001, CST000002, dst)
        $newKode = $role . str_pad($newNumber, 5, '0', STR_PAD_LEFT);

        return response()->json(['kode' => $newKode]);
    }

     // Fungsi Calback private
    private function kendaraan_kode_store() {
        $role = 'LML';

        DB::beginTransaction();

        try {
            $lastUser = DB::table('kendaraan')
                ->where('kode', 'LIKE', $role . '%')
                ->lockForUpdate() // 🔒 Lock table untuk prevent race condition
                ->orderBy('kode', 'desc')
                ->first();

            if ($lastUser) {
                $lastNumber = (int) substr($lastUser->kode, strlen($role));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            $newKode = $role . str_pad($newNumber, 5, '0', STR_PAD_LEFT);

            DB::commit();
            return $newKode;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function dataModel()
    {
        $kendaraan = Kendaraan::select('*')
        ->orderBy('id', 'desc');;

        return DataTables::of($kendaraan)
            ->addIndexColumn()
            ->addColumn('action', function($kendaraan) {
                return '
                <div class="btn-group">
                    <button class="btn btn-sm btn-primary edit" data-id="'.$kendaraan->id.'" id="pickKendaraanDingin"><i class="bx bx-check"></i></button>
                </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
