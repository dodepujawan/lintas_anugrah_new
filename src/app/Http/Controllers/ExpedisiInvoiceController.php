<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Expedisi;
use App\Models\Rekening;
use App\Models\Signature;
use App\Models\Mcustomer;
use App\Models\Arh;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Carbon\Carbon;
use Exception;

class ExpedisiInvoiceController extends Controller
{
    public function index()
    {
        return view('expedisi.expedisi-invoice');
    }

    public function getDataMuat(Request $request){
        $expedisi = Expedisi::select([
            'id',
            'NOMUAT',
            'TGLMUAT',
            'CUSTOMER',
            'CUSTOMER_KODE',
            'PESANAN',
            'GB',
            'PESANANGB',
            'rute',
            'NAMA_KENDARAAN',
            'JUMLAH',
            'UNIT',
            'HARGA',
            'DISC',
            'DC',
            'GRAND',
            'NOSJ',
            'INVOICE',
            'JENIS',
        ])
        ->where('JENIS', 'EKS')
        ->whereNotNull('NOMUAT')   // 🔒 wajib punya NOMUAT
        ->orderByDesc('id');

        // 🔐 FILTER DRIVER
        if (auth()->user()->role_old === 'driver') {
            $expedisi->where('user_id', auth()->user()->user_id);
        }

        // 📅 FILTER TANGGAL
        if ($request->filled('tgl_mulai')) {
            $expedisi->whereDate('TGLMUAT', '>=', $request->tgl_mulai);
        }

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

        // 🧾 FILTER GABUNG (DEFAULT: BELUM)
        $filterInvoice = $request->filter_invoice ?? 'belum';

        if ($filterInvoice === 'belum') {

            // tampilkan yang belum invoice
            $expedisi->where(function ($q) {
                $q->whereNull('GB')
                ->orWhere('GB', '');
            });

        } elseif ($filterInvoice === 'sudah') {

            // tampilkan yang sudah invoice
            // tapi hanya master row (yang punya harga)
            $expedisi->whereNotNull('GB')
                    ->where('GB', '!=', '')
                    ->where('HARGA', '>', 0);
        }

        return DataTables::of($expedisi)
            ->addIndexColumn()
            ->editColumn('TGLMUAT', fn ($r) =>
                $r->TGLMUAT ? date('d-m-Y', strtotime($r->TGLMUAT)) : '-'
            )
            ->editColumn('JUMLAH', fn ($r) =>
                number_format($r->JUMLAH ?? 0, 0, ',', '.') . ' ' . ($r->UNIT ?? '')
            )
            ->addColumn('harga_formatted', fn ($r) =>
                'Rp ' . number_format($r->HARGA ?? 0, 0, ',', '.')
            )
            ->addColumn('dc_formatted', fn ($r) =>
                'Rp ' . number_format($r->DC ?? 0, 0, ',', '.')
            )
            ->addColumn('total_formatted', fn ($r) =>
                'Rp ' . number_format($r->GRAND ?? 0, 0, ',', '.')
            )
            ->make(true);
    }

    public function dataGabung(Request $request){
        $data = Expedisi::select([
            'id',
            'NOMUAT',
            'TGLMUAT',
            'PESANAN',
            'CUSTOMER_KODE',
            'CUSTOMER',
            'rute',
            'NAMA_KENDARAAN',
            'JUMLAH',
            'JUMLAH as jumlahreal',
            'UNIT',
            'JENISHRG',
            'HARGA',
            'DISC',
            'DC',
            'PPN',
            'TOTAL',
            'GRAND',
            'NOSJ',
        ])
        ->where('CUSTOMER_KODE', $request->customer_kode)
        ->where('JENIS', 'EKS')
        ->whereNotNull('NOMUAT')
        ->where(function ($q) {
            $q->whereNull('INVOICE')
            ->orWhere('INVOICE', '');
        })
        ->orderBy('TGLMUAT');

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('TGLMUAT', fn ($r) =>
                $r->TGLMUAT ? date('d-m-Y', strtotime($r->TGLMUAT)) : '-'
            )
            ->editColumn('JUMLAH', fn ($r) =>
                number_format($r->JUMLAH ?? 0, 0, ',', '.') . ' ' . ($r->UNIT ?? '')
            )
            ->addColumn('harga_formatted', fn ($r) =>
                'Rp ' . number_format($r->HARGA ?? 0, 0, ',', '.')
            )
            ->addColumn('gtotal_formatted', fn ($r) =>
                'Rp ' . number_format($r->GRAND ?? 0, 0, ',', '.')
            )
            ->make(true);
    }

