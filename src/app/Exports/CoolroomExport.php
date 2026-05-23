<?php

namespace App\Exports;

use App\Models\Coolroom;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class CoolroomExport implements
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    protected $tanggalDari;
    protected $tanggalSampai;
    protected $status;
    protected $grandTotal=0;

    public function __construct(
        $tanggalDari,
        $tanggalSampai,
        $status
    ){

        $this->tanggalDari=$tanggalDari;

        $this->tanggalSampai=$tanggalSampai;

        $this->status=$status;

    }

    public function collection()
    {
        $query=Coolroom::query()

            ->select([

                'TGLSJ',

                'NOSJ',

                'CUSTOMER',

                'JUMLAH',

                'UNIT',

                'HARGA',

                'DISC',

                'NDISC',

                'DPP',

                'PPN',

                'NPPN',

                DB::raw(
                    'CAST(GRAND AS DECIMAL(15,2)) as GRAND'
                ),

                'INVOICE',

                'TGLINVOICE'

            ])

            ->whereBetween('TGLSJ',[

                $this->tanggalDari,

                $this->tanggalSampai

            ]);

        // =====================
        // FILTER STATUS
        // =====================
        if($this->status=='belum'){

            $query->where(function($q){

                $q->whereNull('INVOICE')
                    ->orWhere('INVOICE','');

            });

        }else{

            $query->whereNotNull('INVOICE')
                ->where('INVOICE','!=','');

        }

        $data=$query
            ->orderBy('TGLSJ')
            ->get();

        // =====================
        // TOTAL GRAND
        // =====================
        $this->grandTotal=
            $data->sum('GRAND');

        return $data;
    }

    public function headings(): array
    {
        return [

            'Tanggal SJ',

            'No SJ',

            'Customer',

            'Jumlah',

            'Unit',

            'Harga',

            'Disc %',

            'Disc Rp',

            'DPP',

            'PPN %',

            'PPN Rp',

            'Grand Total',

            'Invoice',

            'Tanggal Invoice'

        ];
    }

    // =====================
    // STYLE HEADER
    // =====================
    public function styles(Worksheet $sheet)
    {
        return [

            1=>[

                'font'=>[

                    'bold'=>true,

                    'size'=>12,

                    'color'=>[
                        'rgb'=>'FFFFFF'
                    ]

                ],

                'alignment'=>[

                    'horizontal'=>'center',

                    'vertical'=>'center'

                ],

                'fill'=>[

                    'fillType'=>'solid',

                    'startColor'=>[
                        'rgb'=>'1F4E78'
                    ]

                ]

            ]

        ];
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class=>function(AfterSheet $event){

                $sheet=$event->sheet;

                $lastRow=$sheet
                    ->getHighestRow();

                // =====================
                // BORDER
                // =====================
                $sheet->getStyle(
                    'A1:N'.$lastRow
                )->applyFromArray([

                    'borders'=>[

                        'allBorders'=>[
                            'borderStyle'=>'thin'
                        ]

                    ]

                ]);

                // =====================
                // FORMAT RUPIAH
                // =====================
                $sheet->getStyle(
                    'F2:L'.($lastRow+2)
                )
                ->getNumberFormat()
                ->setFormatCode('#,##0');

                // =====================
                // WRAP TEXT
                // =====================
                $sheet->getStyle(
                    'A1:N'.$lastRow
                )
                ->getAlignment()
                ->setWrapText(true);

                // =====================
                // TOTAL ROW
                // =====================
                $totalRow=$lastRow+2;

                $sheet->setCellValue(
                    'K'.$totalRow,
                    'TOTAL'
                );

                $sheet->setCellValue(
                    'L'.$totalRow,
                    $this->grandTotal
                );

                // =====================
                // STYLE TOTAL
                // =====================
                $sheet->getStyle(
                    'K'.$totalRow.
                    ':L'.$totalRow
                )->applyFromArray([

                    'font'=>[

                        'bold'=>true,

                        'size'=>12

                    ],

                    'fill'=>[

                        'fillType'=>'solid',

                        'startColor'=>[
                            'rgb'=>'D9EAF7'
                        ]

                    ]

                ]);

            }

        ];
    }
}
