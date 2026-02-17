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
        ])
        ->whereNotNull('NOMUAT')   // 🔒 wajib punya NOMUAT
        ->orderByDesc('id');

        // 🔐 FILTER DRIVER
        if (auth()->user()->roles === 'driver') {
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
            $expedisi->where(function ($q) {
                $q->whereNull('INVOICE')
                ->orWhere('INVOICE', '');
            });
        }
        // kalau 'semua' → TIDAK difilter apa pun

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
                'redirect' => route('expedisiInvoice.printSuratJalan', ['invoiceNo' => $invoiceNo])
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
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

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);

        $html = view('expedisi.expedisi-invoice-pdf', compact('rows', 'master', 'rekening', 'signature'))->render();

        $mpdf->WriteHTML($html);
        return $mpdf->Output("INVOICE-{$invoiceNo}.pdf", 'I'); // tampil di browser
    }

}
