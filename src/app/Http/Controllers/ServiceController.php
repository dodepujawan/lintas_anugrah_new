<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Msupplier;
use App\Models\Kendaraan;
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

                $edit = '<button class="btn btn-sm btn-warning edit-service" data-id="'.$row->id.'">Edit</button>';

                $delete = '<button class="btn btn-sm btn-danger delete-service" data-id="'.$row->id.'">Delete</button>';

                return $edit.' '.$delete;

            })

            ->rawColumns(['action'])

            ->make(true);
    }

        public function store(Request $request){
        try {
            DB::beginTransaction();

            $id = $request->id_service;
            $no_bukti = null; // Inisialisasi

            $data = [
                'NO_REF' => $request->no_faktur_service,
                'TGL_SERVIS' => $request->tgl_document_service,
                'KODE_MOBIL' => $request->kendaraan_service,
                'KODE_SUPPLIER' => $request->supplier,
                'FNO_PRK_B' => $request->fno_prk_b_service,
                'KETERANGAN' => $request->keterangan_service,
                'NILAI_SERVIS' => $request->nilai_servis,
                'USER_INPUT' => auth()->user()->id ?? 'admin',
            ];

            if ($id) {
                // UPDATE
                DB::table('service')
                    ->where('id', $id)
                    ->update(array_merge($data, [
                        'updated_at' => now()
                    ]));
                $message = 'Data service berhasil diupdate';
            } else {
                // INSERT - generate nomor bukti
                $last = DB::table('service')->orderBy('id','desc')->first();
                $next = $last ? $last->id + 1 : 1;
                $no_bukti = now()->format('Ymd') . str_pad($next,6,'0',STR_PAD_LEFT);

                DB::table('service')->insert(array_merge($data, [
                    'NO_SERVICE' => $no_bukti,
                    'TGL_TRANSAKSI' => now(),
                    'created_at' => now()
                ]));
                $message = 'Data service berhasil disimpan';
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $message,
                'kode' => $no_bukti // Sudah aman karena sudah diinisialisasi
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show($id){
        $data = Service::leftJoin('kendaraan', 'kendaraan.KODE', '=', 'service.KODE_MOBIL')
            ->leftJoin('msupplier', 'msupplier.SUPPLIER', '=', 'service.KODE_SUPPLIER')
            ->where('service.id', $id)
            ->select(
                'service.*',

                'kendaraan.NAMA as kendaraan_nama',
                'kendaraan.PLAT as kendaraan_plat',

                'msupplier.NAMA as supplier_nama'
            )
            ->firstOrFail();

        return response()->json([

            'id_service' => $data->id,

            'supplier' => $data->KODE_SUPPLIER,
            'supplier_nama' => $data->supplier_nama,

            'kendaraan_service' => $data->KODE_MOBIL,
            'kendaraan_service_nama' => $data->kendaraan_nama,
            'kendaraan_service_plat' => $data->kendaraan_plat,

            'fno_prk_b_service' => $data->FNO_PRK_B,

            'nilai_servis' => $data->NILAI_SERVIS,
            'keterangan' => $data->KETERANGAN,

            'no_faktur' => $data->NO_REF,
            'tgl_document' => $data->TGL_SERVIS,

            'tgl_jatuh_tempo' => null,
            'no_jurnal' => null
        ]);
    }

    public function dataSupplierModal(){
        $query = Msupplier::select([
            'id',
            'SUPPLIER',
            'KATEGORI',
            'NAMA',
            'KOTA',
            'TELEPON',
            'EMAIL'
        ]);

        return DataTables::of($query)

            ->addColumn('kategori_label', function ($row) {

                if ($row->KATEGORI == 'SP') {
                    return '<span class="badge bg-primary">Supplier</span>';
                }

                if ($row->KATEGORI == 'LS') {
                    return '<span class="badge bg-warning">Leasing</span>';
                }

                return '-';
            })

            ->addColumn('action', function ($row) {

                return '
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-sm btn-warning btn-pick-supplier-service"
                            data-id="'.$row->id.'" data-supplier="'.$row->SUPPLIER.'" data-nama="'.$row->NAMA.'"
                            title="Edit">
                            <i class="bx bx-check"></i>
                        </button>
                    </div>
                ';
            })

            ->rawColumns(['kategori_label','action'])
            ->make(true);
    }

    public function dataKendaraanModel()
    {
        $kendaraan = Kendaraan::select('*')
        ->orderBy('id', 'desc');;

        return DataTables::of($kendaraan)
            ->addIndexColumn()
            ->addColumn('action', function($kendaraan) {
                return '
                <div class="btn-group">
                    <button class="btn btn-sm btn-primary edit pickKendaraanServiceModel" data-id="'.$kendaraan->id.'" data-kode="'.$kendaraan->KODE.'" data-nama="'.$kendaraan->NAMA.'" data-fno="'.$kendaraan->FNO_PRK_B.'" data-plat="'.$kendaraan->PLAT.'" id="pickKendaraanService"><i class="bx bx-check"></i></button>
                </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
