<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KendaraanController extends Controller
{
    public function index()
    {
        return view('kendaraan.kendaraan');
    }

    public function data()
    {
        $kendaraan = Kendaraan::select('*');

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
            'kode' => 'required|max:20|unique:kendaraan,kode',
            'nama' => 'required|max:100',
            'plat' => 'required|max:50',
            'jens' => 'required|max:50',
            'fno_prk_b' => 'required|max:20',
            'fno_prk_p' => 'required|max:20',
            'fno_prk_s' => 'required|max:20',
            'fno_prk_o' => 'required|max:20',
            'fno_prk_m' => 'required|max:20',
        ]);

        Kendaraan::create($request->all());

        return response()->json(['success' => 'Data berhasil disimpan!']);
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
            'jens' => 'required|max:50',
            'fno_prk_b' => 'required|max:20',
            'fno_prk_p' => 'required|max:20',
            'fno_prk_s' => 'required|max:20',
            'fno_prk_o' => 'required|max:20',
            'fno_prk_m' => 'required|max:20',
        ]);

        $kendaraan = Kendaraan::findOrFail($id);
        $kendaraan->update($request->all());

        return response()->json(['success' => 'Data berhasil diupdate!']);
    }

    public function destroy($id)
    {
        Kendaraan::destroy($id);
        return response()->json(['success' => 'Data berhasil dihapus!']);
    }
}
