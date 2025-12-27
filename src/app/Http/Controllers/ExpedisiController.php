<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Expedisi;
use App\Models\Mcustomer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ExpedisiController extends Controller
{
    public function index()
    {
        return view('expedisi.expedisi');
    }

    public function getDataCustomer(){
        $customers = Mcustomer::select(['id', 'kode_cus', 'NAMACUST', 'TYPECUST', 'TELEPON', 'EMAIL', 'created_at']);

        return DataTables::of($customers)
            ->addIndexColumn()
            ->addColumn('action', function($customer) {
                return '
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info view-btn-customer-expedisi" data-id="'.$customer->kode_cus.'" data-name="'.$customer->NAMACUST.'" data-bs-toggle="tooltip" title="View">
                            <i class="bx bx-check"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request){
        // Validasi
        $validator = Validator::make($request->all(), [
            'TGLMUAT' => 'required|date',
            'CUSTOMER' => 'required|string|max:30',
            'rute' => 'required|string|max:30',
            'JUMLAH' => 'required|numeric|min:0',
            'HARGA' => 'required|numeric|min:0',
        ], [
            'TGLMUAT.required' => 'Tanggal muat harus diisi',
            'CUSTOMER.required' => 'Customer harus dipilih',
            'rute.required' => 'Rute harus diisi',
            'JUMLAH.required' => 'Jumlah harus diisi',
            'HARGA.required' => 'Harga harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Mulai transaction dengan isolasi tinggi
        DB::beginTransaction();

        try {
            // ======================
            // GENERATE NOMUAT DENGAN LOCK
            // ======================
            $nomuat = $this->generateNomuatWithLock();

            // ======================
            // HITUNG TOTAL DARI REQUEST
            // ======================
            $jumlah = $request->JUMLAH ?? 0;
            $harga = $request->HARGA ?? 0;
            $discPercent = $request->DISC ?? 0;
            $delCharge = $request->DC ?? 0;
            $ppnPercent = floatval($request->PPN);
            $subTotal = $jumlah * $harga;
            $discAmount = $subTotal * ($discPercent / 100);
            $totalAfterDisc = $subTotal - $discAmount;
            $ppn = $totalAfterDisc * ($ppnPercent / 100);
            $grandTotal = $totalAfterDisc + $ppn + $delCharge;

            // ======================
            // PREPARE DATA
            // ======================
            $expedisiData = [
                // IDENTITAS & DOKUMEN
                'NOMUAT' => $nomuat,
                'TGLMUAT' => $request->TGLMUAT,
                'NOJALAN' => $request->NOJALAN,
                'WILAYAH' => $request->WILAYAH ?? 'denpasar',
                'CUSTOMER' => $request->CUSTOMER,

                // KENDARAAN & DRIVER
                'KENDARAAN' => $request->kendaraan_expedisi_id,
                'NAMA_KENDARAAN' => $request->NAMA_KENDARAAN,
                'tglsj' => $request->tglsj,
                'NOSJ' => $request->NOSJ,
                'DRIVER' => $request->driver_1_expedisi_id,
                'NAMA_DRIVER' => $request->NAMA_DRIVER,
                'DRIVER2' => $request->driver_2_expedisi_id,
                'NAMA_DRIVER2' => $request->NAMA_DRIVER2,

                // PENERIMA
                'P_PENERIMA' => $request->P_PENERIMA,
                'P_NAMA' => $request->P_NAMA,
                'P_PHONE' => $request->P_PHONE,
                'P_ALAMAT' => $request->P_ALAMAT,

                // DETAIL & PERHITUNGAN
                'rute' => $request->rute,
                'JUMLAH' => $jumlah,
                'UNIT' => $request->UNIT ?? 'KG',
                'HARGA' => $harga,
                'hargaaw' => $request->hargaaw ?? $harga,
                'DISC' => $discPercent,
                'DC' => $delCharge,
                'DCAW' => $request->DCAW ?? $delCharge,
                'NDISC' => $discAmount,
                'NDISCAW' => $discAmount,
                'TOTAL' => $totalAfterDisc,
                'PPN' => 11,
                'GRAND' => $grandTotal,

                // INVOICE
                // 'INVOICE' => $invoice,
                // 'TGLINVOICE' => $request->TGLMUAT,

                // STATUS & DEFAULT
                'JENISHRG' => $request->JENISHRG ?? '1',
                'JENIS' => $request->JENIS ?? 'EKS',
                'STS' => $request->STS ?? 'INVOICE',
                'SIMPAN' => $request->SIMPAN ?? 'N',
                'READY' => $request->READY ?? 'Y',
                'CLOSSING' => $request->CLOSSING ?? 'N',
                'KETERANGAN' => $request->KETERANGAN ?? 'EXPEDISI BARU',

                // USER INFO
                'user' => auth()->user()->name ?? 'SYSTEM',
                // 'USERINV' => auth()->user()->name ?? 'SYSTEM',
                // 'USERKENDARAAN' => auth()->user()->name ?? 'SYSTEM',

                'created_at' => now(),
                'updated_at' => now(),
            ];

            // ======================
            // SIMPAN KE DATABASE
            // ======================
            $expedisi = Expedisi::create($expedisiData);

            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data ekspedisi berhasil disimpan',
                'data' => [
                    'id' => $expedisi->id,
                    'NOMUAT' => $expedisi->NOMUAT,
                    'NOSJ' => $expedisi->NOSJ,
                    'GRAND' => number_format($expedisi->GRAND, 0, ',', '.')
                ],
                'nomuat' => $expedisi->NOMUAT // Kirim ke frontend untuk update field
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error simpan expedisi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data. Silahkan coba lagi.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function getData(Request $request){
        $expedisi = Expedisi::select([
            'id',
            'NOMUAT',
            'TGLMUAT',
            'CUSTOMER',
            'rute',
            'JUMLAH',
            'UNIT',
            'HARGA',
            'DISC',
            'DC',
            'GRAND',
            'NOSJ',
            'KENDARAAN',
            'NAMA_KENDARAAN',
            'DRIVER',
            'NAMA_DRIVER',
            'STS',
            'created_at'
        ]);

        // Filter tanggal mulai
        if ($request->has('tgl_mulai') && !empty($request->tgl_mulai)) {
            $expedisi->whereDate('TGLMUAT', '>=', $request->tgl_mulai);
        }

        // Filter tanggal akhir
        if ($request->has('tgl_akhir') && !empty($request->tgl_akhir)) {
            $expedisi->whereDate('TGLMUAT', '<=', $request->tgl_akhir);
        }

        // Filter search (NO MUAT, CUSTOMER, RUTE)
        if ($request->has('search_muat') && !empty($request->search_muat)) {
            $search = $request->search_muat;
            $expedisi->where(function($query) use ($search) {
                $query->where('NOMUAT', 'like', '%' . $search . '%')
                    ->orWhere('CUSTOMER', 'like', '%' . $search . '%')
                    ->orWhere('rute', 'like', '%' . $search . '%')
                    ->orWhere('NOSJ', 'like', '%' . $search . '%');
            });
        }

        return DataTables::of($expedisi)
            ->addIndexColumn()
            ->addColumn('action', function($row) {
                $btn = '<div class="d-flex gap-2">'; // Gap lebih besar
                $btn .= '<button type="button" class="btn btn-sm btn-outline-primary px-3 py-1 pickMuat"
                            data-id="'.$row->id.'" data-nomuat="'.$row->NOMUAT.'" title="Pilih">
                            <i class="bx bx-check" style="font-size: 14px;"></i>
                        </button>';
                $btn .= '<button type="button" class="btn btn-sm btn-outline-danger px-3 py-1 deleteMuat"
                            data-id="'.$row->id.'" data-nomuat="'.$row->NOMUAT.'" title="Hapus">
                            <i class="bx bx-trash" style="font-size: 14px;"></i>
                        </button>';
                $btn .= '</div>';
                return $btn;
            })
            ->addColumn('total_formatted', function($row) {
                return 'Rp ' . number_format($row->GRAND, 0, ',', '.');
            })
            ->addColumn('harga_formatted', function($row) {
                return 'Rp ' . number_format($row->HARGA, 0, ',', '.');
            })
            ->addColumn('dc_formatted', function($row) {
                return 'Rp ' . number_format($row->DC, 0, ',', '.');
            })
            ->editColumn('TGLMUAT', function($row) {
                return $row->TGLMUAT ? date('d-m-Y', strtotime($row->TGLMUAT)) : '-';
            })
            ->editColumn('JUMLAH', function($row) {
                return number_format($row->JUMLAH, 0, ',', '.') . ' ' . $row->UNIT;
            })
            ->editColumn('DISC', function($row) {
                return $row->DISC ? $row->DISC . '%' : '-';
            })
            ->rawColumns(['action', 'total_formatted', 'harga_formatted', 'dc_formatted'])
            ->make(true);
    }

    public function destroy($id){
        $data = Expedisi::find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $data->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }

    private function generateNomuatWithLock(){
        $currentYear = date('Y');
        $currentMonth = date('m');
        $prefix = 'MU' . $currentYear . $currentMonth;

        // Gunakan lockForUpdate untuk mencegah race condition
        $last = Expedisi::where('NOMUAT', 'like', $prefix . '%')
            ->lockForUpdate() // Penting: lock row
            ->orderBy('NOMUAT', 'desc')
            ->first();

        if ($last) {
            $lastNumber = intval(substr($last->NOMUAT, -5));
            $increment = $lastNumber + 1;
        } else {
            $increment = 1;
        }

        return $prefix . str_pad($increment, 5, '0', STR_PAD_LEFT);
    }
}
