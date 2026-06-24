<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coolroom;
use App\Models\Signature;
use App\Models\Arh;
use App\Models\Kwitansi;
use App\Models\Mcustomer;
use App\Models\Rekening;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CoolroomExport;

use Illuminate\Support\Facades\DB;

class CoolroomGenerateInvoiceController extends Controller
{
    public function index()
    {
        return view('coolroomInvoiceGen.coolroom-invoice-gen');
    }

    public function getDataInvoice(Request $request)
    {
        $query=Coolroom::query();
        // =====================
        // FILTER
        // =====================
        if($request->status_invoice=='belum'){
            $query->where(function($q){
                $q->whereNull('INVOICE')
                    ->orWhere('INVOICE','');
            });
        }
        if($request->status_invoice=='sudah'){
            $query->whereNotNull('INVOICE')
                ->where('INVOICE','!=','');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('TGLSJ',function($row){
                return $row->TGLSJ
                    ? Carbon::parse($row->TGLSJ)
                        ->format('d-m-Y')
                    : '-';
            })
            ->editColumn('JUMLAH',function($row){
                return number_format(
                    $row->JUMLAH ?? 0,
                    3,
                    ',',
                    '.'
                );
            })

            ->editColumn('GRAND',function($row){
                return number_format(
                    $row->GRAND ?? 0,
                    0,
                    ',',
                    '.'
                );
            })
            ->addColumn('action',function($row) use ($request){
                // =====================
                // SUDAH INVOICE
                // =====================
                if($request->status_invoice=='sudah'){
                    return '
                        <button
                            class="btn btn-sm btn-primary btn-edit-invoice-coolroom"
                            data-id="'.$row->id.'"
                            data-nosj="'.$row->NOSJ.'">
                            Edit Invoice
                        </button>
                    ';
                }
                // =====================
                // BELUM INVOICE
                // =====================
                return '
                    <button
                        class="btn btn-sm btn-success btn-proses-invoice-coolroom"
                        data-id="'.$row->id.'"
                        data-nosj="'.$row->NOSJ.'">
                        Buat Invoice
                    </button>
                ';
            })
            ->rawColumns([
                'action'
            ])
            ->make(true);
    }

    public function showInvoiceCoolroom($nosj)
    {
        // =====================
        // AMBIL DATA
        // =====================
        $coolroom=Coolroom::where('NOSJ',$nosj)
            ->first();
        if(!$coolroom){
            return response()->json([
                'status'=>false,
                'message'=>'Data coolroom tidak ditemukan'
            ]);
        }
        // =====================
        // DATA ARH
        // =====================
        $arh=null;
        if(!empty($coolroom->INVOICE)){
            $arh=Arh::where('NOFAKTUR',$coolroom->INVOICE)->first();
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
        // =====================
        // TOP KREDIT CUSTOMER
        // =====================
        $topKredit=0;
        if($coolroom->CUSTOMER_KODE){
            $customer=Mcustomer::where(
                'kode_cus',
                $coolroom->CUSTOMER_KODE
            )->first();
            $topKredit=$customer->TOPKREDIT ?? 0;
        }
        // =====================
        // TGL INVOICE
        // =====================
        $tglInvoice=$coolroom->TGLINVOICE
            ? Carbon::parse($coolroom->TGLINVOICE)
            : now();
        // =====================
        // TGL JATUH TEMPO
        // =====================
        $tglJatuhTempo=$tglInvoice
            ->copy()
            ->addDays((int)$topKredit);
        // =====================
        // RESPONSE
        // =====================
        return response()->json([
            'status'=>true,
            'data'=>[
                // =====================
                // IDENTITAS
                // =====================
                'id'=>$coolroom->id,
                'nosj'=>$coolroom->NOSJ,
                'tglsj'=>$coolroom->TGLSJ,
                'invoice'=>$coolroom->INVOICE ?? '',
                'tgl_invoice'=>$coolroom->TGLINVOICE,
                'customer'=>$coolroom->CUSTOMER,
                'customer_kode'=>$coolroom->CUSTOMER_KODE,
                // =====================
                // NILAI
                // =====================
                'jumlah'=>$coolroom->JUMLAH ?? 0,
                'harga'=>$coolroom->HARGA ?? 0,
                'sub_total'=>$coolroom->SUBTOTAL ?? 0,
                'disc_persen'=>$coolroom->DISC ?? 0,
                'disc_rp'=>$coolroom->NDISC ?? 0,
                'dpp'=>$coolroom->DPP ?? 0,
                'ppn'=>$coolroom->PPN ?? 0,
                'ppn_rp'=>$coolroom->NPPN ?? 0,
                'grand'=>$coolroom->GRAND ?? 0,
                // =====================
                // PEMBAYARAN
                // =====================
                'bayar'=>$coolroom->BAYAR ?? 0,
                'piutang'=>$coolroom->PIUTANG ?? 0,
                // =====================
                // JATUH TEMPO
                // =====================
                'top'=>$topKredit,
                'tgl_jt'=>$coolroom->TGLJT
                    ?? $tglJatuhTempo
                        ->format('Y-m-d'),
                // =====================
                // ARH
                // =====================
                'saldo'=>$arh->SALDO ?? 0
            ]
        ]);
    }

    public function prosesInvoice(Request $request)
    {
        try {
            $invoice=$request->invoice;
            DB::transaction(function () use ($request,&$invoice) {
                // =====================
                // PARSING
                // =====================
                $bayar=(int)preg_replace(
                    '/[^0-9]/',
                    '',
                    $request->bayar
                );
                $top=(int)preg_replace(
                    '/[^0-9]/',
                    '',
                    $request->top
                );
                $tglJtp=$request->tgl_jtp;
                $nosj=$request->nosj;
                // =====================
                // STORE BARU
                // =====================
                if(empty($invoice)){
                    $invoice=$this->generateInvoiceCoolroom();
                    $row=Coolroom::where(
                        'NOSJ',
                        $nosj
                    )
                    ->lockForUpdate()
                    ->first();
                    if(!$row){
                        throw new \Exception(
                            'Data coolroom tidak ditemukan'
                        );
                    }
                    $row->INVOICE=$invoice;
                    $row->TGLINVOICE=now();
                    $row->STS='INVOICE';
                }else{
                    // =====================
                    // UPDATE EXISTING
                    // =====================
                    $row=Coolroom::where(
                        'INVOICE',
                        $invoice
                    )
                    ->lockForUpdate()
                    ->first();
                    if(!$row){
                        throw new \Exception(
                            'Invoice tidak ditemukan'
                        );
                    }
                    // =============================
                    // CEK PEMBAYARAN ARH
                    // =============================
                    $arh = null;
                    if (!empty($row->INVOICE)) {
                        $arh = Arh::where('NOFAKTUR', $row->INVOICE)->first();
                        if ($arh && (float)$arh->BAYAR > 0) {throw new \Exception('Invoice sudah memiliki pembayaran. Silakan hubungi admin.');}
                    }
                }
                // =====================
                // GRAND
                // =====================
                $grand=(int)($row->GRAND ?? 0);
                // =====================
                // VALIDASI
                // =====================
                if($bayar<0){
                    throw new \Exception(
                        'Nominal bayar tidak valid'
                    );
                }
                if($bayar>$grand){
                    throw new \Exception(
                        'Bayar tidak boleh lebih besar dari total'
                    );
                }
                // =====================
                // PIUTANG
                // =====================
                $piutangBaru=$grand-$bayar;
                // =====================
                // UPDATE COOLROOM
                // =====================
                $row->BAYAR=$bayar;
                $row->PIUTANG=$piutangBaru;
                $row->TOP=$top;
                $row->TGLJT=$tglJtp;
                $row->save();
                // =====================
                // HANDLE ARH
                // =====================
                if($piutangBaru>0){
                    Arh::updateOrCreate(
                        [
                            'NOFAKTUR'=>$invoice
                        ],
                        [
                            'TGLFAKTUR'=>$row->TGLINVOICE ?? now(),
                            'CUSTOMER'=>$row->CUSTOMER,
                            'SALDO'=>$top,
                            'PIUTANG'=>$piutangBaru,
                            'DISCOUNT'=>$row->NDISC ?? 0,
                            'TGLJT'=>$tglJtp,
                            'CABANG'=>$row->CABANG ?? '',
                            'KETERANGAN'=>'INVOICE COOLROOM',
                            'USER'=>auth()->user()->user_id,
                            'USER_UPDATE'=>auth()->user()->user_id,
                            'updated_at'=>now()
                        ]
                    );
                }else{
                    // =====================
                    // LUNAS
                    // =====================
                    Arh::where(
                        'NOFAKTUR',
                        $invoice
                    )->delete();
                }
            });
            return response()->json([
                'status'=>true,
                'message'=>'Invoice coolroom berhasil diproses',
                'invoice'=>$invoice
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'=>false,
                'message'=>$e->getMessage()
            ],500);
        }
    }

    public function pdfGenerate($invoice)
    {
        // =====================
        // MASTER
        // =====================
        $master=Coolroom::where(
            'INVOICE',
            $invoice
        )->firstOrFail();

        // =====================
        // ARH
        // =====================
        $arh=Arh::where(
            'NOFAKTUR',
            $invoice
        )->first();

        // =====================
        // CUSTOMER
        // =====================
        $customer=Mcustomer::where(
            'kode_cus',
            $master->CUSTOMER_KODE
        )->first();

        // =====================
        // SIGNATURE
        // =====================
        $signature=Signature::latest()
            ->first();

        // =====================
        // MPDF
        // =====================
        $tempPath = storage_path('app/mpdf-temp');

        if (!is_dir($tempPath)) {
            mkdir($tempPath, 0775, true);
        }

        $pdf = new \Mpdf\Mpdf([
            'mode'    => 'utf-8',
            'format'  => 'A4',
            'tempDir' => $tempPath,
        ]);

        $html=view(
            'coolroomInvoiceGen.coolroomInvoicePdf',
            compact(
                'master',
                'arh',
                'customer',
                'signature'
            )
        )->render();

        $pdf->WriteHTML($html);

        return response(
            $pdf->Output(
                'INV-'.$invoice.'.pdf',
                'I'
            ),
            200,
            [
                'Content-Type'=>'application/pdf'
            ]
        );
    }

    public function export(Request $request)
    {
        $request->validate([
            'tanggal_dari'   => 'required|date',
            'tanggal_sampai' => 'required|date|after_or_equal:tanggal_dari',
            'filter_inv_coolroom' => 'required'
        ]);

        $tanggalDari   = $request->tanggal_dari;
        $tanggalSampai = $request->tanggal_sampai;
        $status        = $request->filter_inv_coolroom;
        $customer      = $request->customer_invoice_col; // select2 customer

        $filename =
            'laporan_coolroom_' .
            $status . '_' .
            $tanggalDari . '_sd_' .
            $tanggalSampai .
            '.xlsx';

        return Excel::download(
            new CoolroomExport(
                $tanggalDari,
                $tanggalSampai,
                $status,
                $customer
            ),
            $filename
        );
    }

    // ##################### EDIT COOLROOM ###############################
    public function indexEdit()
    {
        return view('coolroomInvoiceGen.coolroom-invoice-edit');
    }

    public function tableEditCoolroom(Request $request)
    {
        $query = Coolroom::select(['INVOICE', 'TGLINVOICE', 'CUSTOMER', 'GRAND', 'PIUTANG', 'TGLJT', 'KETERANGAN'])
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
            ->filter(function ($query) use ($request) {
                if ($request->search['value'] ?? false) {
                    $search = $request->search['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('INVOICE', 'like', "%{$search}%")
                        ->orWhere('CUSTOMER', 'like', "%{$search}%");
                    });
                }
            })
            ->addIndexColumn()
            ->editColumn('TGLINVOICE', fn($row) => $row->TGLINVOICE ? Carbon::parse($row->TGLINVOICE)->format('d-m-Y') : '-')
            ->editColumn('GRAND', fn($row) => number_format($row->GRAND ?? 0, 0, ',', '.'))
            ->editColumn('PIUTANG', fn($row) => number_format($row->PIUTANG ?? 0, 0, ',', '.'))
            ->addColumn('bayar', fn($row) => number_format(($row->GRAND ?? 0) - ($row->PIUTANG ?? 0), 0, ',', '.'))
            ->addColumn('aksi', fn($row) => '<button class="btn btn-warning btn-sm btn_edit_invoice_coolroom" data-invoice="' . $row->INVOICE . '"><i class="bx bx-edit-alt"></i></button>')
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function showEditInvoiceCoolroom($invoice)
    {
        $row = Coolroom::where('INVOICE', $invoice)->first();
        if (!$row) {
            return response()->json(['status' => false, 'message' => 'Invoice tidak ditemukan']);
        }

        $arh = null;
        if (!empty($row->INVOICE)) {
            $arh = Arh::where('NOFAKTUR', $invoice)->first();
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

        return response()->json([
            'status' => true,
            'master' => [
                'invoice' => $row->INVOICE,
                'customer' => $row->CUSTOMER,
                'tgl_invoice' => $row->TGLINVOICE ? Carbon::parse($row->TGLINVOICE)->format('Y-m-d') : null,
                'tgl_jt' => ($arh->TGLJT ?? $row->TGLJT) ? Carbon::parse($arh->TGLJT ?? $row->TGLJT)->format('Y-m-d') : null,
                'boxing' => $row->BOXING ?? 0,
                'jumlah' => $row->JUMLAH ?? 0,
                'unit' => $row->UNIT ?? 'KG',
                'harga' => $row->HARGA ?? 0,
                'subtotal' => $row->SUBTOTAL ?? 0,
                'disc' => $row->DISC ?? 0,
                'dpp' => $row->DPP ?? 0,
                'ppn' => $row->PPN ?? 0,
                'total' => $row->TOTAL ?? 0,
                'grand' => $row->GRAND ?? 0,
                'bayar' => $row->BAYAR ?? 0,
                'piutang' => $row->PIUTANG ?? 0,
                'keterangan' => $row->KETERANGAN ?? ''
            ]
        ]);
    }

    public function updateEditInvoiceCoolroom(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $invoice = $request->invoice;
                if (!$invoice) {
                    throw new \Exception('Invoice tidak ditemukan');
                }

                $coolroom = Coolroom::where('INVOICE', $invoice)->first();
                if (!$coolroom) {
                    throw new \Exception('Data invoice tidak ditemukan');
                }

                $arh = null;
                if (!empty($coolroom->INVOICE)) {
                    $arh = Arh::where('NOFAKTUR', $coolroom->INVOICE)->first();
                    if ($arh && (float)$arh->BAYAR > 0) {throw new \Exception('Invoice sudah memiliki pembayaran. Silakan hubungi admin.');}
                }

                $jumlah = (float) ($request->jumlah ?? 0);
                $harga = (float) ($request->harga ?? 0);
                $discPersen = (float) ($request->disc ?? 0);
                $ppnPersen = (float) ($request->ppn ?? 0);
                $boxing = $request->boolean('boxing');

                // Hitung subtotal
                $subtotal = $boxing ? $harga : $jumlah * $harga;
                $ndisc = round($subtotal * ($discPersen / 100), 0);
                $dpp = round($subtotal - $ndisc, 0);
                $nppn = round($dpp * ($ppnPersen / 100), 0);
                $grand = round($dpp + $nppn, 0);

                $bayar = (float) ($coolroom->BAYAR ?? 0);
                if ($grand < $bayar) {
                    throw new \Exception('Grand tidak boleh lebih kecil dari pembayaran yang sudah diterima');
                }
                $piutang = $grand - $bayar;

                // Update coolroom
                $coolroom->update([
                    'BOXING'      => $boxing,
                    'JUMLAH'      => $jumlah,
                    'HARGA'       => $harga,
                    'SUBTOTAL'    => $subtotal,
                    'DISC'        => $discPersen,
                    'NDISC'       => $ndisc,
                    'DPP'         => $dpp,
                    'PPN'         => $ppnPersen,
                    'NPPN'        => $nppn,
                    'TOTAL'       => $dpp,
                    'GRAND'       => $grand,
                    'PIUTANG'     => $piutang,
                    'TGLJT'       => $request->tgl_jt,
                    'KETERANGAN'  => $request->keterangan,
                    'USEREDIT'    => auth()->user()->user_id
                ]);

                // Handle ARH
                $arh = Arh::where('NOFAKTUR', $invoice)->first();
                if ($piutang > 0) {
                    if (!$arh) {
                        Arh::create([
                            'NOFAKTUR'   => $invoice,
                            'TGLFAKTUR'  => $coolroom->TGLINVOICE,
                            'TGLJT'      => $request->tgl_jt,
                            'CUSTOMER'   => $coolroom->CUSTOMER,
                            'PIUTANG'    => $piutang,
                            'BAYAR'      => 0,
                            'SALDO'      => $piutang,
                            'KETERANGAN' => $request->keterangan,
                            'USER'       => auth()->user()->user_id,
                            'CABANG'     => $coolroom->area_id ?? ''
                        ]);
                    } else {
                        $arh->update([
                            'TGLJT'       => $request->tgl_jt,
                            'PIUTANG'     => $piutang,
                            'SALDO'       => $piutang,
                            'KETERANGAN'  => $request->keterangan,
                            'USER_UPDATE' => auth()->user()->user_id
                        ]);
                    }
                } else {
                    if ($arh) $arh->delete();
                }

                // Update kwitansi jika ada
                $kwitansi = Kwitansi::where('FDOK_TRANS', $invoice)->first();
                if ($kwitansi) {
                    $kwitansi->update([
                        'FNIL_DOK' => $grand,
                        'TOTAL'    => $dpp,
                        'PPN'      => $ppnPersen,
                        'DISC'     => $discPersen,
                        'NDISC'    => $ndisc
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Invoice coolroom berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function printInvoiceCoolroom($invoiceNo)
    {
        $rows = Coolroom::where('INVOICE', $invoiceNo)->orderBy('NOSJ')->get();
        if ($rows->isEmpty()) abort(404);

        $master = $rows->first();

        // CUSTOMER
        $customer = Mcustomer::where('CUSTOMER', $master->CUSTOMER_KODE)->first();
        $kepada = strtoupper($customer->NAMACUST ?? '-');
        $up     = strtoupper($customer->KONTAK ?? '-');
        $alamat = strtoupper($customer->ALAMAT1 ?? '-');

        // REKENING
        $rekening = Rekening::where('AKTIF', 1)->first();
        $bank    = $rekening->BANK ?? '-';
        $norek   = $rekening->NOREK ?? '-';
        $namaRek = $rekening->NAMA ?? '-';

        // TOTAL
        $subtotal = (float) ($master->SUBTOTAL ?? 0);
        $ndisc    = (float) ($master->NDISC ?? 0);
        $dpp      = (float) ($master->DPP ?? 0);
        $nppn     = (float) ($master->NPPN ?? 0);
        $grand    = (float) ($master->GRAND ?? 0);
        $dibayar  = (float) ($master->BAYAR ?? 0);
        $saldo    = (float) ($master->PIUTANG ?? 0);

        $lines = [];

        // HEADER
        $lines[] = str_pad('PT. LINTAS MITRA ANUGERAH SEJATI', 80, ' ', STR_PAD_BOTH);
        $lines[] = str_pad('COLD CHAIN DISTRIBUTION & STORAGE', 80, ' ', STR_PAD_BOTH);
        $lines[] = str_pad('Jl. Raya Sempidi No.9 Badung - Bali', 80, ' ', STR_PAD_BOTH);
        $lines[] = str_pad('Telp. (0361) 8947610', 80, ' ', STR_PAD_BOTH);
        $lines[] = str_repeat('=', 80);
        $lines[] = str_pad('INVOICE COOLROOM', 80, ' ', STR_PAD_BOTH);
        $lines[] = str_repeat('=', 80);

        // INFO 2 KOLOM
        $lines[] = sprintf(
            "%-40s %-40s",
            'NOMOR  : ' . $master->INVOICE,
            'TANGGAL : ' . date('d-m-Y', strtotime($master->TGLINVOICE))
        );
        $lines[] = sprintf(
            "%-40s %-40s",
            'KEPADA : ' . substr($kepada, 0, 28),
            !empty($master->TGLJT) ? 'TGL JT : ' . date('d-m-Y', strtotime($master->TGLJT)) : ''
        );
        $lines[] = sprintf(
            "%-40s %-40s",
            'UP     : ' . substr($up, 0, 28),
            'CETAK : ' . now()->format('d-m-Y H:i')
        );

        // ALAMAT (multiline)
        $alamatWrap = explode("\n", wordwrap($alamat, 65, "\n"));
        foreach ($alamatWrap as $i => $alamatRow) {
            $lines[] = $i === 0 ? 'ALAMAT : ' . $alamatRow : '         ' . $alamatRow;
        }
        $lines[] = '';
        $lines[] = 'JUMLAH SJ : ' . $rows->count();
        $lines[] = str_repeat('=', 80);

        // TABLE HEADER
        $lines[] = sprintf("%-4s %-14s %-30s %-10s %-8s %10s", 'NO', 'SJ', 'KETERANGAN', 'JUMLAH', 'UNIT', 'TOTAL');
        $lines[] = str_repeat('-', 80);

        // DETAIL
        $no = 1;
        foreach ($rows as $r) {
            $qty = (float) ($r->JUMLAH ?? 0);
            $qtyText = floor($qty) == $qty ? number_format($qty, 0) : rtrim(rtrim(number_format($qty, 3, '.', ''), '0'), '.');
            $lines[] = sprintf(
                "%-4s %-14s %-30s %-10s %-8s %10s",
                $no,
                substr($r->NOSJ ?? '-', 0, 14),
                substr($r->KETERANGAN ?? '-', 0, 30),
                $qtyText,
                $r->UNIT ?? '',
                number_format($r->TOTAL ?? 0, 0, ',', '.')
            );
            $no++;
        }
        $lines[] = str_repeat('-', 80);

        // REKENING + TOTAL (2 kolom)
        $lines[] = sprintf("%-40s %-40s", 'BANK   : ' . $bank, 'SUB TOTAL : ' . number_format($subtotal, 0, ',', '.'));
        $lines[] = sprintf("%-40s %-40s", 'NO REK : ' . $norek, 'DISKON : ' . number_format($ndisc, 0, ',', '.'));
        $lines[] = sprintf("%-40s %-40s", 'A/N    : ' . $namaRek, 'DPP : ' . number_format($dpp, 0, ',', '.'));
        $lines[] = sprintf("%-40s %-40s", '', 'PPN : ' . number_format($nppn, 0, ',', '.'));
        $lines[] = sprintf("%-40s %-40s", '', 'GRAND TOTAL : ' . number_format($grand, 0, ',', '.'));
        $lines[] = sprintf("%-40s %-40s", '', 'DIBAYAR : ' . number_format($dibayar, 0, ',', '.'));
        $lines[] = sprintf("%-40s %-40s", '', 'SALDO : ' . number_format($saldo, 0, ',', '.'));

        // FOOTER
        $lines[] = '';
        $lines[] = '';
        $lines[] = str_pad('PENERIMA', 40) . str_pad('MENGETAHUI', 40);
        $lines[] = '';
        $lines[] = '';
        $lines[] = '';
        $lines[] = str_pad('(......................)', 40) . str_pad('PT. LINTAS MITRA ANUGERAH SEJATI', 40);

        // OUTPUT
        $text = implode("\r\n", $lines);
        $text = iconv('UTF-8', 'CP437//TRANSLIT', $text);

        return response()->json(['text' => $text]);
    }

    private function generateInvoiceCoolroom(): string{
        $tahun = now()->format('Y');
        $last = Coolroom::where('INVOICE', 'like', "FCO{$tahun}%")
            ->orderBy('INVOICE', 'desc')
            ->lockForUpdate()
            ->first();
        $lastNo = $last
            ? intval(substr($last->INVOICE, -6))
            : 0;
        return 'FCO' . $tahun . str_pad($lastNo + 1, 6, '0', STR_PAD_LEFT);
    }
}

// public function printInvoiceCoolroom($invoiceNo)
//     {
//         $rows = Coolroom::where('INVOICE', $invoiceNo)
//             ->orderBy('NOSJ')
//             ->get();

//         if ($rows->isEmpty()) {
//             abort(404);
//         }

//         $master = $rows->first();

//         // =========================================
//         // CUSTOMER
//         // =========================================
//         $customer = Mcustomer::where('CUSTOMER', $master->CUSTOMER_KODE)->first();

//         $kepada  = $customer->NAMACUST ?? '-';
//         $up      = $customer->KONTAK   ?? '-';
//         $alamat  = $customer->ALAMAT1  ?? '-';

//         // =========================================
//         // REKENING
//         // =========================================
//         $rekening = Rekening::where('AKTIF', 1)->first();

//         $bank    = $rekening->BANK  ?? '-';
//         $norek   = $rekening->NOREK ?? '-';
//         $namaRek = $rekening->NAMA  ?? '-';

//         // =========================================
//         // TOTAL
//         // =========================================
//         $subtotal = (float) ($master->SUBTOTAL ?? 0);
//         $ndisc    = (float) ($master->NDISC   ?? 0);
//         $dpp      = (float) ($master->DPP     ?? 0);
//         $nppn     = (float) ($master->NPPN    ?? 0);
//         $grand    = (float) ($master->GRAND   ?? 0);
//         $dibayar  = (float) ($master->BAYAR   ?? 0);
//         $saldo    = (float) ($master->PIUTANG ?? 0);

//         $lines = [];

//         // =========================================
//         // HEADER
//         // =========================================
//         $lines[] = str_pad('INVOICE COOLROOM', 40) .
//                 str_pad('PT. LINTAS MITRA ANUGERAH SEJATI', 40, ' ', STR_PAD_LEFT);

//         $lines[] = str_repeat('=', 80);

//         $lines[] = 'NOMOR      : ' . $master->INVOICE;
//         $lines[] = 'TANGGAL    : ' . date('d-m-Y', strtotime($master->TGLINVOICE));
//         $lines[] = 'TGL JT     : ' . date('d-m-Y', strtotime($master->TGLJT));
//         $lines[] = 'TGL CETAK  : ' . now()->format('d-m-Y H:i');
//         $lines[] = '';
//         $lines[] = 'NOMOR SJ   : ' . ($master->NOSJ ?? '-');
//         $lines[] = '';
//         $lines[] = 'KEPADA     : ' . strtoupper($kepada);
//         $lines[] = 'UP         : ' . strtoupper($up);
//         $lines[] = 'ALAMAT     : ' . strtoupper($alamat);
//         $lines[] = str_repeat('=', 80);

//         // =========================================
//         // DETAIL
//         // =========================================
//         $lines[] = sprintf(
//             "%-3s %-12s %-25s %-10s %-8s %15s",
//             'NO',
//             'SJ',
//             'KETERANGAN',
//             'JUMLAH',
//             'UNIT',
//             'TOTAL'
//         );
//         $lines[] = str_repeat('-', 80);

//         $no = 1;
//         foreach ($rows as $r) {
//             $qty = (float) ($r->JUMLAH ?? 0);

//             $qtyText = floor($qty) == $qty
//                 ? number_format($qty, 0)
//                 : rtrim(rtrim(number_format($qty, 3, '.', ''), '0'), '.');

//             $lines[] = sprintf(
//                 "%-3s %-12s %-25s %-10s %-8s %15s",
//                 $no,
//                 $r->NOSJ,
//                 substr($r->KETERANGAN ?? '-', 0, 25),
//                 $qtyText,
//                 $r->UNIT ?? '',
//                 number_format($r->TOTAL, 0, ',', '.')
//             );

//             $no++;
//         }

//         $lines[] = str_repeat('-', 80);

//         // =========================================
//         // PEMBAYARAN
//         // =========================================
//         $lines[] = '';
//         $lines[] = 'Untuk pembayaran mohon transfer ke rekening resmi :';
//         $lines[] = '';
//         $lines[] = 'Bank   : ' . $bank;
//         $lines[] = 'No Rek : ' . $norek;
//         $lines[] = 'A/N    : ' . $namaRek;
//         $lines[] = '';

//         // =========================================
//         // TOTAL
//         // =========================================
//         $lines[] = str_pad('SUB TOTAL : ' . number_format($subtotal, 0, ',', '.'), 80, ' ', STR_PAD_LEFT);
//         $lines[] = str_pad('DISKON    : ' . number_format($ndisc,    0, ',', '.'), 80, ' ', STR_PAD_LEFT);
//         $lines[] = str_pad('DPP       : ' . number_format($dpp,      0, ',', '.'), 80, ' ', STR_PAD_LEFT);
//         $lines[] = str_pad('PPN       : ' . number_format($nppn,     0, ',', '.'), 80, ' ', STR_PAD_LEFT);
//         $lines[] = str_pad('GRAND     : ' . number_format($grand,    0, ',', '.'), 80, ' ', STR_PAD_LEFT);
//         $lines[] = str_pad('DIBAYAR   : ' . number_format($dibayar,  0, ',', '.'), 80, ' ', STR_PAD_LEFT);
//         $lines[] = str_pad('SALDO     : ' . number_format($saldo,    0, ',', '.'), 80, ' ', STR_PAD_LEFT);

//         // =========================================
//         // FOOTER
//         // =========================================
//         $footer = [];
//         $footer[] = '';
//         $footer[] = '';
//         $footer[] = str_pad('PENERIMA', 40) . str_pad('MENGETAHUI', 40);
//         $footer[] = '';
//         $footer[] = '';
//         $footer[] = '';
//         $footer[] = '';
//         $footer[] = str_pad('(......................)', 40) . str_pad('PT. LINTAS MITRA ANUGERAH SEJATI', 40);

//         // =========================================
//         // FIX HEIGHT
//         // =========================================
//         $pageHeight = 60;
//         while (count($lines) < ($pageHeight - count($footer))) {
//             $lines[] = '';
//         }

//         $lines = array_merge($lines, $footer);

//         // =========================================
//         // OUTPUT
//         // =========================================
//         $text = implode("\r\n", $lines);
//         $text = iconv('UTF-8', 'CP437//TRANSLIT', $text);

//         return response()->json(['text' => $text]);
//     }
