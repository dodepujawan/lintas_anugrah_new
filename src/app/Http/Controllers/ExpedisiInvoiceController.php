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
use Carbon\Carbon;

class ExpedisiInvoiceController extends Controller
{
    public function index()
    {
        return view('expedisi.expedisi-invoice');
    }

    public function getDataMuat(Request $request){
        // 🔹 Subquery: ambil BARIS PERTAMA per NOMUAT
        $subQuery = DB::table('expedisi')
            ->select(DB::raw('MIN(id) as id'))
            ->where('JENIS', 'EKS')
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

        // 🧾 FILTER INVOICE
        $filterInvoice = $request->filter_invoice ?? 'belum'; // default

        if ($filterInvoice === 'belum') {
            $expedisi->where(function ($q) {
                $q->whereNull('INVOICE')
                ->orWhere('INVOICE', '');
            });
        }

        return DataTables::of($expedisi)
            ->addIndexColumn()

            // 🔘 ACTION BUTTON
            ->addColumn('action', function ($row) {
                $btn = '<div class="d-flex gap-2">'; // Gap lebih besar
                $btn .= '<button type="button" class="btn btn-sm btn-outline-primary px-3 py-1 pickMuat"
                            data-id="'.$row->id.'"
                            data-nomuat="'.$row->NOMUAT.'"
                            title="Pilih">
                            <i class="bx bx-check" style="font-size: 14px;"></i>
                        </button>';
                $btn .= '<button type="button" class="btn btn-sm btn-outline-danger px-3 py-1 deleteMuat"
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
}
