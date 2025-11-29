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

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Prices::select('ID', 'KETERANGAN', 'DARI', 'SAMPAI', 'RUTE', 'HARGA', 'HV', 'HKG', 'HBOK', 'JENIS', 'created_at');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '
                        <button onclick="editData('.$row->ID.')" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 mr-2">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>
                        <button onclick="deleteData('.$row->ID.')" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                            <i class="fas fa-trash mr-1"></i>Hapus
                        </button>
                    ';
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
            'KETERANGAN' => 'required|string|max:50',
            'DARI' => 'required|numeric',
            'SAMPAI' => 'required|numeric',
            'RUTE' => 'required|string|max:30',
            'HARGA' => 'required|numeric',
            'JENIS' => 'required|string|max:1',
        ]);

        // Generate RUTE lengkap
        $ruteLengkap = $request->RUTE;

        $Prices = Prices::create([
            'KETERANGAN' => $request->KETERANGAN,
            'DARI' => $request->DARI,
            'SAMPAI' => $request->SAMPAI,
            'RUTE' => $ruteLengkap,
            'HARGA' => $request->HARGA,
            'HV' => 0,
            'HKG' => 0,
            'HBOK' => 0,
            'JENIS' => $request->JENIS,
            'USER' => Auth::check() ? Auth::user()->name : 'System',
            'USEREDIT' => Auth::check() ? Auth::user()->name : 'System',
            'KUNCI' => uniqid(),
            'HG' => $request->HG ?? 0,
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
            'KETERANGAN' => 'required|string|max:50',
            'DARI' => 'required|numeric',
            'SAMPAI' => 'required|numeric',
            'RUTE' => 'required|string|max:30',
            'HARGA' => 'required|numeric',
            'JENIS' => 'required|string|max:1',
        ]);

        $Prices = Prices::findOrFail($id);

        // Extract tujuan dari RUTE (remove "DPS-")
        $tujuan = $request->RUTE;
        if (strpos($tujuan, 'DPS-') === 0) {
            $tujuan = substr($tujuan, 4);
        }

        $Prices->update([
            'KETERANGAN' => $request->KETERANGAN,
            'DARI' => $request->DARI,
            'SAMPAI' => $request->SAMPAI,
            'RUTE' => 'DPS-' . $tujuan,
            'HARGA' => $request->HARGA,
            'HV' => $request->HV ?? 0,
            'HKG' => $request->HKG ?? 0,
            'HBOK' => $request->HBOK ?? 0,
            'JENIS' => $request->JENIS,
            'USEREDIT' => Auth::check() ? Auth::user()->name : 'System',
            'HG' => $request->HG ?? 0,
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
