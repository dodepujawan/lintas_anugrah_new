<?php

namespace App\Http\Controllers;

use App\Models\Mcustomer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index_customer(){
        return view('customer.customer');
    }

    public function customer_get_data(){
        $customers = Mcustomer::select([
            'id',
            'kode_cus',
            'NAMACUST',
            'ALAMAT1',
            'TELEPON',
            'EMAIL',
            'created_at'
        ]);

        return DataTables::of($customers)
            ->addIndexColumn()
            ->addColumn('action', function ($customer) {
                return '
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info view-btn-customer" data-id="'.$customer->id.'">
                            <i class="bx bx-show"></i>
                        </button>
                        <button class="btn btn-sm btn-warning edit-btn-customer" data-id="'.$customer->id.'">
                            <i class="bx bx-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn-customer" data-id="'.$customer->id.'">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function customer_store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
        ]);
        try {
            $kode_cus = $this->customer_kode_store();
            Mcustomer::create([
                // mapping modern → legacy
                'kode_cus'   => $kode_cus,
                'CUSTOMER'   => $request->jenis_usaha, // atau generator FoxPro
                'NAMACUST'   => $request->nama,
                'ALAMAT1'    => $request->alamat,
                'KOTA'       => $request->kota ?? '',
                'TELEPON'    => $request->telepon,
                'FAX'        => $request->fax,
                'EMAIL'      => $request->email,
                'KONTAK'     => $request->kontak,
                'NPWP'       => $request->npwp,

                'TOPKREDIT'  => $request->top_kredit ?? 0,

                // wilayah
                'desa'       => $request->desa,
                'camat'      => $request->kecamatan,
                'kabupaten'  => $request->kabupaten,

                // purchasing
                'namapur'    => $request->purchasing_nama,
                'em_pur'     => $request->purchasing_email,
                'hp_pur'     => $request->purchasing_extensi_hp,

                // pajak
                'NM_PAJAK'   => $request->data_pajak_nama,
                'NP_PAJAK'   => $request->data_pajak_npwp,
                'AL_PAJAK'   => $request->data_pajak_alamat,
                'AL_PAJAK2'  => $request->data_pajak_alamat2,

                // pemilik
                'nama_p'     => $request->pemilik_nama,
                'ktp_p'      => $request->pemilik_no_ktp_sim,
                'tempat_l'   => $request->pemilik_tempat_lahir,
                'tgll_p'     => $request->pemilik_tgl_lahir,
                'alamat_p'   => $request->pemilik_alamat_rumah,
                'desa_p'     => $request->pemilik_desa,
                'camat_p'    => $request->pemilik_kecamatan,
                'kab_p'      => $request->pemilik_kabupaten,
                'tlp_p'      => $request->pemilik_telepon,
                'fax_p'      => $request->pemilik_fax,
                'email_p'    => $request->pemilik_email,
                'npwp_p'     => $request->pemilik_npwp,
                'agama_p'    => $request->pemilik_agama,

                // kontak lain
                'kontak_l'   => $request->kontak_lain_nama,
                'tlp_kl'     => $request->kontak_lain_telepon,

                // accounting
                'nama_ac'    => $request->accounting_nama,
                'em_ac'      => $request->accounting_email,
                'hp_ac'      => $request->accounting_extensi_hp,
                'TGL_UPDATE' => Carbon::now(),
            ]);

            return response()->json([
                'status' => 'success',
                'success' => 'Data customer berhasil disimpan',
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

        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Format tanggal
        // $pemilik_tgl_lahir = $customer->tgll_p ? $customer->tgll_p->format('Y-m-d') : null;

        // Mapping ke nama field frontend
        $data = [
            'kode' => $customer->kode_cus,
            'nama' => $customer->NAMACUST,
            'jenis_usaha' => $customer->CUSTOMER ?? '-',
            'telepon' => $customer->TELEPON,
            'email' => $customer->EMAIL,

            // Info pemilik
            'pemilik_nama' => $customer->nama_p,
            'pemilik_no_ktp_sim' => $customer->ktp_p,
            'pemilik_email' => $customer->email_p,
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function customer_update(Request $request, $id){
        $customer = Mcustomer::find($id);

        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            // 'kode' optional kalau pakai generator FoxPro
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Mapping fields sama seperti store
            $customer->update([
                'CUSTOMER'   => $request->jenis_usaha,
                'NAMACUST'   => $request->nama,
                'ALAMAT1'    => $request->alamat ?? '',
                'KOTA'       => $request->kota ?? '',
                'TELEPON'    => $request->telepon ?? '',
                'FAX'        => $request->fax ?? '',
                'EMAIL'      => $request->email ?? '',
                'KONTAK'     => $request->kontak ?? '',
                'NPWP'       => $request->npwp ?? '',

                'TOPKREDIT'  => $request->top_kredit ?? 0,

                // wilayah
                'desa'       => $request->desa ?? '',
                'camat'      => $request->kecamatan ?? '',
                'kabupaten'  => $request->kabupaten ?? '',

                // purchasing
                'namapur'    => $request->purchasing_nama ?? '',
                'em_pur'     => $request->purchasing_email ?? '',
                'hp_pur'     => $request->purchasing_extensi_hp ?? '',

                // pajak
                'NM_PAJAK'   => $request->data_pajak_nama ?? '',
                'NP_PAJAK'   => $request->data_pajak_npwp ?? '',
                'AL_PAJAK'   => $request->data_pajak_alamat ?? '',
                'AL_PAJAK2'  => $request->data_pajak_alamat2 ?? '',

                // pemilik
                'nama_p'     => $request->pemilik_nama ?? '',
                'ktp_p'      => $request->pemilik_no_ktp_sim ?? '',
                'tempat_l'   => $request->pemilik_tempat_lahir ?? '',
                'tgll_p'     => $request->pemilik_tgl_lahir ?? null,
                'alamat_p'   => $request->pemilik_alamat_rumah ?? '',
                'desa_p'     => $request->pemilik_desa ?? '',
                'camat_p'    => $request->pemilik_kecamatan ?? '',
                'kab_p'      => $request->pemilik_kabupaten ?? '',
                'tlp_p'      => $request->pemilik_telepon ?? '',
                'fax_p'      => $request->pemilik_fax ?? '',
                'email_p'    => $request->pemilik_email ?? '',
                'npwp_p'     => $request->pemilik_npwp ?? '',
                'agama_p'    => $request->pemilik_agama ?? '',

                // kontak lain
                'kontak_l'   => $request->kontak_lain_nama ?? '',
                'tlp_kl'     => $request->kontak_lain_telepon ?? '',

                // accounting
                'nama_ac'    => $request->accounting_nama ?? '',
                'em_ac'      => $request->accounting_email ?? '',
                'hp_ac'      => $request->accounting_extensi_hp ?? '',
            ]);

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
            ->where('kode_cus', 'LIKE', $role . '%')
            ->orderBy('kode_cus', 'desc')
            ->first();

        if ($lastUser) {
            // Ambil angka dari kode terakhir, contoh: CST000001 -> 000001 -> 1
            $lastNumber = (int) substr($lastUser->kode_cus, strlen($role));
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
                ->where('kode_cus', 'LIKE', $role . '%')
                ->lockForUpdate() // 🔒 Lock table untuk prevent race condition
                ->orderBy('kode_cus', 'desc')
                ->first();

            if ($lastUser) {
                $lastNumber = (int) substr($lastUser->kode_cus, strlen($role));
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

// public function customer_storexxx(Request $request){
//         $validator = Validator::make($request->all(), [
//             'nama' => 'required'
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Validasi gagal',
//                 'errors' => $validator->errors()
//             ], 422);
//         }

//         try {
//             // Generate kode
//             $kode = $this->customer_kode_store();

//             // Merge kode ke data request
//             $data = $request->all();
//             $data['kode'] = $kode;

//             // Tetap bisa pakai create dengan all data
//             $customer = Mcustomer::create($data);

//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'Data customer berhasil disimpan',
//                 'data' => $customer
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Terjadi kesalahan: ' . $e->getMessage()
//             ], 500);
//         }
//     }
