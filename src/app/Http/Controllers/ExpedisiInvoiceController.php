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

        // 🧾 FILTER INVOICE (DEFAULT: BELUM)
        $filterInvoice = $request->filter_invoice ?? 'belum';

        if ($filterInvoice === 'belum') {

            // tampilkan yang belum invoice
            $expedisi->where(function ($q) {
                $q->whereNull('INVOICE')
                ->orWhere('INVOICE', '');
            });

        } elseif ($filterInvoice === 'sudah') {

            // tampilkan yang sudah invoice
            // tapi hanya master row (yang punya harga)
            $expedisi->whereNotNull('INVOICE')
                    ->where('INVOICE', '!=', '')
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

    public function getExistingInvoice(Request $request){
        $invoice = $request->invoice;

        $rows = Expedisi::where('INVOICE', $invoice)
            ->orderBy('NOSJ')
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'data' => [],
                'master' => null
            ]);
        }

        // master row = yang punya harga
        $master = $rows->firstWhere('HARGA', '>', 0);

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
            // untuk mengambil nilai dalam transaction dibawah supaya bisa kirim ke json respon karna json respon diluar transaction
            $invoiceNo = null;
            // DB::transaction tambahandari try and catch iar lebih paten
            DB::transaction(function () use ($request, &$invoiceNo) {
                $rows = Expedisi::whereIn('NOSJ', $request->nosj_list)
                ->lockForUpdate()
                ->orderBy('NOSJ')
                ->get();

                if ($rows->isEmpty()) {
                    throw new Exception('Data SJ tidak ditemukan');
                }

                foreach ($rows as $row) {
                    if ($row->STS === 'INVOICE') {
                        throw new Exception("SJ {$row->NOSJ} sudah di-invoice");
                    }
                }

                $invoiceNo = $this->generateInvoiceOnline();
                $gb = $this->generateGB();
                // PENANDA BARIS PERTAMA SAAT LOOP
                // ---------------------------------------------
                // Kasus invoice gabung:
                // - Kalau cuma 1 SJ  → baris ini otomatis jadi "master"
                // - Kalau banyak SJ → HANYA baris pertama yang isi nilai
                //   (HARGA, DISC, TOTAL, PPN, GRAND, PIUTANG)
                // - Baris selanjutnya dianggap "detail", nilainya 0
                //
                // $isFirst TIDAK mengecek jumlah data.
                // Dia cuma penanda urutan loop:
                //   - true  → baris pertama
                //   - false → baris berikutnya
                //
                // Catatan buat diri sendiri:
                // Ini dipakai biar gak perlu if count() yang ribet
                // dan tetap aman untuk 1 atau banyak data.
                $isFirst = true;
                foreach ($rows as $row) {

                    // backup nilai lama
                    $row->HARGAAW  = $row->HARGA;
                    $row->NDISCAW  = $row->NDISC;
                    $row->DCAW     = $row->DC;

                    $row->INVOICE     = $invoiceNo;
                    $row->TGLINVOICE  = now();
                    $row->STS         = 'INVOICE';
                    $row->GB          = $gb;
                    $row->PESANANGB   = $request->item;

                    if ($isFirst) {

                        // BARIS PERTAMA (MASTER)
                        $row->HARGA   = $request->harga;
                        $row->DISC    = $request->diskon;
                        $row->NDISC   = $this->calcNominalDiskon($request);
                        $row->DC      = $request->dc;
                        $row->TOTAL   = $request->total;
                        $row->PPN     = $request->ppn;
                        $row->GRAND   = $request->grand_total;
                        $row->PIUTANG = $request->grand_total;

                        $isFirst = false;

                    } else {

                        // BARIS LANJUTAN (DETAIL)
                        $row->HARGA   = 0;
                        $row->DISC    = 0;
                        $row->NDISC   = 0;
                        $row->DC      = 0;
                        $row->TOTAL   = 0;
                        $row->PPN     = 0;
                        $row->GRAND   = 0;
                        $row->PIUTANG = 0;
                    }

                    $row->USERINV = auth()->user()->user_id . '-' . now()->format('d-m-Y h:i:s A');

                    $row->save();
                }
                // Bagian ARH
                // Ambil nilai baris pertama yang diatas patokannya $isFirst = true;
                $masterRow = $rows->first();
                Arh::create([
                    'NOFAKTUR'   => $invoiceNo,
                    'TGLFAKTUR'  => $masterRow->TGLINVOICE,
                    'CUSTOMER'   => $masterRow->CUSTOMER,
                    'PIUTANG'    => $masterRow->GRAND,
                    'DISCOUNT'   => $masterRow->NDISC,
                    'SALDO'      => 0,
                    'CABANG'     => $masterRow->CABANG ?? '',
                    'KETERANGAN' => 'INVOICE DARI EXPEDISI',
                    'USER'   => auth()->user()->user_id,
                ]);
            });
            return response()->json([
                'status'  => true,
                'message' => 'Invoice gabung berhasil disimpan',
                'invoiceNo' => $invoiceNo
                // 'redirect' => route('expedisiInvoice.printInvoice', ['invoiceNo' => $invoiceNo])
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
             // untuk mengambil nilai dalam transaction dibawah supaya bisa kirim ke json respon karna json respon diluar transaction
            $invoiceNo = null;
            DB::transaction(function () use ($request, &$invoiceNo) {

                $invoiceNo = $request->no_invoice;

                if (!$invoiceNo) {
                    throw new Exception('Invoice tidak ditemukan');
                }

                // =============================
                // 1. rollback invoice lama
                // =============================
                $oldRows = Expedisi::where('INVOICE', $invoiceNo)
                    ->lockForUpdate()
                    ->get();

                foreach ($oldRows as $row) {

                    $row->TGLINVOICE = null;
                    $row->INVOICE = null;
                    $row->STS = null;
                    $row->GB = null;
                    $row->PESANANGB = null;

                    // kembalikan harga lama
                    $row->HARGA = $row->HARGAAW;
                    $row->NDISC = $row->NDISCAW;
                    $row->DC = $row->DCAW;

                    $row->TOTAL = 0;
                    $row->PPN = 0;
                    $row->GRAND = 0;
                    $row->PIUTANG = 0;

                    $row->save();
                }

                // hapus ARH lama
                Arh::where('NOFAKTUR', $invoiceNo)->delete();

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

                $isFirst = true;

                foreach ($rows as $row) {

                    $row->INVOICE = $invoiceNo;
                    $row->TGLINVOICE = now();
                    $row->STS = 'INVOICE';
                    $row->PESANANGB = $request->item;

                    if ($isFirst) {

                        $row->HARGA = $request->harga;
                        $row->DISC = $request->diskon;
                        $row->NDISC = $this->calcNominalDiskon($request);
                        $row->DC = $request->dc;
                        $row->TOTAL = $request->total;
                        $row->PPN = $request->ppn;
                        $row->GRAND = $request->grand_total;
                        $row->PIUTANG = $request->grand_total;

                        $isFirst = false;

                    } else {

                        $row->HARGA = 0;
                        $row->DISC = 0;
                        $row->NDISC = 0;
                        $row->DC = 0;
                        $row->TOTAL = 0;
                        $row->PPN = 0;
                        $row->GRAND = 0;
                        $row->PIUTANG = 0;
                    }

                    $row->save();
                }

                // =============================
                // 3. buat ARH baru
                // =============================
                $masterRow = $rows->first();

                Arh::create([
                    'NOFAKTUR' => $invoiceNo,
                    'TGLFAKTUR' => now(),
                    'CUSTOMER' => $masterRow->CUSTOMER,
                    'PIUTANG' => $request->grand_total,
                    'DISCOUNT' => $request->diskon,
                    'SALDO' => 0,
                    'CABANG' => $masterRow->CABANG ?? '',
                    'KETERANGAN' => 'UPDATE INVOICE EXPEDISI',
                    'USER' => auth()->user()->user_id,
                ]);

            });

            return response()->json([
                'status' => true,
                'message' => 'Invoice berhasil diupdate',
                'invoiceNo' => $invoiceNo
                // 'redirect' => route('expedisiInvoice.printInvoice', ['invoiceNo' => $invoiceNo])
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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
