<?php

namespace App\Http\Controllers;
use App\Models\Prices;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

class PricesController extends Controller
{
    public function index()
    {
        return view('prices.prices');
    }

    public function getData(Request $request){
        if ($request->ajax()) {
            $data = Prices::select('ID', 'KETERANGAN', 'DARI', 'SAMPAI', 'RUTE', 'HARGA', 'HV', 'HKG', 'HBOK', 'JENIS', 'created_at');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    // Cek jika user bukan admin, kembalikan tanda '-'
                    if (!Auth::check() || Auth::user()->roles != 'admin') {
                        return '-';
                    }

                    $btn = '<div class="btn-group"> <button onclick="editData('.$row->ID.')"  class="btn btn-sm btn-warning edit edit-driver"><i class="bx bx-edit"></i></button>
                    <button onclick="deleteData('.$row->ID.')" class="btn btn-sm btn-danger delete delete-driver"><i class="bx bx-trash"></i></button>
                    </div>';
                    return $btn;
                })
                ->editColumn('HARGA', function($row){
                    return number_format($row->HARGA, 0, ',', '.');
                })
                ->editColumn('JENIS', function($row){
                    return $row->JENIS == 'E' ? 'Eceran' : 'Boking';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'keterangan_price' => 'required|string|max:50',
            'dari_price' => 'required|numeric',
            'dari_price' => 'required|numeric',
            'rute_price' => 'required|string|max:30',
            'harga_price' => 'required|numeric',
            'jenis_val' => 'required|string|max:1',
        ]);

        // Generate KODE
        $lastCode = Prices::orderBy('ID', 'desc')->value('KODE');
        $number = $lastCode ? (int) substr($lastCode, 3) + 1 : 1;
        $kode = 'PRC' . str_pad($number, 6, '0', STR_PAD_LEFT);

        $Prices = Prices::create([
            'KODE' => $kode,
            'KETERANGAN' => $request->keterangan_price,
            'DARI' => $request->dari_price,
            'SAMPAI' => $request->sampai_price,
            'RUTE' => $request->rute_price,
            'HARGA' => $request->harga_price,
            'HV' => 0,
            'HKG' => 0,
            'HBOK' => 0,
            'JENIS' => $request->jenis_val,
            'USER' => Auth::check() ? Auth::user()->user_id : 'System',
            'USEREDIT' => Auth::check() ? Auth::user()->user_id : 'System',
            'KUNCI' => $request->keterangan_price . $request->jenis_val . $request->dari_price . $request->sampai_price,
            'HG' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $Prices
        ]);
    }

    public function show($id)
    {
        $Prices = Prices::findOrFail($id);
        return response()->json($Prices);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan_price' => 'required|string|max:50',
            'dari_price' => 'required|numeric',
            'dari_price' => 'required|numeric',
            'rute_price' => 'required|string|max:30',
            'harga_price' => 'required|numeric',
            'jenis_val' => 'required|string|max:1',
        ]);

        $Prices = Prices::findOrFail($id);

        $Prices->update([
            'KETERANGAN' => $request->keterangan_price,
            'DARI' => $request->dari_price,
            'SAMPAI' => $request->sampai_price,
            'RUTE' => $request->rute_price,
            'HARGA' => $request->harga_price,
            'HV' => 0,
            'HKG' => 0,
            'HBOK' => 0,
            'JENIS' => $request->jenis_val,
            'USEREDIT' => Auth::check() ? Auth::user()->user_id : 'System',
            'KUNCI' => $request->keterangan_price . $request->jenis_val . $request->dari_price . $request->sampai_price,
            'HG' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diupdate',
            'data' => $Prices
        ]);
    }

    public function destroy($id)
    {
        $Prices = Prices::findOrFail($id);
        $Prices->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
