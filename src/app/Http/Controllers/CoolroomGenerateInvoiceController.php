<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coolroom;
use App\Models\Signature;
use App\Models\Arh;
use App\Models\Kwitansi;
use App\Models\Mcustomer;
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
            $arh=Arh::where(
                'NOFAKTUR',
                $coolroom->INVOICE
            )->first();
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
                'tgl_jt'=>$arh->TGLJT
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
        $pdf=new \Mpdf\Mpdf([
            'mode'=>'utf-8',
            'format'=>'A4'
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
            'tanggal_dari'=>'required|date',
            'tanggal_sampai'=>'required|date|after_or_equal:tanggal_dari',
            'status_invoice'=>'required'
        ]);
        $tanggalDari=$request->tanggal_dari;
        $tanggalSampai=$request->tanggal_sampai;
        $status=$request->status_invoice;
        $filename=
            'laporan_coolroom_'.
            $status.'_'.
            $tanggalDari.'_sd_'.
            $tanggalSampai.
            '.xlsx';
        return Excel::download(
            new CoolroomExport(
                $tanggalDari,
                $tanggalSampai,
                $status
            ),
            $filename
        );
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
