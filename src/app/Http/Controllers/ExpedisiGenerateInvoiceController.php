<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Expedisi;
use App\Models\Rekening;
use App\Models\Signature;
use App\Models\Mcustomer;
use App\Models\Kwitansi;
use App\Models\Arh;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Carbon\Carbon;
use Exception;
use App\Exports\InvoiceExport;
use Maatwebsite\Excel\Facades\Excel;

class ExpedisiGenerateInvoiceController extends Controller
{
    public function index()
    {
        return view('expedisiInvoieGen.expedisi-invoice-gen');
    }

    public function getDataInvoiceGen(Request $request){
        $query = Expedisi::select([
            'INVOICE',
            'TGLINVOICE',
            'CUSTOMER',
            'GRAND',
            'PIUTANG',
            'GB',
            'NOMUAT',
            'TGLMUAT',
            'NOSJ'
        ])
        ->where('JENIS', 'EKS')
        ->whereNotNull('NOMUAT')
        ->where('NOMUAT', '!=', '');
        // =============================
        // FILTER STATUS INVOICE
        // =============================
        if ($request->status_invoice == 'belum') {
            $query->where(function($q){
                $q->whereNull('INVOICE')
                ->orWhere('INVOICE', '');
            });
        }

        if ($request->status_invoice == 'sudah') {
            $query->where(function($q){
                $q->whereNotNull('INVOICE')
                ->where('INVOICE', '!=', '');
            })
            // ->where(function($q){
            //     $q->whereNull('kwt')
            //     ->orWhere('kwt', '');
            // })
            ->where('GRAND', '>', 0);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->editColumn('TGLINVOICE', function ($row) {
                return $row->TGLINVOICE
                    ? \Carbon\Carbon::parse($row->TGLINVOICE)->format('d-m-Y')
                    : '-';
            })

            ->editColumn('GRAND', function ($row) {
                return number_format($row->GRAND ?? 0, 0, ',', '.');
            })

            ->addColumn('gb', function($row){
                return $row->GB ?: '-';
            })

            ->addColumn('action', function ($row) use ($request) {

                // =============================
                // SUDAH INVOICE
                // =============================
                if ($request->status_invoice == 'sudah') {
                    return '
                        <button
                            class="btn btn-sm btn-primary btn-buat-invoice"
                            data-surjal="'.$row->NOSJ.'">
                            Edit
                        </button>
                    ';
                }

                // =============================
                // BELUM INVOICE
                // =============================
                return '
                    <button
                        class="btn btn-sm btn-success btn-buat-invoice"
                        data-surjal="'.$row->NOSJ.'">
                        Buat Invoice
                    </button>
                ';
            })

            ->rawColumns(['action'])
            ->make(true);
    }

    public function showInvoiceGabung($surjalNo){
        // =====================================
        // 1. AMBIL SJ YANG DIPILIH
        // =====================================
        $row = Expedisi::where('NOSJ', $surjalNo)->first();
        if (!$row) {

            return response()->json([
                'status' => false,
                'message' => 'Data SJ tidak ditemukan'
            ]);
        }
        // =====================================
        // 2. CEK APAKAH PUNYA GB
        // =====================================
        if (!empty($row->GB)) {
            // 🔥 kalau punya GB ambil semua member GB
            $rows = Expedisi::where('GB', $row->GB)
                ->orderBy('NOSJ')
                ->get();
        } else {
            // 🔥 single SJ
            $rows = collect([$row]);
        }
        // =====================================
        // 3. CARI MASTER
        // =====================================
        $master = $rows->firstWhere('GRAND', '>', 0)
                ?? $rows->first();
        if (!$master) {
            return response()->json([
                'status' => false,
                'message' => 'Data master tidak ditemukan'
            ]);
        }

        // =====================================
        // 4. DETAIL SJ
        // =====================================
        $details = $rows->sortBy('NOSJ');
        // =====================================
        // 5. ARH
        // =====================================
        $arh = null;
        if (!empty($master->INVOICE)) {
            $arh = Arh::where('NOFAKTUR', $master->INVOICE)
                ->first();
        }
        // =====================================
        // 6. TOP KREDIT CUSTOMER
        // =====================================
        $kodeCustomer = $master->CUSTOMER_KODE ?? null;
        $topKredit = 0;
        if ($kodeCustomer) {
            $customer = Mcustomer::where('kode_cus', $kodeCustomer)
                ->first();
            $topKredit = $customer->TOPKREDIT ?? 0;
        }

        // =====================================
        // 7. HITUNG JATUH TEMPO
        // =====================================
        $tglInvoice = $master->TGLINVOICE
            ? Carbon::parse($master->TGLINVOICE)
            : now();
        $tglJatuhTempo = $tglInvoice
            ->copy()
            ->addDays((int) $topKredit);
        // =====================================
        // 8. RESPONSE
        // =====================================
        return response()->json([
            'status' => true,
            'data' => [
                // =============================
                // IDENTITAS
                // =============================
                'gb'           => $master->GB ?? '',

                'invoice'      => $master->INVOICE ?? '',
                'tgl_invoice'  => $master->TGLINVOICE,

                'customer'     => $master->CUSTOMER,
                'nomor_muat'   => $master->NOMUAT ?? '',
                'tgl_muat'     => $master->TGLMUAT ?? '',
                'kendaraan'    => $master->NAMA_KENDARAAN ?? '',

                // =============================
                // NILAI INVOICE (MASTER)
                // =============================
                'sub_total'    => $master->HARGA ?? 0,
                'disc_persen'  => $master->DISC ?? 0,
                'disc_rp'      => $master->NDISC ?? 0,
                'd_charge'     => $master->DC ?? 0,
                'total'        => $master->TOTAL ?? 0,
                'ppn'          => $master->PPN ?? 0,
                'grand'        => $master->GRAND ?? 0,

                // =============================
                // PEMBAYARAN
                // =============================
                'bayar'        => $master->BAYAR ?? 0,
                'piutang'      => (int) ceil($master->PIUTANG ?? 0),

                // =============================
                // DETAIL SJ GABUNGAN
                // =============================
                'nomor_sj'     => $details->pluck('NOSJ')->implode(', '),
                'master_nosj' => $master->NOSJ,

                // =============================
                // DATA ARH
                // =============================
                'tgl_jt'       => $arh->TGLJT ?? $tglJatuhTempo->format('Y-m-d'),
                'piutang_arh'  => $arh->PIUTANG ?? 0,
                'saldo'        => $arh->SALDO ?? 0,
            ]
        ]);
    }

    public function prosesInvoiceStore(Request $request){
        try {
            $invoiceNo = null;
            DB::transaction(function () use ($request, &$invoiceNo) {
                // =============================
                // 1. VALIDASI BASIC
                // =============================
                $nosj = $request->nosj;
                if (!$nosj) {
                    throw new \Exception('Nomor SJ tidak ditemukan');
                }
                // parsing nominal
                $bayar = (int) preg_replace('/[^0-9]/', '', $request->bayar);
                $top   = (int) preg_replace('/[^0-9]/', '', $request->top);
                $tglJtp = $request->tgl_jtp;
                // =============================
                // 2. AMBIL SJ YANG DIPILIH
                // =============================
                $row = Expedisi::where('NOSJ', $nosj)
                    ->lockForUpdate()
                    ->first();
                if (!$row) {
                    throw new \Exception('Data SJ tidak ditemukan');
                }
                // =============================
                // 3. CEK APAKAH PUNYA GB
                // =============================
                if (!empty($row->GB)) {
                    // 🔥 ambil semua member GB
                    $targetRows = Expedisi::where('GB', $row->GB)
                        ->lockForUpdate()
                        ->orderBy('NOSJ')
                        ->get();
                } else {
                    // 🔥 single SJ
                    $targetRows = collect([$row]);
                }
                // =============================
                // 4. VALIDASI SUDAH INVOICE?
                // =============================
                foreach ($targetRows as $item) {
                    if (!is_null($item->INVOICE) && trim($item->INVOICE) !== '') {
                        throw new \Exception("SJ {$item->NOSJ} sudah memiliki invoice");
                    }
                }
                // =============================
                // 5. CARI MASTER
                // =============================
                $master = $targetRows->firstWhere('GRAND', '>', 0)
                        ?? $targetRows->first();
                if (!$master) {
                    throw new \Exception('Master invoice tidak ditemukan');
                }
                // =============================
                // 6. AMBIL TOTAL GRAND
                // =============================
                $grand = (int) ($master->GRAND ?? 0);
                // =============================
                // 7. VALIDASI BAYAR
                // =============================
                if ($bayar < 0) {
                    throw new \Exception('Nominal bayar tidak valid');
                }
                if ($bayar > $grand) {
                    throw new \Exception('Bayar tidak boleh lebih besar dari total');
                }
                // =============================
                // 8. HITUNG PIUTANG
                // =============================
                $piutang = $grand - $bayar;
                // =============================
                // 9. GENERATE INVOICE
                // =============================
                $invoiceNo = $this->generateInvoiceOnline();
                // =============================
                // 10. UPDATE EXPEDISI
                // =============================
                foreach ($targetRows as $item) {
                    $item->INVOICE    = $invoiceNo;
                    $item->TGLINVOICE = now();
                    $item->STS        = 'INVOICE';
                    $item->TGLJT      = $tglJtp;
                    // ====================================
                    // HANYA MASTER YANG SIMPAN PEMBAYARAN
                    // ====================================
                    if ($item->id == $master->id) {
                        $item->BAYAR   = $bayar;
                        $item->PIUTANG = $piutang;
                    } else {
                        // DETAIL GB
                        $item->BAYAR   = 0;
                        $item->PIUTANG = 0;
                    }
                    $item->USERINV = auth()->user()->user_id. '-' .now()->format('d-m-Y h:i:s A');
                    $item->save();
                }
                // =============================
                // 11. INSERT ARH
                // Hanya Jika Piutang Tidak 0
                // =============================
                if ($piutang > 0) {
                    Arh::create([
                        'NOFAKTUR'   => $invoiceNo,
                        'TGLFAKTUR'  => now(),
                        'CUSTOMER'   => $master->CUSTOMER,
                        'PIUTANG'    => $piutang,
                        'DISCOUNT'   => $master->NDISC ?? 0,
                        // pembayaran dihandle modul lain
                        // 'BAYAR' => 0,
                        'SALDO'      => $top,
                        'TGLJT'      => $tglJtp,
                        'CABANG'     => $master->CABANG ?? '',
                        'KETERANGAN' => 'INVOICE DARI EXPEDISI',
                        'USER'       => auth()->user()->user_id,
                    ]);
                }
            });

            return response()->json([

                'status'    => true,
                'message'   => 'Invoice berhasil dibuat',
                'invoiceNo' => $invoiceNo
            ]);

        } catch (\Throwable $e) {

            return response()->json([

                'status'  => false,
                'message' => $e->getMessage()

            ], 500);
        }
    }

    public function updateInvoice(Request $request){
        try {
            $invoice = $request->invoice;
            DB::transaction(function () use ($request, $invoice) {
                if (!$invoice) {
                    throw new \Exception('Invoice tidak ditemukan');
                }

                // parsing angka
                $bayar = (int) preg_replace('/[^0-9]/', '', $request->bayar);
                $top   = (int) preg_replace('/[^0-9]/', '', $request->top);
                $tglJtp = $request->tgl_jtp;

                // =============================
                // 1. Ambil MASTER dari EXPEDISI
                // =============================
                $rows = Expedisi::where('INVOICE', $invoice)->get();

                $master = $rows->firstWhere('GRAND', '>', 0)
                        ?? $rows->first();

                if (!$master) {
                    throw new \Exception('Data invoice tidak ditemukan');
                }

                $grand = (int) $master->GRAND;

                // =============================
                // 2. VALIDASI
                // =============================
                if ($bayar < 0) {
                    throw new \Exception('Nominal bayar tidak valid');
                }

                // if ($bayar > $grand) {
                //     throw new \Exception('Bayar tidak boleh lebih besar dari total');
                // }

                // =============================
                // 3. HITUNG PIUTANG DAN UPDATE EXPEDISI BAYAR DAN PIUTANG
                // =============================
                $piutang = $grand - $bayar;

                foreach ($rows as $row) {
                    $row->TGLJT = $tglJtp;
                    if ($row->id == $master->id) {
                        $row->BAYAR   = $bayar;
                        $row->PIUTANG = $piutang;
                    } else {
                        $row->BAYAR   = 0;
                        $row->PIUTANG = 0;
                    }
                    $row->save();
                }
                // =============================
                // 4. UPDATE ARH
                // =============================
                if ($piutang > 0) {
                    // kalau masih ada piutang
                    Arh::updateOrCreate(
                        ['NOFAKTUR' => $invoice],
                        [
                            'TGLFAKTUR'  => $master->TGLINVOICE ?? now(),
                            'CUSTOMER'   => $master->CUSTOMER,
                            'PIUTANG'    => $piutang,
                            'SALDO'      => $top,
                            'TGLJT'      => $tglJtp,
                            'CABANG'     => $master->CABANG ?? '',
                            'USER_UPDATE'=> auth()->user()->user_id,
                            'updated_at' => now()
                        ]
                    );
                } else {
                    // kalau lunas hapus dari ARH
                    Arh::where('NOFAKTUR', $invoice)->delete();
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'Invoice berhasil diupdate',
                'invoiceNo' => $invoice
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $request->validate([
            'tanggal_dari'   => 'required|date',
            'tanggal_sampai' => 'required|date|after_or_equal:tanggal_dari',
            'filter_inv_gen' => 'required'
        ]);

        $tanggalDari   = $request->tanggal_dari;
        $tanggalSampai = $request->tanggal_sampai;
        $status        = $request->filter_inv_gen;

        $filename = 'laporan_invoice_expedisi_' .
            $status . '_' .
            $tanggalDari . '_sd_' .
            $tanggalSampai . '.xlsx';

        return Excel::download(
            new InvoiceExport(
                $tanggalDari,
                $tanggalSampai,
                $status
            ),
            $filename
        );
    }

    // ############################ Edit Invoice ####################################
    public function indexEdit()
    {
        return view('expedisiInvoieGen.expedisi-invoice-edit');
    }

    public function tableEdit(Request $request)
    {
        $query = Expedisi::select([
                'INVOICE',
                'TGLINVOICE',
                'CUSTOMER',
                'GRAND',
                'PIUTANG',
                'GB',
                'NOSJ',
                'TGLJT',
                'KETERANGAN'
            ])
        ->where('JENIS', 'EKS')
        ->whereNotNull('INVOICE')
        ->where('INVOICE', '!=', '')
        ->where('GRAND', '>', 0)
        ->orderByDesc('TGLINVOICE')
        ->orderByDesc('INVOICE');

        if ($request->tanggal_dari) {
            $query->whereDate('TGLINVOICE', '>=', $request->tanggal_dari);
        }
        if ($request->tanggal_sampai) {
            $query->whereDate('TGLINVOICE', '<=', $request->tanggal_sampai);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('TGLINVOICE', function ($row) {
                return $row->TGLINVOICE ? \Carbon\Carbon::parse($row->TGLINVOICE)->format('d-m-Y') : '-';
            })
            ->editColumn('GRAND', function ($row) {
                return number_format($row->GRAND ?? 0, 0, ',', '.');
            })
            ->editColumn('PIUTANG', function ($row) {
                return number_format($row->PIUTANG ?? 0, 0, ',', '.');
            })
            ->addColumn('bayar', function ($row) {
                $bayar = ($row->GRAND ?? 0) - ($row->PIUTANG ?? 0);
                return number_format($bayar, 0, ',', '.');
            })
            ->addColumn('gb', function ($row) {
                return $row->GB ?: '-';
            })
            ->addColumn('aksi', function ($row) {
                return '<button class="btn btn-warning btn-sm btn_edit_invoice_eks" data-invoice="'.$row->INVOICE.'"><i class="bx bx-edit-alt"></i></button>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function showEditInvoice($invoice)
    {
        // =====================================
        // AMBIL SEMUA BARIS INVOICE
        // =====================================
        $rows = Expedisi::where('INVOICE', $invoice)
            ->orderBy('NOSJ')
            ->get();
        if ($rows->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Invoice tidak ditemukan'
            ]);
        }

        // =====================================
        // MASTER
        // =====================================
        $master = $rows->firstWhere('GRAND', '>', 0)
            ?? $rows->first();

        // =====================================
        // ARH
        // =====================================
        $arh = Arh::where(
            'NOFAKTUR',
            $master->INVOICE
        )->first();

        // =====================================
        // DETAIL
        // =====================================
        $details = $rows->map(function ($row) {
            return [
                'nosj'    => $row->NOSJ,
                'jumlah'  => $row->JUMLAH,
                'hargaaw' => $row->hargaaw,
                'total'   => $row->hargaaw
            ];
        });
        return response()->json([
            'status' => true,
            'master' => [

                // =====================================
                // IDENTITAS
                // =====================================
                'invoice'      => $master->INVOICE,
                'customer'     => $master->CUSTOMER,
                'kendaraan'    => $master->NAMA_KENDARAAN ?? '',
                'driver'       => $master->DRIVER ?? '',
                'nomuat'       => $master->NOMUAT ?? '',
                'tgl_invoice'  => $master->TGLINVOICE,
                'tgl_jt'       => $arh->TGLJT ?? $master->TGLJT,
                // =====================================
                // INFO EXPEDISI
                // =====================================
                'wilayah'      => $master->WILAYAH ?? '',
                'item'         => $master->PESANAN ?? '',
                'rute'         => $master->rute ?? '',
                // =====================================
                // NILAI
                // =====================================
                'jumlah'       => $master->JUMLAH ?? 0,
                'harga'        => $master->HARGA ?? 0,
                'total'        => $master->TOTAL ?? 0,
                'diskon'       => $master->DISC ?? 0,
                'del_charge'   => $master->DC ?? 0,
                'ppn'          => $master->PPN ?? 0,
                'grand'        => $master->GRAND ?? 0,
                // =====================================
                // PEMBAYARAN
                // =====================================
                'bayar'        => $master->BAYAR ?? 0,
                'piutang'      => ($master->GRAND ?? 0)-($master->BAYAR ?? 0),
                'keterangan'   => $master->KETERANGAN
            ],
            'details' => $details
        ]);
    }

    public function updateEditInvoice(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $invoice = $request->invoice;
                if (!$invoice) {
                    throw new \Exception('Invoice tidak ditemukan');
                }

                $rows = Expedisi::where('INVOICE', $invoice)->get();
                if ($rows->isEmpty()) {
                    throw new \Exception('Data invoice tidak ditemukan');
                }

                $master = $rows->firstWhere('GRAND', '>', 0) ?? $rows->first();
                if (!$master) {
                    throw new \Exception('Data master tidak ditemukan');
                }

                $harga = (float) str_replace(',', '', $request->harga);
                $discPersen = (float) ($request->disc ?? 0);
                $delCharge = (float) str_replace(',', '', $request->del_charge);
                $ppnPersen = (float) ($request->ppn ?? 0);
                $jumlah = (float) $request->jumlah;

                $subTotal = $jumlah * $harga;
                $ndisc = round($subTotal * ($discPersen / 100),0);

                $total = round($subTotal - $ndisc,0);
                $ppnNominal = round($total * ($ppnPersen / 100),0);
                $grand = round($total + $ppnNominal + $delCharge,0);

                $bayar = (float) ($master->BAYAR ?? 0);
                if ($grand < $bayar) {
                    throw new \Exception('Grand tidak boleh lebih kecil dari pembayaran yang sudah diterima');
                }
                $piutang = $grand - $bayar;

                $master->update([
                    'HARGA'      => $harga,
                    'DISC'       => $discPersen,
                    'NDISC'      => $ndisc,
                    'DC'         => $delCharge,
                    'TOTAL'      => $total,
                    'PPN'        => $ppnPersen,
                    'GRAND'      => $grand,
                    'PIUTANG'    => $piutang,
                    'TGLJT'      => $request->tgl_jt,
                    'KETERANGAN' => $request->keterangan,
                    'updated_at' => now()
                ]);

                $arh = Arh::where('NOFAKTUR', $invoice)->first();

                if ($piutang > 0) {
                    if (!$arh) {
                        Arh::create([
                            'NOFAKTUR'   => $invoice,
                            'TGLFAKTUR'  => $master->TGLINVOICE,
                            'TGLJT'      => $request->tgl_jt,
                            'CUSTOMER'   => $master->CUSTOMER,
                            'PIUTANG'    => $piutang,
                            'BAYAR'      => $bayar,
                            'SALDO'      => $piutang,
                            'KETERANGAN' => $request->keterangan,
                            'USER'       => auth()->user()->name ?? 'SYSTEM',
                            'CABANG'     => $master->area_id ?? '',
                        ]);
                    } else {
                        $arh->update([
                            'TGLJT'       => $request->tgl_jt,
                            'PIUTANG'     => $piutang,
                            'BAYAR'       => $bayar,
                            'SALDO'       => $piutang,
                            'KETERANGAN'  => $request->keterangan,
                            'USER_UPDATE' => auth()->user()->name ?? 'SYSTEM'
                        ]);
                    }
                } else {
                    if ($arh) {
                        $arh->delete();
                    }
                }

                $kwitansi = Kwitansi::where('FDOK_TRANS', $invoice)->first();
                if ($kwitansi) {
                    $kwitansi->update([
                        'TOTAL' => $grand,
                        'PPN'   => $ppnPersen,
                        'DISC'  => $discPersen,
                        'NDISC' => $ndisc
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
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
}
