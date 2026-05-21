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

class DataDsrtPemeriksaanExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Pemeriksaan';
    }
    public function columnWidths(): array
    {
        return [
            'A' => 12, // Kode Prop
            'B' => 12, // Kode Kab
            'C' => 18, // Kode NKS
            'D' => 14, // No Urut Ruta
            'E' => 16, // Ceklis Pemeriksaan?
            'F' => 16, // Tanggal Ceklis Pemeriksaan
            'G' => 15, // Jumlah ART (R301)
            'H' => 15, // R304 VSEN26.KP
            'I' => 15, // R305 VSEN26.KP
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Merge title row — 9 kolom: A–I
        $sheet->mergeCells('A1:I1');

        // Dynamic borders for all rows
        $highestRow = $sheet->getHighestRow();
        $range = 'A1:I' . $highestRow;
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Alignment & wrap text for all cells
        $sheet->getStyle($range)->getAlignment()->setWrapText(true);
        $sheet->getStyle($range)->getAlignment()->setVertical('top');

        // Center alignment for header rows 1–3
        $sheet->getStyle('A1:I3')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:I3')->getAlignment()->setVertical('center');

        return [
            // Row 1: Title
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF843C0C'], // Dark Brownish Orange
                ],
            ],
            // Row 2 & 3: Headers
            2 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFED7D31'], // Orange
                ],
            ],
            3 => [
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
        return DataDsrt::query()->orderBy('updated_at', 'desc');
    }

    public function map($data): array
    {
        return [
            '16',
            '10',
            $data->nks_sak22 ?? '',
            $data->nus_ssn ?? '',
            $data->ceklis_pemeriksaan == '1' ? 'Sudah' : 'Belum',
            optional($data->waktu_ceklis_pemeriksaan)->format('d-m-Y') ?? '',
            $data->r301_jumlah_art ?? '',
            $data->r304_vsen26kp ?? '',
            $data->r305_vsen26kp ?? '',
        ];
    }

    public function headings(): array
    {
        return [
            ['Data Progress Pemeriksaan Kuesioner Susenas'],
            [
                'Kode Prop',
                'Kode Kab',
                'Kode NKS',
                'No Urut Ruta',
                'Ceklis Pemeriksaan?',
                'Tanggal Ceklis Pemeriksaan',
                'Jumlah ART (R301)',
                'R304 VSEN26.KP',
                'R305 VSEN26.KP',
            ],
            [
                '',
                '',
                '',
                '',
                '',
                'TT-BB-TTTT',
                '',
                '',
                ''
            ]
        ];
    }
}