    public function getExistingGabung(Request $request){
        $gb = $request->gbExist;

        $rows = Expedisi::where('GB', $gb)
            ->orderBy('NOSJ')
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'data' => [],
                'master' => null
            ]);
        }

        // 🔥 karena belum invoice → tidak ada "master harga"
        // jadi kita ambil baris pertama saja sebagai referensi
        $master = $rows->first();

        $data = $rows->map(function($r,$i){

            return [
                'DT_RowIndex' => $i + 1,
                'NOMUAT' => $r->NOMUAT,
                'TGLMUAT' => $r->TGLMUAT ? date('d-m-Y', strtotime($r->TGLMUAT)) : '-',
                'NOSJ' => $r->NOSJ,
                'PESANAN' => $r->PESANAN,
                'rute' => $r->rute,
                'NAMA_KENDARAAN' => $r->NAMA_KENDARAAN,
                'JUMLAH' => number_format($r->JUMLAH ?? 0,0,',','.').' '.$r->UNIT,

                // ❗ ini kemungkinan 0 semua (karena belum invoice)
                'harga_formatted' => 'Rp '.number_format($r->HARGA ?? 0,0,',','.'),
                'gtotal_formatted' => 'Rp '.number_format($r->GRAND ?? 0,0,',','.'),

                'HARGA' => $r->HARGA,
                'DISC' => $r->DISC,
                'DC' => $r->DC,
                'TOTAL' => $r->TOTAL,
                'PPN' => $r->PPN
            ];

        });

        return response()->json([
            'data' => $data,
            'master' => $master
        ]);
    }

    public function storeGabungInvoice(Request $request){
        try {
            $gb = null;

            DB::transaction(function () use ($request, &$gb) {

                $rows = Expedisi::whereIn('NOSJ', $request->nosj_list)
                    ->lockForUpdate()
                    ->orderBy('NOSJ')
                    ->get();

                // ❌ Tidak ada data
                if ($rows->isEmpty()) {
                    throw new Exception('Data SJ tidak ditemukan');
                }

                // ❌ Harus lebih dari 1 (gabung)
                if ($rows->count() < 2) {
                    throw new Exception('Gabung invoice minimal 2 SJ');
                }

                // ❌ Cek sudah pernah invoice
                foreach ($rows as $row) {
                    if ($row->STS === 'INVOICE') {
                        throw new Exception("SJ {$row->NOSJ} sudah di-invoice");
                    }
                }

                // ✅ Hanya generate GB
                $gb = $this->generateGB();

                foreach ($rows as $row) {

                    // backup nilai lama
                    $row->HARGAAW  = $row->HARGA;
                    $row->NDISCAW  = $row->NDISC;
                    $row->DCAW     = $row->DC;

                    // ❗ Fokus ke GB saja
                    $row->GB        = $gb;
                    $row->PESANANGB = $request->item;

                    // ❗ Status jangan INVOICE (opsional tergantung flow kamu)
                    $row->STS = 'GABUNG';

                    $row->USERINV = auth()->user()->user_id . '-' . now()->format('d-m-Y h:i:s A');

                    $row->save();
                }
            });

            return response()->json([
                'status'  => true,
                'message' => 'Gabung SJ berhasil',
                'gb'      => $gb
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateGabungInvoice(Request $request){
        try {
            $gb = $request->gb;
            DB::transaction(function () use ($request, $gb) {
                if (!$gb) {
                    throw new Exception('GB tidak ditemukan');
                }

                // =============================
                // 1. rollback GB lama
                // =============================
                $oldRows = Expedisi::where('GB', $gb)
                    ->lockForUpdate()
                    ->get();

                foreach ($oldRows as $row) {
                    $row->GB = null;
                    $row->PESANANGB = null;
                    $row->STS = 'GABUNG';
                    $row->save();
                }

                // =============================
                // 2. ambil SJ baru
                // =============================
                $rows = Expedisi::whereIn('NOSJ', $request->nosj_list)
                    ->lockForUpdate()
                    ->orderBy('NOSJ')
                    ->get();

                if ($rows->isEmpty()) {
                    throw new Exception('Data SJ tidak ditemukan');
                }

                if ($rows->count() < 2) {
                    throw new Exception('Gabung minimal 2 SJ');
                }

                foreach ($rows as $row) {
                    if ($row->STS === 'INVOICE') {
                        throw new Exception("SJ {$row->NOSJ} sudah di-invoice");
                    }
                    $row->GB = $gb;
                    $row->PESANANGB = $request->item;
                    $row->STS = 'GABUNG';
                    $row->save();
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'Gabung berhasil diupdate',
                'gb' => $gb
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function printInvoiceText($invoiceNo){
        $rows = Expedisi::where('INVOICE', $invoiceNo)
            ->orderBy('NOSJ')
            ->get();

        if ($rows->isEmpty()) {
            abort(404);
        }

        $master = $rows->first();

        $lines = [];

        // HEADER
        $lines[] = str_pad("PT. LINTAS ANUGERAH SEJATI", 80, " ", STR_PAD_BOTH);
        $lines[] = str_repeat("=", 80);
        $lines[] = "INVOICE : {$master->INVOICE}";
        $lines[] = "TANGGAL : " . date('d-m-Y', strtotime($master->TGLINVOICE));
        $lines[] = "CUSTOMER: {$master->CUSTOMER}";
        $lines[] = str_repeat("=", 80);

        // TABLE HEADER
        $lines[] = sprintf(
            "%-3s %-10s %-30s %-5s %10s",
            "NO", "SJ", "NAMA BARANG", "QTY", "TOTAL"
        );

        $lines[] = str_repeat("-", 80);

        // DATA
        $no = 1;
        foreach ($rows as $r) {
            $nama = trim($r->PESANANGB) !== '' ? $r->PESANANGB : $r->PESANAN;

            $lines[] = sprintf(
                "%-3s %-10s %-30s %-5s %10s",
                $no,
                $r->NOSJ,
                substr($nama, 0, 30),
                "KG",
                number_format($r->TOTAL, 0, ',', '.')
            );

            $no++;
        }

        $lines[] = str_repeat("=", 80);
        $lines[] = str_pad(
            "TOTAL : " . number_format($master->GRAND, 0, ',', '.'),
            80,
            " ",
            STR_PAD_LEFT
        );

        // FOOTER
        $lines[] = "";
        $lines[] = "";
        $lines[] = str_pad("PENERIMA", 40) . str_pad("HORMAT KAMI", 40);
        $lines[] = "";
        $lines[] = "";
        $lines[] = str_pad("(...................)", 40) . str_pad("LINTAS ANUGERAH", 40);

        // 🔥 FIX HEIGHT (60 BARIS)
        $maxLines = 60;
        while (count($lines) < $maxLines) {
            $lines[] = "";
        }

        $text = implode("\n", $lines);

        return response()->json([
            'text' => $text
        ]);
    }

    public function pdfGabungInvoice($invoiceNo){
        $rows = Expedisi::where('INVOICE', $invoiceNo)
            ->orderBy('NOSJ')
            ->get();

        if ($rows->isEmpty()) {
            abort(404, 'Invoice tidak ditemukan');
        }

        $master = $rows->first();
        // 🔥 ambil rekening aktif
        $rekening = Rekening::where('AKTIF', 1)->first();
        $signature = Signature::orderByDesc('id')->first();

        // membuat folder tempPath
        $tempPath = storage_path('app/mpdf-temp');
        if (!is_dir($tempPath)) {
            mkdir($tempPath, 0775, true);
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
            'tempDir' => $tempPath,
        ]);

        $html = view('expedisi.expedisi-invoice-pdf', compact('rows', 'master', 'rekening', 'signature'))->render();

        $mpdf->WriteHTML($html);
        return response($mpdf->Output('', 'S'),200)->header('Content-Type','application/pdf');
        // return $mpdf->Output("INVOICE-{$invoiceNo}.pdf", 'I'); // tampil di browser
    }

    private function generateInvoiceOnline(): string{
        $tahun = now()->format('Y');

        $last = Expedisi::where('INVOICE', 'like', "FJO{$tahun}%")
            ->orderBy('INVOICE', 'desc')
            ->lockForUpdate()
            ->first();

        $lastNo = $last
            ? intval(substr($last->INVOICE, -6))
            : 0;

        return 'FJO' . $tahun . str_pad($lastNo + 1, 6, '0', STR_PAD_LEFT);
    }

    private function generateGB(): string{
        $date = now()->format('Ymd');

        $last = Expedisi::whereNotNull('GB')
            ->orderBy('GB', 'desc')
            ->lockForUpdate()
            ->first();

        $lastNo = $last
            ? intval(substr($last->GB, -4))
            : 0;

        return $date . str_pad($lastNo + 1, 4, '0', STR_PAD_LEFT);
    }

    private function calcNominalDiskon(Request $request){
        $total  = (float) $request->total;   // total_gabung_exp_inv
        $diskon = (float) $request->diskon;  // persen, contoh: 5

        if ($total <= 0 || $diskon <= 0) {
            return 0;
        }

        return $total * ($diskon / 100);
    }

}
