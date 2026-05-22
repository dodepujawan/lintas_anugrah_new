<?php

namespace App\Exports;

use App\Models\Expedisi;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class InvoiceDgnExport implements
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    protected $tanggalDari;
    protected $tanggalSampai;
    protected $status;
    protected $grandTotal = 0;

    public function __construct($tanggalDari, $tanggalSampai, $status)
    {
        $this->tanggalDari   = $tanggalDari;
        $this->tanggalSampai = $tanggalSampai;
        $this->status        = $status;
    }

    public function collection()
    {
        $query = Expedisi::query()
            ->select([
                'TGLMUAT',
                'NOSJ',
                'NOJALAN',
                'CUSTOMER',
                'NAMA_KENDARAAN',
                'NAMA_DRIVER',
                'rute',
                'barang',
                'JUMLAH',
                'UNIT',

                DB::raw('CAST(GRAND AS DECIMAL(15,2)) as GRAND'),

                'INVOICE',
                'TGLINVOICE',
                'STS',
            ])

            ->whereBetween('TGLMUAT', [
                $this->tanggalDari,
                $this->tanggalSampai
            ])

            ->where('JENIS', 'REN');

        // =============================
        // BELUM INVOICE
        // =============================
        if ($this->status == 'belum') {

            $query->where(function ($q) {

                $q->whereNull('INVOICE')
                    ->orWhere('INVOICE', '');

            });

        } else {

            // =============================
            // SUDAH INVOICE
            // =============================
            $query->whereNotNull('INVOICE')
                ->where('INVOICE', '!=', '');

        }

        $data = $query
            ->orderBy('TGLMUAT')
            ->get();

        // =============================
        // TOTAL GRAND
        // =============================
        $this->grandTotal = $data->sum('GRAND');

        return $data;
    }

    public function headings(): array
    {
        return [

            'Tanggal Muat',
            'No SJ',
            'No Jalan',
            'Customer',
            'Kendaraan',
            'Driver',
            'Rute',
            'Barang',
            'Jumlah',
            'Unit',
            'Grand Total',
            'Invoice',
            'Tgl Invoice',
            'Status',

        ];
    }

    // =============================
    // STYLE HEADER
    // =============================
    public function styles(Worksheet $sheet)
    {
        return [

            1 => [

                'font' => [

                    'bold' => true,
                    'size' => 12,

                    'color' => [
                        'rgb' => 'FFFFFF'
                    ]

                ],

                'alignment' => [

                    'horizontal' => 'center',
                    'vertical' => 'center',

                ],

                'fill' => [

                    'fillType' => 'solid',

                    'startColor' => [
                        'rgb' => '1F4E78'
                    ]

                ]

            ]

        ];
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet;

                $lastRow = $sheet->getHighestRow();

                // =============================
                // BORDER
                // =============================
                $sheet->getStyle('A1:N'.$lastRow)
                    ->applyFromArray([

                        'borders' => [

                            'allBorders' => [
                                'borderStyle' => 'thin',
                            ],

                        ],

                    ]);

                // =============================
                // FORMAT RUPIAH
                // =============================
                $sheet->getStyle('K2:K'.($lastRow + 2))
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                // =============================
                // WRAP TEXT
                // =============================
                $sheet->getStyle('A1:N'.$lastRow)
                    ->getAlignment()
                    ->setWrapText(true);

                // =============================
                // TOTAL ROW
                // =============================
                $totalRow = $lastRow + 2;

                // LABEL TOTAL
                $sheet->setCellValue(
                    'J'.$totalRow,
                    'TOTAL'
                );

                // TOTAL GRAND
                $sheet->setCellValue(
                    'K'.$totalRow,
                    $this->grandTotal
                );

                // =============================
                // STYLE TOTAL
                // =============================
                $sheet->getStyle('J'.$totalRow.':K'.$totalRow)
                    ->applyFromArray([

                        'font' => [

                            'bold' => true,
                            'size' => 12,

                        ],

                        'fill' => [

                            'fillType' => 'solid',

                            'startColor' => [
                                'rgb' => 'D9EAF7'
                            ]

                        ]

                    ]);

                // =============================
                // CENTER VERTICAL
                // =============================
                $sheet->getStyle('A1:N'.$lastRow)
                    ->getAlignment()
                    ->setVertical('center');

            }

        ];
    }
}
