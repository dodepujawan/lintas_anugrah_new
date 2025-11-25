<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DriverController extends Controller
{
    public function index()
    {
        return view('driver.driver');
    }

    public function data()
    {
        $driver = Driver::select('*');

        return DataTables::of($driver)
            ->addIndexColumn()
            ->addColumn('action', function($driver) {
                return '
                <div class="btn-group">
                    <button class="btn btn-sm btn-warning edit edit-driver" data-id="'.$driver->id.'"><i class="bx bx-edit"></i></button>
                    <button class="btn btn-sm btn-danger delete delete-driver" data-id="'.$driver->id.'"><i class="bx bx-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|max:20|unique:driver,kode',
            'nama' => 'required|max:100',
            'alamat' => 'required|max:200', // Biasanya alamat lebih panjang
            'phone' => 'required|min:5|numeric', // numeric bukan number
            'mulai_kerja' => 'required|date',
        ]);

        Driver::create($request->all());

        return response()->json(['success' => 'Data berhasil disimpan!']);
    }

    public function edit($id)
    {
        $driver = Driver::findOrFail($id);
        return response()->json($driver);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|max:20|unique:driver,kode,'.$id,
            'nama' => 'required|max:100',
            'alamat' => 'required|max:200', // Biasanya alamat lebih panjang
            'phone' => 'required|min:5|numeric', // numeric bukan number
            'mulai_kerja' => 'required|date',
        ]);

        $driver = Driver::findOrFail($id);
        $driver->update($request->all());

        return response()->json(['success' => 'Data berhasil diupdate!']);
    }

    public function destroy($id)
    {
        Driver::destroy($id);
        return response()->json(['success' => 'Data berhasil dihapus!']);
    }

    // Fungsi Calback id_user
    public function driver_kode() {
        $role = 'DRV'; // Default prefix untuk customer

        $lastUser = DB::table('driver')
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
}
