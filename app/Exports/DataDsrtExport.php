<?php

namespace App\Exports;

use App\Models\DataDsrt;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataDsrtExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function styles(Worksheet $sheet)
    {
        // Merge title row
        $sheet->mergeCells('A1:F1');
        
        // Alignment for headers
        $sheet->getStyle('A1:F3')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:F3')->getAlignment()->setVertical('center');

        return [
            // Row 1: Title
            1    => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF843C0C'], // Dark Brownish Orange
                ],
            ],
            // Row 2 & 3: Headers
            2    => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFED7D31'], // Orange
                ],
            ],
            3    => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFED7D31'], // Orange
                ],
            ],
            // Borders for headers and data
            'A1:F100' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        // Return query for export sorted by status (Sudah first)
        return DataDsrt::query()->orderBy('ceklis_ipds', 'desc');
    }

    public function map($data): array
    {
        return [
            '16',
            '10',
            $data->nks_sak22,
            $data->nus_ssn,
            $data->ceklis_ipds == '1' ? 'Sudah' : 'Belum',
            optional($data->waktu_ceklis_ipds)->format('d-m-Y') ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            ['Data Progress Penerimaan Kuesioner Susenas oleh IPDS'],
            [
                'Kode Prop',
                'Kode Kab',
                'Kode NKS',
                'No Urut Ruta',
                'Ceklis IPDS?',
                'Tanggal Penerimaan'
            ],
            [
                '',
                '',
                '',
                '',
                '',
                'TT-BB-TTTT'
            ]
        ];
    }
}
