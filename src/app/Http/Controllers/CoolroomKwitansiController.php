<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Coolroom;
use App\Models\Rekening;
use App\Models\Signature;
use App\Models\Mcustomer;
use App\Models\Arh;
use App\Models\Kwitansi;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Carbon\Carbon;
use Exception;

class CoolroomKwitansiController extends Controller
{
    public function index()
    {
        return view('coolroomKwitansi.coolroom-kwt');
    }

    public function getDataKwitansi(Request $request)
    {
        $query = Coolroom::select([
                'INVOICE',
                'TGLINVOICE',
                'CUSTOMER',
                'GRAND',
                'PIUTANG',
                'kwt'
            ])
            ->whereNotNull('INVOICE')
            ->where('INVOICE', '!=', '')
            ->where('GRAND', '>', 0)
            ->orderBy('INVOICE')
            ->orderByDesc('GRAND');

        if ($request->status_kwt == 'belum') {
            $query->where(function ($q) {
                $q->whereNull('kwt')
                ->orWhere('kwt', '');
            });
        }

        if ($request->status_kwt == 'sudah') {
            $query->whereNotNull('kwt')
                ->where('kwt', '!=', '');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('TGLINVOICE', function ($row) {
                return $row->TGLINVOICE
                    ? \Carbon\Carbon::parse($row->TGLINVOICE)
                        ->format('d-m-Y')
                    : '-';
            })
            ->editColumn('GRAND', function ($row) {
                return number_format(
                    $row->GRAND ?? 0,
                    0,
                    ',',
                    '.'
                );
            })
            ->editColumn('PIUTANG', function ($row) {
                return number_format(
                    $row->PIUTANG ?? 0,
                    0,
                    ',',
                    '.'
                );
            })
            ->addColumn('action', function ($row) use ($request) {
                if ($request->status_kwt == 'belum') {
                    return '
                        <button
                            class="btn btn-sm btn-success btn-proses-kwt-coolroom"
                            data-invoice="'.$row->INVOICE.'">
                            Proses
                        </button>
                    ';
                }
                return '
                    <a
                        href="'.route('coolroomKwt.pdf', $row->INVOICE).'"
                        target="_blank"
                        class="btn btn-sm btn-primary">
                        PDF
                    </a>
                    <button
                        class="btn btn-sm btn-danger btn-delete-kwt-coolroom"
                        data-kwt="'.$row->kwt.'">
                        Delete
                    </button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function prosesKwitansi(Request $request)
    {
        try {
            $invoice = $request->invoice;
            DB::transaction(function () use ($invoice, &$kwt) {
                // =====================================
                // VALIDASI
                // =====================================
                if (!$invoice) {
                    throw new \Exception(
                        'Invoice tidak ditemukan'
                    );
                }
                // =====================================
                // AMBIL DATA COOLROOM
                // =====================================
                $rows = Coolroom::where('INVOICE', $invoice)
                    ->lockForUpdate()
                    ->get();
                if ($rows->isEmpty()) {
                    throw new \Exception(
                        'Data invoice tidak ditemukan'
                    );
                }
                // =====================================
                // CEK SUDAH KWITANSI?
                // =====================================
                $alreadyKwt = $rows->first(function ($row) {
                    return !empty($row->KWT);
                });
                if ($alreadyKwt) {
                    throw new \Exception(
                        'Invoice sudah memiliki kwitansi'
                    );
                }
                // =====================================
                // GENERATE NOMOR KWITANSI
                // =====================================
                $kwt = $this->generateKW();
                // =====================================
                // MASTER
                // =====================================
                $master = $rows->firstWhere('GRAND', '>', 0)
                        ?? $rows->first();
                if (!$master) {
                    throw new \Exception(
                        'Master invoice tidak ditemukan'
                    );
                }
                // =====================================
                // NAMA CUSTOMER
                // =====================================
                $namaCust = $master->CUSTOMER;
                // =====================================
                // UPDATE COOLROOM
                // =====================================
                foreach ($rows as $row) {
                    $row->KWT   = $kwt;
                    $row->TGLKW = now();
                    $row->save();
                }
                // =====================================
                // INSERT TABEL KWITANSI
                // =====================================
                Kwitansi::create([
                    'NOKWT' => $kwt,
                    'TGL' => now(),
                    'FDOK_TRANS' => $invoice,
                    'TGL_TRANS' => $master->TGLINVOICE,
                    'CUSTOMER' => $master->CUSTOMER,
                    'NOSJ' => $rows
                        ->pluck('NOSJ')
                        ->implode(','),
                    'FKETERANG' =>
                        'SEWA RUANG DINGIN PADA '
                        .$namaCust
                        .', INVOICE : '
                        .$invoice,
                    'FNAMA' => $namaCust,
                    'FNIL_DOK' => $master->GRAND ?? 0,
                    'USERINPUT' => auth()->user()->user_id,
                    'TOTAL' => $master->TOTAL ?? 0,
                    'PPN' => $master->PPN ?? 0,
                    'DISC' => $master->DISC ?? 0,
                    'NDISC' => $master->NDISC ?? 0,
                    'JENIS' => 'COL',
                ]);
            });
            return response()->json([
                'status'  => true,
                'message' => 'Kwitansi berhasil diproses',
                'pdf_url' => route(
                'coolroomKwt.pdf',$invoice
            )
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteKwitansi(Request $request)
    {
        try {

            $kwt = $request->kwt;

            DB::transaction(function () use ($kwt) {

                // =====================================
                // VALIDASI
                // =====================================
                if (!$kwt) {
                    throw new \Exception(
                        'Nomor kwitansi tidak ditemukan'
                    );
                }

                // =====================================
                // AMBIL DATA COOLROOM
                // =====================================
                $rows = Coolroom::where('KWT', $kwt)
                    ->lockForUpdate()
                    ->get();

                if ($rows->isEmpty()) {
                    throw new \Exception(
                        'Data kwitansi tidak ditemukan'
                    );
                }

                // =====================================
                // RESET KWITANSI
                // =====================================
                foreach ($rows as $row) {

                    $row->KWT   = null;
                    $row->TGLKW = null;
                    $row->save();
                }

                // =====================================
                // DELETE TABEL KWITANSI
                // =====================================
                Kwitansi::where('NOKWT', $kwt)
                    ->where('JENIS', 'COL')
                    ->delete();
            });

            return response()->json([
                'status'  => true,
                'message' => 'Kwitansi berhasil dibatalkan'
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function pdfInvoiceKwitansi($invoice)
    {
        // ==========================================
        // MASTER COOLROOM
        // ==========================================
        $master = Coolroom::where('INVOICE', $invoice)
            ->where('GRAND', '>', 0)
            ->firstOrFail();
        // ==========================================
        // DETAIL COOLROOM
        // ==========================================
        $details = Coolroom::where('INVOICE', $invoice)
            ->orderBy('NOSJ')
            ->get();
        // ==========================================
        // PEMBAYARAN ARH
        // ==========================================
        $arh = Arh::where('NOFAKTUR', $invoice)
            ->first();
        // ==========================================
        // SIGNATURE
        // ==========================================
        $signature = Signature::orderByDesc('id')
            ->first();
        // ==========================================
        // RENDER VIEW PDF
        // ==========================================
        $html = view(
            'coolroomKwitansi.coolroom-kwt-pdf',
            compact(
                'master',
                'details',
                'arh',
                'signature'
            )
        )->render();

        // ==========================================
        // MPDF
        // ==========================================
        $tempPath = storage_path('app/mpdf-temp');

        if (!is_dir($tempPath)) {
            mkdir($tempPath, 0775, true);
        }
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',

            'margin_top' => 20,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_right' => 15,
            'tempDir' => $tempPath,
        ]);

        $mpdf->WriteHTML($html);

        // ==========================================
        // OUTPUT PDF
        // ==========================================
        return response(
            $mpdf->Output(
                'Kwitansi-Coolroom-'.$invoice.'.pdf',
                'I'
            )
        )->header(
            'Content-Type',
            'application/pdf'
        );
    }

    private function generateKW()
    {
        $year = now()->format('Y');

        $last = Kwitansi::where('NOKWT', 'like', 'KW'.$year.'%')
            ->lockForUpdate()
            ->orderByDesc('NOKWT')
            ->value('NOKWT');

        if (!$last) {
            return 'KW' . $year . '0000001';
        }

        $number = (int) substr($last, 7);
        $number++;

        return 'KW' . $year . str_pad($number, 7, '0', STR_PAD_LEFT);
    }
}
