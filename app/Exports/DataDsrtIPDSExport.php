<?php

namespace App\Exports;

use App\Models\DataDsrt;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataDsrtIPDSExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'IPDS';
    }
    public function columnWidths(): array
    {
        return [
            'A' => 12, // Kode Prop
            'B' => 12, // Kode Kab
            'C' => 18, // Kode NKS
            'D' => 14, // No Urut Ruta
            'E' => 16, // Ceklis IPDS?
            'F' => 16, // Tanggal Ceklis IPDS
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Merge title row — 6 kolom: A–F
        $sheet->mergeCells('A1:F1');

        // Dynamic borders for all rows
        $highestRow = $sheet->getHighestRow();
        $range = 'A1:F' . $highestRow;
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Alignment & wrap text for all cells
        $sheet->getStyle($range)->getAlignment()->setWrapText(true);
        $sheet->getStyle($range)->getAlignment()->setVertical('top');

        // Center alignment for header rows 1–3
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
            $data->nks_sak2 ?? '',
            $data->nus_ssn ?? '',
            $data->ceklis_ipds == '1' ? 'Sudah' : 'Belum',
            optional($data->waktu_ceklis_ipds)->format('d-m-Y') ?? '',
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
                'Tanggal Ceklis IPDS'
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
