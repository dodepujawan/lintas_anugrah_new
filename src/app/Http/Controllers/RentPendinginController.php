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
        ])->where('JENIS', 'REN');

        // 🔐 FILTER ROLE DRIVER
        if (auth()->user()->roles === 'driver') {
            $expedisi->where('user_id', auth()->user()->user_id);
        }

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
                $btn .= '<button type="button" class="btn btn-sm btn-outline-primary px-3 py-1 pickRentDgn"
                            data-id="'.$row->id.'" data-nomuat="'.$row->NOMUAT.'" title="Pilih">
                            <i class="bx bx-check" style="font-size: 14px;"></i>
                        </button>';
                $btn .= '<button type="button" class="btn btn-sm btn-outline-danger px-3 py-1 deleteRentDgn"
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
}
