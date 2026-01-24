<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Expedisi;
use App\Models\Mcustomer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

class RentPendinginController extends Controller
{
    public function index()
    {
        return view('rentPendingin.rentPendingin');
    }

    public function getDataMuat(Request $request){
        // 🔹 Subquery: ambil BARIS PERTAMA per NOMUAT
        $subQuery = DB::table('expedisi')
            ->select(DB::raw('MIN(id) as id'))
            ->where('JENIS', 'REN')
            ->whereNotNull('NOMUAT')
            ->groupBy('NOMUAT');

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
        ])->whereNotNull('NOMUAT')
        ->whereIn('id', $subQuery)
        ->orderBy('id', 'desc');

        // 🔐 FILTER ROLE DRIVER
        if (auth()->user()->roles === 'driver') {
            $expedisi->where('user_id', auth()->user()->user_id);
        }

        // 📅 FILTER TANGGAL MULAI
        if ($request->filled('tgl_mulai')) {
            $expedisi->whereDate('TGLMUAT', '>=', $request->tgl_mulai);
        }

        // 📅 FILTER TANGGAL AKHIR
        if ($request->filled('tgl_akhir')) {
            $expedisi->whereDate('TGLMUAT', '<=', $request->tgl_akhir);
        }

        // 🔍 SEARCH
        if ($request->filled('search_muat')) {
            $search = $request->search_muat;
            $expedisi->where(function ($q) use ($search) {
                $q->where('NOMUAT', 'like', "%{$search}%")
                ->orWhere('CUSTOMER', 'like', "%{$search}%")
                ->orWhere('rute', 'like', "%{$search}%")
                ->orWhere('NOSJ', 'like', "%{$search}%");
            });
        }

        return DataTables::of($expedisi)
            ->addIndexColumn()

            // 🔘 ACTION BUTTON
            ->addColumn('action', function ($row) {
                $btn = '<div class="d-flex gap-2">'; // Gap lebih besar
                $btn .= '<button type="button" class="btn btn-sm btn-outline-primary px-3 py-1 pickRentDgn"
                            data-id="'.$row->id.'"
                            data-nomuat="'.$row->NOMUAT.'"
                            title="Pilih">
                            <i class="bx bx-check" style="font-size: 14px;"></i>
                        </button>';
                $btn .= '<button type="button" class="btn btn-sm btn-outline-danger px-3 py-1 deleteRentDgn"
                            data-id="'.$row->id.'"
                            data-nomuat="'.$row->NOMUAT.'"
                            title="Hapus">
                            <i class="bx bx-trash" style="font-size: 14px;"></i>
                        </button>';
                $btn .= '</div>';
                return $btn;
            })

            // 💰 FORMAT HARGA
            ->addColumn('harga_formatted', function ($row) {
                return 'Rp ' . number_format($row->HARGA ?? 0, 0, ',', '.');
            })

            ->addColumn('dc_formatted', function ($row) {
                return 'Rp ' . number_format($row->DC ?? 0, 0, ',', '.');
            })

            ->addColumn('total_formatted', function ($row) {
                return 'Rp ' . number_format($row->GRAND ?? 0, 0, ',', '.');
            })

            // ✏️ EDIT KOLOM
            ->editColumn('TGLMUAT', function ($row) {
                return $row->TGLMUAT
                    ? date('d-m-Y', strtotime($row->TGLMUAT))
                    : '-';
            })

            ->editColumn('JUMLAH', function ($row) {
                return number_format($row->JUMLAH ?? 0, 0, ',', '.') . ' ' . ($row->UNIT ?? '');
            })

            ->editColumn('DISC', function ($row) {
                return $row->DISC ? $row->DISC . '%' : '-';
            })

            ->rawColumns(['action'])
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

    public function getDataSurjal(Request $request){
        $expedisi = Expedisi::select([
            'id',
            'NOSJ',
            'tglsj',
            'CUSTOMER',
            'rute',
            'JUMLAH',
            'UNIT',
            'HARGA',
            'DISC',
            'DC',
            'GRAND',
            'KENDARAAN',
            'NAMA_KENDARAAN',
            'DRIVER',
            'NAMA_DRIVER',
            'STS',
            'JENISHRG',
            'created_at'
        ])->where('JENIS', 'REN')
        ->where(function($query) {
            $query->where('NOMUAT', '')
                    ->orWhereNull('NOMUAT');
        })
        ->orderBy('created_at', 'desc'); ;

        // 🔐 FILTER ROLE DRIVER
        if (auth()->user()->roles === 'driver') {
            $expedisi->where('user_id', auth()->user()->user_id);
        }

        // Filter tanggal mulai
        if ($request->has('tgl_mulai') && !empty($request->tgl_mulai)) {
            $expedisi->whereDate('tglsj', '>=', $request->tgl_mulai);
        }

        // Filter tanggal akhir
        if ($request->has('tgl_akhir') && !empty($request->tgl_akhir)) {
            $expedisi->whereDate('tglsj', '<=', $request->tgl_akhir);
        }

        // Filter search (NO MUAT, CUSTOMER, RUTE)
        if ($request->has('search_muat') && !empty($request->search_muat)) {
            $search = $request->search_muat;
            $expedisi->where(function($query) use ($search) {
                $query->where('NOSJ', 'like', '%' . $search . '%')
                    ->orWhere('CUSTOMER', 'like', '%' . $search . '%')
                    ->orWhere('rute', 'like', '%' . $search . '%');
            });
        }

        return DataTables::of($expedisi)
            ->addIndexColumn()
            ->addColumn('action', function($row) {
                $btn = '<div class="d-flex gap-2">'; // Gap lebih besar
                $btn .= '<button type="button" class="btn btn-sm btn-outline-primary px-3 py-1 pickSurjal"
                            data-id="'.$row->id.'" data-nosj="'.$row->NOSJ.'" title="Pilih">
                            <i class="bx bx-check" style="font-size: 14px;"></i>
                        </button>';
                $btn .= '<button type="button" class="btn btn-sm btn-outline-danger px-3 py-1 deleteSurjal"
                            data-id="'.$row->id.'" data-nosj="'.$row->NOSJ.'" title="Hapus">
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

    public function getDataCustomer(){
        $customers = Mcustomer::select(['id', 'kode_cus','CUSTOMER', 'NAMACUST', 'TYPECUST', 'TELEPON', 'EMAIL', 'ALAMAT1', 'TELEPON', 'created_at']);

        return DataTables::of($customers)
            ->addIndexColumn()
            ->addColumn('action', function($customer) {
                return '
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info view-btn-customer-rent-dingin" data-id="'.$customer->kode_cus.'" data-name="'.$customer->NAMACUST.'" data-customer="'.$customer->CUSTOMER.'" data-alamat="'.$customer->ALAMAT1.'" data-telepon="'.$customer->TELEPON.'" data-bs-toggle="tooltip" title="View">
                            <i class="bx bx-check"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function storeRentPendinginSurjal(Request $request){
        $validator = Validator::make($request->all(), [
            'tanggal_surjal_rent_dingin' => 'required|date',
            'customer_rent_dingin' => 'required|string',
            'jml_hari_rent_dingin' => 'required|numeric|min:1',
            'harga_rent_dingin' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // ======================
            // GENERATE NOSJ
            // ======================
            $nosj = $this->generateNoSuratJalan();

            // ======================
            // HITUNG ULANG (SERVER SIDE)
            // ======================
            $jumlah = (int) $request->jml_hari_rent_dingin;
            $harga  = (int) $request->harga_rent_dingin;
            // DISC dari form = PERSEN
            $discPercent = (float) ($request->discount_rent_dingin ?? 0);
            // Pajak = PERSEN
            $ppnPercent = (float) ($request->pajak_rent_dingin ?? 0);
            // ======================
            // PERHITUNGAN
            // ======================
            // 1. Sub total
            $subTotal = $jumlah * $harga;
            // 2. Discount dalam rupiah
            $discAmount = round($subTotal * ($discPercent / 100));
            // 3. Safety: diskon tidak boleh > subtotal
            $discAmount = min($discAmount, $subTotal);
            // 4. DPP
            $dpp = $subTotal - $discAmount;
            // 5. Pajak
            $ppn = round($dpp * ($ppnPercent / 100));
            // 6. Grand total
            $grandTotal = $dpp + $ppn;

            // ======================
            // SIMPAN DATA
            // ======================
            $expedisi = Expedisi::create([
                // DOKUMEN
                'tglsj' => $request->tanggal_surjal_rent_dingin,
                'NOSJ' => $nosj,
                'WILAYAH' => $request->wilayah_nosj_rent_dingin,

                // CUSTOMER
                'CUSTOMER_KODE' => $request->customer_rent_dingin_id,
                'CUSTOMER' => $request->customer_rent_dingin,

                // ITEM RENT
                'PESANAN' => $request->item_rent_dingin,

                // DRIVER & KENDARAAN
                'DRIVER' => $request->driver_rent_dingin_id,
                'NAMA_DRIVER' => $request->driver_rent_dingin,
                'KENDARAAN' => $request->kendaraan_rent_dingin_id,
                'NAMA_KENDARAAN' => $request->kendaraan_rent_dingin,

                // PERHITUNGAN
                'JUMLAH' => $jumlah,
                'UNIT' => 'HARI',
                'HARGA' => $harga,
                'DISC' => $discPercent,
                'NDISC' => $discAmount,
                'TOTAL' => $dpp,
                'PPN' => $ppnPercent,
                'GRAND' => $grandTotal,

                // STATUS
                'JENIS' => 'REN',
                'STS' => 'INVOICE',
                'READY' => 'Y',
                'CLOSSING' => 'N',

                // KETERANGAN
                'KETERANGAN' => $request->keterangan_rent_dingin ?? 'RENT PENDINGIN',

                // USER
                'user_id' => auth()->user()->user_id,
                'user' => auth()->user()->name,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rent pendingin berhasil disimpan',
                'data' => [
                    'NOSJ' => $expedisi->NOSJ,
                    'GRAND' => number_format($expedisi->GRAND, 0, ',', '.'),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan rent pendingin',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function showSurjal($nosj){
        $data = Expedisi::where('NOSJ', $nosj)
            ->where('JENIS', 'REN')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    private function generateNoSuratJalan(){
        return DB::transaction(function () {

            $bulan = now()->format('m');
            $tahun = now()->format('y');

            $prefix = 'SJ' . $bulan . $tahun;

            $last = Expedisi::where('NOSJ', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('NOSJ', 'desc')
                ->first();

            if ($last) {
                $lastNumber = (int) substr($last->NOSJ, -5);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            $number = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            return $prefix . $number;
        });
    }

}

// public function getDataMuat1(Request $request){
    //     $expedisi = Expedisi::select([
    //         'id',
    //         'NOMUAT',
    //         'TGLMUAT',
    //         'CUSTOMER',
    //         'rute',
    //         'JUMLAH',
    //         'UNIT',
    //         'HARGA',
    //         'DISC',
    //         'DC',
    //         'GRAND',
    //         'NOSJ',
    //         'KENDARAAN',
    //         'NAMA_KENDARAAN',
    //         'DRIVER',
    //         'NAMA_DRIVER',
    //         'STS',
    //         'created_at'
    //     ])->where('JENIS', 'REN');

    //     // 🔐 FILTER ROLE DRIVER
    //     if (auth()->user()->roles === 'driver') {
    //         $expedisi->where('user_id', auth()->user()->user_id);
    //     }

    //     // Filter tanggal mulai
    //     if ($request->has('tgl_mulai') && !empty($request->tgl_mulai)) {
    //         $expedisi->whereDate('TGLMUAT', '>=', $request->tgl_mulai);
    //     }

    //     // Filter tanggal akhir
    //     if ($request->has('tgl_akhir') && !empty($request->tgl_akhir)) {
    //         $expedisi->whereDate('TGLMUAT', '<=', $request->tgl_akhir);
    //     }

    //     // Filter search (NO MUAT, CUSTOMER, RUTE)
    //     if ($request->has('search_muat') && !empty($request->search_muat)) {
    //         $search = $request->search_muat;
    //         $expedisi->where(function($query) use ($search) {
    //             $query->where('NOMUAT', 'like', '%' . $search . '%')
    //                 ->orWhere('CUSTOMER', 'like', '%' . $search . '%')
    //                 ->orWhere('rute', 'like', '%' . $search . '%')
    //                 ->orWhere('NOSJ', 'like', '%' . $search . '%');
    //         });
    //     }

    //     return DataTables::of($expedisi)
    //         ->addIndexColumn()
    //         ->addColumn('action', function($row) {
    //             $btn = '<div class="d-flex gap-2">'; // Gap lebih besar
    //             $btn .= '<button type="button" class="btn btn-sm btn-outline-primary px-3 py-1 pickRentDgn"
    //                         data-id="'.$row->id.'" data-nomuat="'.$row->NOMUAT.'" title="Pilih">
    //                         <i class="bx bx-check" style="font-size: 14px;"></i>
    //                     </button>';
    //             $btn .= '<button type="button" class="btn btn-sm btn-outline-danger px-3 py-1 deleteRentDgn"
    //                         data-id="'.$row->id.'" data-nomuat="'.$row->NOMUAT.'" title="Hapus">
    //                         <i class="bx bx-trash" style="font-size: 14px;"></i>
    //                     </button>';
    //             $btn .= '</div>';
    //             return $btn;
    //         })
    //         ->addColumn('total_formatted', function($row) {
    //             return 'Rp ' . number_format($row->GRAND, 0, ',', '.');
    //         })
    //         ->addColumn('harga_formatted', function($row) {
    //             return 'Rp ' . number_format($row->HARGA, 0, ',', '.');
    //         })
    //         ->addColumn('dc_formatted', function($row) {
    //             return 'Rp ' . number_format($row->DC, 0, ',', '.');
    //         })
    //         ->editColumn('TGLMUAT', function($row) {
    //             return $row->TGLMUAT ? date('d-m-Y', strtotime($row->TGLMUAT)) : '-';
    //         })
    //         ->editColumn('JUMLAH', function($row) {
    //             return number_format($row->JUMLAH, 0, ',', '.') . ' ' . $row->UNIT;
    //         })
    //         ->editColumn('DISC', function($row) {
    //             return $row->DISC ? $row->DISC . '%' : '-';
    //         })
    //         ->rawColumns(['action', 'total_formatted', 'harga_formatted', 'dc_formatted'])
    //         ->make(true);
    // }
