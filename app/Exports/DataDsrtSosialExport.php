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

class DataDsrtSosialExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Sosial oleh Kabupaten';
    }
    public function columnWidths(): array
    {
        return [
            'A' => 12, // Kode Prop
            'B' => 12, // Kode Kab
            'C' => 18, // Kode NKS
            'D' => 14, // No Urut Ruta
            'E' => 16, // Ceklis Sosial?
            'F' => 16, // Tanggal Ceklis Sosial
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
        return DataDsrt::query()->orderBy('ceklis_sosial', 'desc');
    }

    public function map($data): array
    {
        return [
            '16',
            '10',
            $data->nks_sak22 ?? '',
            $data->nus_ssn ?? '',
            $data->ceklis_sosial == '1' ? 'Sudah' : 'Belum',
            optional($data->waktu_ceklis_sosial)->format('d-m-Y') ?? '',
        ];
    }

    public function headings(): array
    {
        return [
            ['Data Progress Penerimaan Kuesioner Susenas oleh Kabupaten'],
            [
                'Kode Prop',
                'Kode Kab',
                'Kode NKS',
                'No Urut Ruta',
                'Ceklis Sosial?',
                'Tanggal Ceklis Sosial'
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
