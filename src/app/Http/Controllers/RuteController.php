<?php

namespace App\Http\Controllers;

use App\Models\Rute;
use Illuminate\Http\Request;
use DataTables;

class RuteController extends Controller
{
    public function index()
    {
        return view('rute.index');
    }

    public function getData()
    {
        $rutes = Rute::all();
        return response()->json([
            'success' => true,
            'data' => $rutes
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'RUTE' => 'required|string|max:255|unique:rute,RUTE'
        ]);

        $rute = Rute::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Rute berhasil ditambahkan',
            'data' => $rute
        ]);
    }
}
