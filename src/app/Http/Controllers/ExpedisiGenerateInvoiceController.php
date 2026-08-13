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
            $arh = Arh::where('NOFAKTUR', $master->INVOICE)->first();
            if ($arh) {
                $bayar = (float) ($arh->BAYAR ?? 0);
                if ($bayar > 0) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invoice sudah memiliki pembayaran. Silakan hubungi admin.'
                    ]);
                }
            }
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
                'tgl_jt'       => $master->TGLJT ?? $tglJatuhTempo->format('Y-m-d'),
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
                // CEK PEMBAYARAN ARH
                // =============================
                $arh = null;
                if (!empty($master->INVOICE)) {
                    $arh = Arh::where('NOFAKTUR', $master->INVOICE)->first();
                    if ($arh && (float)$arh->BAYAR > 0) {throw new \Exception('Invoice sudah memiliki pembayaran. Silakan hubungi admin.');}
                }
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
        $customer      = $request->customer_invoice_exp;

        $filename = 'laporan_invoice_expedisi_' .
            $status . '_' .
            $tanggalDari . '_sd_' .
            $tanggalSampai . '.xlsx';

        return Excel::download(
            new InvoiceExport(
                $tanggalDari,
                $tanggalSampai,
                $status,
                $customer
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
        $arh = null;
        if (!empty($master->INVOICE)) {
            $arh = Arh::where('NOFAKTUR', $master->INVOICE)->first();
            if ($arh) {
                $bayar = (float) ($arh->BAYAR ?? 0);
                if ($bayar > 0) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invoice sudah memiliki pembayaran. Silakan hubungi admin.'
                    ]);
                }
            }
        }

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
                'customer_kode'     => $master->CUSTOMER_KODE,
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

                // =============================
                // CEK PEMBAYARAN ARH
                // =============================
                $arh = null;
                if (!empty($master->INVOICE)) {
                    $arh = Arh::where('NOFAKTUR', $master->INVOICE)->first();
                    if ($arh && (float)$arh->BAYAR > 0) {throw new \Exception('Invoice sudah memiliki pembayaran. Silakan hubungi admin.');}
                }

                $jumlah      = (float) $request->jumlah;
                $harga       = (float) str_replace(',', '', $request->harga);
                $discPersen  = (float) ($request->disc ?? 0);
                $delCharge   = (float) str_replace(',', '', $request->del_charge);
                $ppnPersen   = (float) ($request->ppn ?? 0);

                $subTotal = round($jumlah * $harga);
                $ndisc = round($subTotal * ($discPersen / 100));
                $total = round($subTotal - $ndisc);
                $ppnNominal = round($total * ($ppnPersen / 100));
                $grand = round($total + $ppnNominal + $delCharge);

                $bayar = round((float) str_replace(',', '', $request->bayar));
                if ($grand < $bayar) {
                    throw new \Exception('Grand tidak boleh lebih kecil dari pembayaran yang sudah diterima');
                }
                $piutang = round($grand - $bayar);

                $master->update([
                    'PESANAN'    => $request->item,
                    'rute'       => $request->rute,
                    'HARGA'      => $harga,
                    'DISC'       => $discPersen,
                    'NDISC'      => $ndisc,
                    'DC'         => $delCharge,
                    'TOTAL'      => $total,
                    'PPN'        => $ppnPersen,
                    'GRAND'      => $grand,
                    'BAYAR'      => $bayar,
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
                            'BAYAR'      => 0,
                            // 'SALDO'      => $piutang,
                            'KETERANGAN' => $request->keterangan,
                            'USER'       => auth()->user()->name ?? 'SYSTEM',
                            'CABANG'     => $master->area_id ?? '',
                        ]);
                    } else {
                        $arh->update([
                            'TGLJT'       => $request->tgl_jt,
                            'PIUTANG'     => $piutang,
                            // 'SALDO'       => $piutang,
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

    public function printInvoiceText($invoiceNo)
    {
        $rows = Expedisi::where('INVOICE', $invoiceNo)->orderBy('NOSJ')->get();
        if ($rows->isEmpty()) abort(404);

        $master = $rows->first();

        // ====== ANGKA YANG PERLU DITES DI KANTOR ======
        $LINE_WIDTH     = 130; // lebar karakter per baris (udah fix, terbukti pas)
        $LEFT_MARGIN    = 3;   // spasi kosong di kiri biar gak kena lubang kertas
        $LINES_PER_PAGE = 30;  // jumlah baris yg muat di 1 lembar (~14cm @ 6 LPI)
        // ================================================

        $half = intdiv($LINE_WIDTH, 2);
        $namaBarangWidth = max(10, $LINE_WIDTH - 48);
        $alamatWrapWidth = max(20, $LINE_WIDTH - 20);

        $customer = Mcustomer::where('CUSTOMER', $master->CUSTOMER_KODE)->first();
        $kepada = strtoupper($customer->NAMACUST ?? '-');
        $up     = strtoupper($customer->KONTAK ?? '-');
        $alamat = strtoupper($customer->ALAMAT1 ?? '-');

        $rekening = Rekening::where('AKTIF', 1)->first();
        $bank    = $rekening->BANK ?? '-';
        $norek   = $rekening->NOREK ?? '-';
        $namaRek = $rekening->NAMA ?? '-';

        $subtotal = (float) ($master->GRAND ?? 0);
        $dibayar  = (float) ($master->BAYAR ?? 0);
        $saldo    = (float) ($master->PIUTANG ?? 0);

        // ---------- GABUNG SEMUA NOMOR SJ ----------
        $nomorSjList = $rows->pluck('NOSJ')
            ->filter(fn($v) => !empty(trim((string) $v)))
            ->unique()
            ->implode(',');

        // ---------- HEADER PENUH (halaman pertama saja) ----------
        $headerFull = [];
        $headerFull[] = str_pad('PT. LINTAS MITRA ANUGERAH SEJATI', $LINE_WIDTH, ' ', STR_PAD_BOTH);
        $headerFull[] = str_pad('COLD CHAIN DISTRIBUTION & STORAGE', $LINE_WIDTH, ' ', STR_PAD_BOTH);
        $headerFull[] = str_pad('Jl. Raya Sempidi No.9 Badung - Bali', $LINE_WIDTH, ' ', STR_PAD_BOTH);
        $headerFull[] = str_pad('Telp. (0361) 8947610', $LINE_WIDTH, ' ', STR_PAD_BOTH);
        $headerFull[] = str_repeat('=', $LINE_WIDTH);
        $headerFull[] = str_pad('INVOICE', $LINE_WIDTH, ' ', STR_PAD_BOTH);
        $headerFull[] = str_repeat('=', $LINE_WIDTH);

        $headerFull[] = sprintf("%-{$half}s %-{$half}s",
            'NOMOR  : ' . $master->INVOICE,
            'TANGGAL : ' . date('d-m-Y', strtotime($master->TGLINVOICE))
        );
        // ---------- BARIS BARU: NOMOR SJ ----------
        $headerFull[] = sprintf("%-{$half}s %-{$half}s",
            'NOMOR SJ : ' . substr($nomorSjList, 0, $half - 12),
            ''
        );
        $headerFull[] = sprintf("%-{$half}s %-{$half}s",
            'KEPADA : ' . substr($kepada, 0, $half - 10),
            !empty($master->TGLJT) ? 'TGL JT : ' . date('d-m-Y', strtotime($master->TGLJT)) : ''
        );
        $headerFull[] = sprintf("%-{$half}s %-{$half}s",
            'UP     : ' . substr($up, 0, $half - 10),
            'CETAK : ' . now()->format('d-m-Y H:i')
        );

        $alamatWrap = explode("\n", wordwrap($alamat, $alamatWrapWidth, "\n"));
        foreach ($alamatWrap as $i => $alamatRow) {
            $headerFull[] = $i === 0 ? 'ALAMAT : ' . $alamatRow : '         ' . $alamatRow;
        }
        $headerFull[] = '';
        $headerFull[] = 'JUMLAH SJ : ' . $rows->count();
        $headerFull[] = str_repeat('=', $LINE_WIDTH);

        // ---------- HEADER TABEL ----------
        $tableHeader = [];
        $tableHeader[] = sprintf("%-4s %-{$namaBarangWidth}s %-10s %14s", 'NO', 'NAMA BARANG', 'QTY', 'TOTAL');
        $tableHeader[] = str_repeat('-', $LINE_WIDTH);

        // ---------- DETAIL: DIRINGKAS JADI 1 BARIS ----------
        // Nama barang diambil dari baris master (baris pertama), bukan per-SJ.
        // JUMLAH dan TOTAL dijumlahkan dari semua baris SJ terkait invoice ini.
        $namaBarang = trim($master->PESANANGB) !== '' ? $master->PESANANGB : $master->PESANAN;
        $totalQty   = (float) $rows->sum('JUMLAH');
        $totalRow   = (float) $rows->sum('TOTAL');
        $qtyText    = floor($totalQty) == $totalQty
            ? number_format($totalQty, 0)
            : rtrim(rtrim(number_format($totalQty, 3, '.', ''), '0'), '.');

        $detailLines = [];
        $detailLines[] = sprintf(
            "%-4s %-{$namaBarangWidth}s %-10s %14s",
            1,
            substr($namaBarang, 0, $namaBarangWidth),
            $qtyText . ' KG',
            number_format($totalRow, 0, ',', '.')
        );

        // ---------- FOOTER (rekening + total + ttd, cuma halaman terakhir) ----------
        $footer = [];
        $footer[] = str_repeat('-', $LINE_WIDTH);
        $footer[] = sprintf("%-{$half}s %-{$half}s", 'BANK   : ' . $bank, 'SUB TOTAL : ' . number_format($subtotal, 0, ',', '.'));
        $footer[] = sprintf("%-{$half}s %-{$half}s", 'NO REK : ' . $norek, 'DIBAYAR   : ' . number_format($dibayar, 0, ',', '.'));
        $footer[] = sprintf("%-{$half}s %-{$half}s", 'A/N    : ' . $namaRek, 'SALDO     : ' . number_format($saldo, 0, ',', '.'));
        $footer[] = '';
        $footer[] = '';
        $footer[] = str_pad('PENERIMA', $half) . str_pad('MENGETAHUI', $half);
        $footer[] = '';
        $footer[] = '';
        $footer[] = '';
        $footer[] = str_pad('(......................)', $half) . str_pad('PT. LINTAS MITRA ANUGERAH SEJATI', $half);

        // ====================== PAGINASI ======================
        // Catatan: karena detail sekarang selalu 1 baris, praktis invoice ini
        // hampir pasti muat 1 halaman. Tapi logic paginasi tetap dipertahankan
        // untuk jaga-jaga kalau suatu saat mau kembali ke mode per-SJ.
        $overheadFirst = count($headerFull) + count($tableHeader);
        $overheadNext  = 2 + count($tableHeader); // judul lanjutan + garis
        $footerHeight  = count($footer);

        $pages = [];
        $remainingRows = $detailLines;
        while (!empty($remainingRows)) {
            $isFirstPage = empty($pages);
            $overhead = $isFirstPage ? $overheadFirst : $overheadNext;
            $capacity = max(1, $LINES_PER_PAGE - $overhead);
            $pages[] = array_splice($remainingRows, 0, $capacity);
        }
        if (empty($pages)) $pages[] = [];

        $lastOverhead = (count($pages) === 1) ? $overheadFirst : $overheadNext;
        $usedOnLastPage = $lastOverhead + count(end($pages));
        $footerNeedsOwnPage = ($LINES_PER_PAGE - $usedOnLastPage) < $footerHeight;

        $totalPages = count($pages) + ($footerNeedsOwnPage ? 1 : 0);

        // ====================== SUSUN OUTPUT ======================
        $allPagesOutput = [];
        foreach ($pages as $i => $chunk) {
            $pageNum = $i + 1;
            $lines = [];
            if ($i === 0) {
                $lines = array_merge($lines, $headerFull);
            } else {
                $lines[] = str_pad('INVOICE ' . $master->INVOICE . ' (LANJUTAN) - HAL ' . $pageNum . '/' . $totalPages, $LINE_WIDTH, ' ', STR_PAD_BOTH);
                $lines[] = str_repeat('=', $LINE_WIDTH);
            }
            $lines = array_merge($lines, $tableHeader, $chunk);

            if ($i === count($pages) - 1 && !$footerNeedsOwnPage) {
                $spaceUsedIfFooterAppended = count($lines) + $footerHeight;
                if ($spaceUsedIfFooterAppended < $LINES_PER_PAGE) {
                    $padCount = $LINES_PER_PAGE - $spaceUsedIfFooterAppended;
                    $lines = array_merge($lines, array_fill(0, $padCount, ''));
                }
                $lines = array_merge($lines, $footer);
            }
            $allPagesOutput[] = $lines;
        }

        if ($footerNeedsOwnPage) {
            $pageNum = $totalPages;
            $lines = [];
            $lines[] = str_pad('INVOICE ' . $master->INVOICE . ' (LANJUTAN) - HAL ' . $pageNum . '/' . $totalPages, $LINE_WIDTH, ' ', STR_PAD_BOTH);
            $lines[] = str_repeat('=', $LINE_WIDTH);

            $overheadHere = 2;
            $spaceUsedIfFooterAppended = $overheadHere + $footerHeight;
            if ($spaceUsedIfFooterAppended < $LINES_PER_PAGE) {
                $padCount = $LINES_PER_PAGE - $spaceUsedIfFooterAppended;
                $lines = array_merge($lines, array_fill(0, $padCount, ''));
            }
            $lines = array_merge($lines, $footer);
            $allPagesOutput[] = $lines;
        }

        // ====================== GABUNG JADI TEKS FINAL ======================
        $marginStr = str_repeat(' ', $LEFT_MARGIN);
        $pageTexts = [];
        foreach ($allPagesOutput as $pageLines) {
            $padded = array_map(fn($l) => $marginStr . $l, $pageLines);
            $pageTexts[] = implode("\r\n", $padded);
        }

        $text = implode("\r\n\x0C\r\n", $pageTexts);

        $text = iconv('UTF-8', 'CP437//TRANSLIT//IGNORE', $text);
        if ($text === false) {
            $text = implode("\r\n\x0C\r\n", $pageTexts);
        }

        $text = "\x1B\x0F" . $text . "\x12";

        return response()->json([
            'text' => $text,
            'total_pages' => $totalPages,
        ]);
    }

    // Menguji Printer
    public function printTest(){
        $text = '';

        // ==========================================
        // HEADER
        // ==========================================
        $text .= "========================================\r\n";
        $text .= "       EPSON LX RAW PRINT TEST\r\n";
        $text .= "========================================\r\n\r\n";

        // ==========================================
        // NORMAL MODE
        // ==========================================
        $text .= "===== NORMAL MODE =====\r\n";
        $text .= "01234567890123456789012345678901234567890123456789\r\n";
        $text .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ\r\n\r\n";

        // ==========================================
        // CONDENSED MODE
        // ==========================================
        $text .= "\x0F";     // Condensed ON
        $text .= "===== CONDENSED MODE =====\r\n";
        $text .= "012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789\r\n";
        $text .= "ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZ\r\n";
        $text .= "\x12";     // Condensed OFF
        $text .= "\r\n";

        // ==========================================
        // NORMAL LAGI (sanity check balik normal)
        // ==========================================
        $text .= "===== NORMAL LAGI =====\r\n";
        $text .= "01234567890123456789012345678901234567890123456789\r\n";
        $text .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ\r\n\r\n";

        // ==========================================
        // WIDTH TEST - NORMAL MODE
        // ==========================================
        $text .= "========================================\r\n";
        $text .= "WIDTH TEST - NORMAL MODE\r\n";
        $text .= "========================================\r\n\r\n";

        for ($i = 70; $i <= 100; $i += 5) {
            $text .= "LEN {$i}\r\n";
            $text .= str_repeat("=", $i);
            $text .= "\r\n\r\n";
        }

        // ==========================================
        // WIDTH TEST - CONDENSED MODE
        // ==========================================
        $text .= "\x0F"; // Condensed ON
        $text .= "========================================\r\n";
        $text .= "WIDTH TEST - CONDENSED MODE\r\n";
        $text .= "========================================\r\n\r\n";

        for ($i = 100; $i <= 140; $i += 5) {
            $text .= "LEN {$i}\r\n";
            $text .= str_repeat("=", $i);
            $text .= "\r\n\r\n";
        }
        $text .= "\x12"; // Condensed OFF
        $text .= "\r\n";

        // ==========================================
        // FOOTER
        // ==========================================
        $text .= "========================================\r\n";
        $text .= "END OF TEST\r\n";
        $text .= "========================================\r\n";

        // ==========================================
        // ENCODING
        // ==========================================
        $text = iconv(
            'UTF-8',
            'CP437//TRANSLIT//IGNORE',
            $text
        );
        if ($text === false) {
            $text = '';
        }

        return response()->json([
            'text' => $text
        ]);
    }

    // ### Versi New
    // public function printInvoiceText($invoiceNo){
    //     $rows = Expedisi::where('INVOICE', $invoiceNo)->orderBy('NOSJ')->get();
    //     if ($rows->isEmpty()) abort(404);

    //     $master = $rows->first();

    //     // ====== SATU-SATUNYA ANGKA YANG PERLU DIUBAH SETELAH TES DI KANTOR ======
    //     $LINE_WIDTH = 130; // <-- ganti ini sesuai hasil tes print "=" di printer LX-310 kantor
    //     // ==========================================================================

    //     // turunan lebar otomatis dari LINE_WIDTH
    //     $half = intdiv($LINE_WIDTH, 2);                 // utk baris 2 kolom (label kiri/kanan)
    //     $namaBarangWidth = max(10, $LINE_WIDTH - 48);    // 4(NO)+16(SJ)+10(QTY)+14(TOTAL)+4(spasi)=48
    //     $alamatWrapWidth = max(20, $LINE_WIDTH - 20);    // sisain buffer utk label "ALAMAT : "

    //     // CUSTOMER
    //     $customer = Mcustomer::where('CUSTOMER', $master->CUSTOMER_KODE)->first();
    //     $kepada = strtoupper($customer->NAMACUST ?? '-');
    //     $up     = strtoupper($customer->KONTAK ?? '-');
    //     $alamat = strtoupper($customer->ALAMAT1 ?? '-');

    //     // REKENING
    //     $rekening = Rekening::where('AKTIF', 1)->first();
    //     $bank    = $rekening->BANK ?? '-';
    //     $norek   = $rekening->NOREK ?? '-';
    //     $namaRek = $rekening->NAMA ?? '-';

    //     // TOTAL
    //     $subtotal = (float) ($master->GRAND ?? 0);
    //     $dibayar  = (float) ($master->BAYAR ?? 0);
    //     $saldo    = (float) ($master->PIUTANG ?? 0);

    //     $lines = [];

    //     // HEADER
    //     $lines[] = str_pad('PT. LINTAS MITRA ANUGERAH SEJATI', $LINE_WIDTH, ' ', STR_PAD_BOTH);
    //     $lines[] = str_pad('COLD CHAIN DISTRIBUTION & STORAGE', $LINE_WIDTH, ' ', STR_PAD_BOTH);
    //     $lines[] = str_pad('Jl. Raya Sempidi No.9 Badung - Bali', $LINE_WIDTH, ' ', STR_PAD_BOTH);
    //     $lines[] = str_pad('Telp. (0361) 8947610', $LINE_WIDTH, ' ', STR_PAD_BOTH);
    //     $lines[] = str_repeat('=', $LINE_WIDTH);
    //     $lines[] = str_pad('INVOICE', $LINE_WIDTH, ' ', STR_PAD_BOTH);
    //     $lines[] = str_repeat('=', $LINE_WIDTH);

    //     // INFO 2 KOLOM
    //     $lines[] = sprintf("%-{$half}s %-{$half}s",
    //         'NOMOR  : ' . $master->INVOICE,
    //         'TANGGAL : ' . date('d-m-Y', strtotime($master->TGLINVOICE))
    //     );
    //     $lines[] = sprintf("%-{$half}s %-{$half}s",
    //         'KEPADA : ' . substr($kepada, 0, $half - 10),
    //         !empty($master->TGLJT) ? 'TGL JT : ' . date('d-m-Y', strtotime($master->TGLJT)) : ''
    //     );
    //     $lines[] = sprintf("%-{$half}s %-{$half}s",
    //         'UP     : ' . substr($up, 0, $half - 10),
    //         'CETAK : ' . now()->format('d-m-Y H:i')
    //     );

    //     // ALAMAT (multiline)
    //     $alamatWrap = explode("\n", wordwrap($alamat, $alamatWrapWidth, "\n"));
    //     foreach ($alamatWrap as $i => $alamatRow) {
    //         $lines[] = $i === 0 ? 'ALAMAT : ' . $alamatRow : '         ' . $alamatRow;
    //     }
    //     $lines[] = '';
    //     $lines[] = 'JUMLAH SJ : ' . $rows->count();
    //     $lines[] = str_repeat('=', $LINE_WIDTH);

    //     // TABLE HEADER
    //     $lines[] = sprintf("%-4s %-16s %-{$namaBarangWidth}s %-10s %14s", 'NO', 'SJ', 'NAMA BARANG', 'QTY', 'TOTAL');
    //     $lines[] = str_repeat('-', $LINE_WIDTH);

    //     // DETAIL
    //     $no = 1;
    //     foreach ($rows as $r) {
    //         $namaBarang = trim($r->PESANANGB) !== '' ? $r->PESANANGB : $r->PESANAN;
    //         $qty = (float) $r->JUMLAH;
    //         $qtyText = floor($qty) == $qty ? number_format($qty, 0) : rtrim(rtrim(number_format($qty, 3, '.', ''), '0'), '.');
    //         $lines[] = sprintf(
    //             "%-4s %-16s %-{$namaBarangWidth}s %-10s %14s",
    //             $no,
    //             substr($r->NOSJ, 0, 16),
    //             substr($namaBarang, 0, $namaBarangWidth),
    //             $qtyText . ' KG',
    //             number_format($r->TOTAL, 0, ',', '.')
    //         );
    //         $no++;
    //     }
    //     $lines[] = str_repeat('-', $LINE_WIDTH);

    //     // REKENING + TOTAL
    //     $lines[] = sprintf("%-{$half}s %-{$half}s", 'BANK   : ' . $bank, 'SUB TOTAL : ' . number_format($subtotal, 0, ',', '.'));
    //     $lines[] = sprintf("%-{$half}s %-{$half}s", 'NO REK : ' . $norek, 'DIBAYAR   : ' . number_format($dibayar, 0, ',', '.'));
    //     $lines[] = sprintf("%-{$half}s %-{$half}s", 'A/N    : ' . $namaRek, 'SALDO     : ' . number_format($saldo, 0, ',', '.'));

    //     // FOOTER
    //     $lines[] = '';
    //     $lines[] = '';
    //     $lines[] = str_pad('PENERIMA', $half) . str_pad('MENGETAHUI', $half);
    //     $lines[] = '';
    //     $lines[] = '';
    //     $lines[] = '';
    //     $lines[] = str_pad('(......................)', $half) . str_pad('PT. LINTAS MITRA ANUGERAH SEJATI', $half);

    //     // OUTPUT - Konversi dulu, baru bungkus ESC/P
    //     $text = implode("\r\n", $lines);
    //     $text = iconv('UTF-8','CP437//TRANSLIT//IGNORE',$text);
    //     if ($text === false) {
    //         $text = implode("\r\n", $lines);
    //     }

    //     // ESC/P wrapper setelah konversi
    //     $text = "\x1B\x0F" . $text . "\x12";

    //     return response()->json([
    //         'text' => $text
    //     ]);
    // }
    // ### Versi Old
    // public function printInvoiceText($invoiceNo){
    //     $rows = Expedisi::where('INVOICE', $invoiceNo)->orderBy('NOSJ')->get();
    //     if ($rows->isEmpty()) abort(404);

    //     $master = $rows->first();

    //     // CUSTOMER
    //     $customer = Mcustomer::where('CUSTOMER', $master->CUSTOMER_KODE)->first();
    //     $kepada = strtoupper($customer->NAMACUST ?? '-');
    //     $up     = strtoupper($customer->KONTAK ?? '-');
    //     $alamat = strtoupper($customer->ALAMAT1 ?? '-');

    //     // REKENING
    //     $rekening = Rekening::where('AKTIF', 1)->first();
    //     $bank    = $rekening->BANK ?? '-';
    //     $norek   = $rekening->NOREK ?? '-';
    //     $namaRek = $rekening->NAMA ?? '-';

    //     // TOTAL
    //     $subtotal = (float) ($master->GRAND ?? 0);
    //     $dibayar  = (float) ($master->BAYAR ?? 0);
    //     $saldo    = (float) ($master->PIUTANG ?? 0);

    //     $lines = [];

    //     // HEADER
    //     $lines[] = str_pad('PT. LINTAS MITRA ANUGERAH SEJATI', 90, ' ', STR_PAD_BOTH);
    //     $lines[] = str_pad('COLD CHAIN DISTRIBUTION & STORAGE', 90, ' ', STR_PAD_BOTH);
    //     $lines[] = str_pad('Jl. Raya Sempidi No.9 Badung - Bali', 90, ' ', STR_PAD_BOTH);
    //     $lines[] = str_pad('Telp. (0361) 8947610', 90, ' ', STR_PAD_BOTH);
    //     $lines[] = str_repeat('=', 90);
    //     $lines[] = str_pad('INVOICE', 90, ' ', STR_PAD_BOTH);
    //     $lines[] = str_repeat('=', 90);

    //     // INFO 2 KOLOM
    //     $lines[] = sprintf(
    //         "%-45s %-45s",
    //         'NOMOR  : ' . $master->INVOICE,
    //         'TANGGAL : ' . date('d-m-Y', strtotime($master->TGLINVOICE))
    //     );
    //     $lines[] = sprintf(
    //         "%-45s %-45s",
    //         'KEPADA : ' . substr($kepada, 0, 33),
    //         !empty($master->TGLJT) ? 'TGL JT : ' . date('d-m-Y', strtotime($master->TGLJT)) : ''
    //     );
    //     $lines[] = sprintf(
    //         "%-45s %-45s",
    //         'UP     : ' . substr($up, 0, 33),
    //         'CETAK : ' . now()->format('d-m-Y H:i')
    //     );

    //     // ALAMAT (multiline)
    //     $alamatWrap = explode("\n", wordwrap($alamat, 60, "\n"));
    //     foreach ($alamatWrap as $i => $alamatRow) {
    //         $lines[] = $i === 0 ? 'ALAMAT : ' . $alamatRow : '         ' . $alamatRow;
    //     }
    //     $lines[] = '';
    //     $lines[] = 'JUMLAH SJ : ' . $rows->count();
    //     $lines[] = str_repeat('=', 90);

    //     // TABLE HEADER
    //     $lines[] = sprintf("%-4s %-16s %-42s %-10s %14s", 'NO', 'SJ', 'NAMA BARANG', 'QTY', 'TOTAL');
    //     $lines[] = str_repeat('-', 90);

    //     // DETAIL
    //     $no = 1;
    //     foreach ($rows as $r) {
    //         $namaBarang = trim($r->PESANANGB) !== '' ? $r->PESANANGB : $r->PESANAN;
    //         $qty = (float) $r->JUMLAH;
    //         $qtyText = floor($qty) == $qty ? number_format($qty, 0) : rtrim(rtrim(number_format($qty, 3, '.', ''), '0'), '.');
    //         $lines[] = sprintf(
    //             "%-4s %-16s %-42s %-10s %14s",
    //             $no,
    //             substr($r->NOSJ, 0, 16),
    //             substr($namaBarang, 0, 42),
    //             $qtyText . ' KG',
    //             number_format($r->TOTAL, 0, ',', '.')
    //         );
    //         $no++;
    //     }
    //     $lines[] = str_repeat('-', 90);

    //     // REKENING + TOTAL
    //     $lines[] = sprintf("%-45s %-45s", 'BANK   : ' . $bank, 'SUB TOTAL : ' . number_format($subtotal, 0, ',', '.'));
    //     $lines[] = sprintf("%-45s %-45s", 'NO REK : ' . $norek, 'DIBAYAR   : ' . number_format($dibayar, 0, ',', '.'));
    //     $lines[] = sprintf("%-45s %-45s", 'A/N    : ' . $namaRek, 'SALDO     : ' . number_format($saldo, 0, ',', '.'));

    //     // FOOTER
    //     $lines[] = '';
    //     $lines[] = '';
    //     $lines[] = str_pad('PENERIMA', 45) . str_pad('MENGETAHUI', 45);
    //     $lines[] = '';
    //     $lines[] = '';
    //     $lines[] = '';
    //     $lines[] = str_pad('(......................)', 45) . str_pad('PT. LINTAS MITRA ANUGERAH SEJATI', 45);

    //     // OUTPUT - Konversi dulu, baru bungkus ESC/P
    //     $text = implode("\r\n", $lines);
    //     $text = iconv('UTF-8','CP437//TRANSLIT//IGNORE',$text);
    //     if ($text === false) {
    //         $text = implode("\r\n", $lines);
    //     }

    //     // ESC/P wrapper setelah konversi
    //     $text = "\x1B\x0F" . $text . "\x12";

    //     return response()->json([
    //         'text' => $text
    //     ]);
    // }

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
}


    //  public function printInvoiceText($invoiceNo)
    // {
    //     $rows = Expedisi::where('INVOICE', $invoiceNo)->orderBy('NOSJ')->get();
    //     if ($rows->isEmpty()) abort(404);

    //     $master = $rows->first();

    //     // CUSTOMER
    //     $customer = Mcustomer::where('CUSTOMER', $master->CUSTOMER_KODE)->first();
    //     $kepada = strtoupper($customer->NAMACUST ?? '-');
    //     $up     = strtoupper($customer->KONTAK ?? '-');
    //     $alamat = strtoupper($customer->ALAMAT1 ?? '-');

    //     // REKENING
    //     $rekening = Rekening::where('AKTIF', 1)->first();
    //     $bank    = $rekening->BANK ?? '-';
    //     $norek   = $rekening->NOREK ?? '-';
    //     $namaRek = $rekening->NAMA ?? '-';

    //     // TOTAL
    //     $subtotal = (float) ($master->GRAND ?? 0);
    //     $dibayar  = (float) ($master->BAYAR ?? 0);
    //     $saldo    = (float) ($master->PIUTANG ?? 0);

    //     $lines = [];

    //     // HEADER
    //     $lines[] = str_pad('PT. LINTAS MITRA ANUGERAH SEJATI', 80, ' ', STR_PAD_BOTH);
    //     $lines[] = str_pad('COLD CHAIN DISTRIBUTION & STORAGE', 80, ' ', STR_PAD_BOTH);
    //     $lines[] = str_pad('Jl. Raya Sempidi No.9 Badung - Bali', 80, ' ', STR_PAD_BOTH);
    //     $lines[] = str_pad('Telp. (0361) 8947610', 80, ' ', STR_PAD_BOTH);
    //     $lines[] = str_repeat('=', 80);
    //     $lines[] = str_pad('INVOICE', 80, ' ', STR_PAD_BOTH);
    //     $lines[] = str_repeat('=', 80);

    //     // INFO 2 KOLOM
    //     $lines[] = sprintf(
    //         "%-40s %-40s",
    //         'NOMOR  : ' . $master->INVOICE,
    //         'TANGGAL : ' . date('d-m-Y', strtotime($master->TGLINVOICE))
    //     );
    //     $lines[] = sprintf(
    //         "%-40s %-40s",
    //         'KEPADA : ' . substr($kepada, 0, 28),
    //         !empty($master->TGLJT) ? 'TGL JT : ' . date('d-m-Y', strtotime($master->TGLJT)) : ''
    //     );
    //     $lines[] = sprintf(
    //         "%-40s %-40s",
    //         'UP     : ' . substr($up, 0, 28),
    //         'CETAK : ' . now()->format('d-m-Y H:i')
    //     );

    //     // ALAMAT (multiline)
    //     $alamatWrap = explode("\n", wordwrap($alamat, 65, "\n"));
    //     foreach ($alamatWrap as $i => $alamatRow) {
    //         $lines[] = $i === 0 ? 'ALAMAT : ' . $alamatRow : '         ' . $alamatRow;
    //     }
    //     $lines[] = '';
    //     $lines[] = 'JUMLAH SJ : ' . $rows->count();
    //     $lines[] = str_repeat('=', 80);

    //     // TABLE HEADER
    //     $lines[] = sprintf("%-4s %-14s %-35s %-10s %12s", 'NO', 'SJ', 'NAMA BARANG', 'QTY', 'TOTAL');
    //     $lines[] = str_repeat('-', 80);

    //     // DETAIL
    //     $no = 1;
    //     foreach ($rows as $r) {
    //         $namaBarang = trim($r->PESANANGB) !== '' ? $r->PESANANGB : $r->PESANAN;
    //         $qty = (float) $r->JUMLAH;
    //         $qtyText = floor($qty) == $qty ? number_format($qty, 0) : rtrim(rtrim(number_format($qty, 3, '.', ''), '0'), '.');
    //         $lines[] = sprintf(
    //             "%-4s %-14s %-35s %-10s %12s",
    //             $no,
    //             substr($r->NOSJ, 0, 14),
    //             substr($namaBarang, 0, 35),
    //             $qtyText . ' KG',
    //             number_format($r->TOTAL, 0, ',', '.')
    //         );
    //         $no++;
    //     }
    //     $lines[] = str_repeat('-', 80);

    //     // REKENING + TOTAL (2 kolom)
    //     $lines[] = sprintf("%-40s %-40s", 'BANK   : ' . $bank, 'SUB TOTAL : ' . number_format($subtotal, 0, ',', '.'));
    //     $lines[] = sprintf("%-40s %-40s", 'NO REK : ' . $norek, 'DIBAYAR   : ' . number_format($dibayar, 0, ',', '.'));
    //     $lines[] = sprintf("%-40s %-40s", 'A/N    : ' . $namaRek, 'SALDO     : ' . number_format($saldo, 0, ',', '.'));

    //     // FOOTER
    //     $lines[] = '';
    //     $lines[] = '';
    //     $lines[] = str_pad('PENERIMA', 40) . str_pad('MENGETAHUI', 40);
    //     $lines[] = '';
    //     $lines[] = '';
    //     $lines[] = '';
    //     $lines[] = str_pad('(......................)', 40) . str_pad('PT. LINTAS MITRA ANUGERAH SEJATI', 40);

    //     // OUTPUT
    //     $text = implode("\r\n", $lines);
    //     $text = iconv('UTF-8', 'CP437//TRANSLIT', $text);

    //     return response()->json(['text' => $text]);
    // }
