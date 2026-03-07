<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ServiceController extends Controller
{
    public function index(){
        return view('service.service');
    }

    public function data(){
        $data = DB::table('service')
            ->orderBy('id','desc'); // terbaru dulu

        return DataTables::of($data)

            ->addIndexColumn()

            ->addColumn('action', function($row){

                $edit = '<button class="btn btn-sm btn-warning edit" data-id="'.$row->id.'">Edit</button>';

                $delete = '<button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>';

                return $edit.' '.$delete;

            })

            ->rawColumns(['action'])

            ->make(true);
    }

    public function store(Request $request){
        try {

            DB::beginTransaction();

            // generate nomor bukti
            $last = DB::table('service')->orderBy('id','desc')->first();

            $next = $last ? $last->id + 1 : 1;

            $no_bukti = now()->format('Ymd') . str_pad($next,6,'0',STR_PAD_LEFT);

            DB::table('service')->insert([

                'NO_SERVICE' => $no_bukti,
                'NO_REF' => $request->no_faktur,
                'TGL_SERVIS' => $request->tgl_document,
                'TGL_TRANSAKSI' => now(),
                'KODE_MOBIL' => $request->kendaraan_supplier,
                'KODE_SUPPLIER' => $request->supplier,
                'FNO_PRK_B' => $request->fno_prk_b_supplier,
                'KETERANGAN' => $request->keterangan,
                'NILAI_SERVIS' => $request->nilai_servis,
                'USER_INPUT' => auth()->user()->id ?? 'admin',
                'created_at' => now()

            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data service berhasil disimpan'
            ]);

        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
