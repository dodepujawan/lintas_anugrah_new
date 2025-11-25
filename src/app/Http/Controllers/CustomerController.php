<?php

namespace App\Http\Controllers;

use App\Models\Mcustomer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index_customer(){
        return view('customer.customer');
    }

    public function customer_get_data(){
        $customers = Mcustomer::select(['id', 'kode', 'nama', 'jenis_usaha', 'telepon', 'email', 'created_at']);

        return DataTables::of($customers)
            ->addIndexColumn()
            ->addColumn('action', function($customer) {
                return '
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info view-btn-customer" data-id="'.$customer->id.'" data-bs-toggle="tooltip" title="View">
                            <i class="bx bx-show"></i>
                        </button>
                        <button class="btn btn-sm btn-warning edit-btn-customer" data-id="'.$customer->id.'" data-bs-toggle="tooltip" title="Edit">
                            <i class="bx bx-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn-customer" data-id="'.$customer->id.'" data-bs-toggle="tooltip" title="Delete">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function customer_store(Request $request){
        $validator = Validator::make($request->all(), [
            'nama' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate kode
            $kode = $this->customer_kode_store();

            // Merge kode ke data request
            $data = $request->all();
            $data['kode'] = $kode;

            // Tetap bisa pakai create dengan all data
            $customer = Mcustomer::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Data customer berhasil disimpan',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function customer_show($id){
        $customer = Mcustomer::find($id);
        $customer->pemilik_tgl_lahir = $customer->pemilik_tgl_lahir?->format('Y-m-d');

        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $customer
        ]);
    }

    public function customer_update(Request $request, $id){
        // dd($request->all());
        $customer = Mcustomer::find($id);

        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode' => 'required|unique:Mcustomer,kode,'.$id,
            'nama' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Data customer berhasil diupdate',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function customer_destroy($id){
        $customer = Mcustomer::find($id);

        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        try {
            $customer->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data customer berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Fungsi Calback id_user
    public function customer_kode() {
        $role = 'CST'; // Default prefix untuk customer

        $lastUser = DB::table('mcustomer')
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
        $newKode = $role . str_pad($newNumber, 6, '0', STR_PAD_LEFT);

        return response()->json(['kode' => $newKode]);
    }

    // Fungsi Calback private
    private function customer_kode_store() {
        $role = 'CST';

        DB::beginTransaction();

        try {
            $lastUser = DB::table('mcustomer')
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

            $newKode = $role . str_pad($newNumber, 6, '0', STR_PAD_LEFT);

            DB::commit();
            return $newKode;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
