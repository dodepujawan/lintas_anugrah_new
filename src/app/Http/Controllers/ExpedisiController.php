<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expedisi;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ExpedisiController extends Controller
{
    public function index()
    {
        return view('expedisi.expedisi');
    }
}
