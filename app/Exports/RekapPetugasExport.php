<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapPetugasExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Rekap Penugasan Petugas Entry';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 35,  // Nama Petugas
            'C' => 18,  // Kode Petugas
            'D' => 25,  // ENTRY PEMUTAKHIRAN
            'E' => 25,  // ENTRY SUSENAS
            'F' => 25,  // ENTRY SERUTI
            'G' => 20,  // TOTAL TUGAS
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge title row
        $sheet->mergeCells('A1:G1');

        $highestRow = $sheet->getHighestRow();
        $range = 'A1:G' . $highestRow;
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Alignment & wrap text
        $sheet->getStyle($range)->getAlignment()->setWrapText(true);
        $sheet->getStyle($range)->getAlignment()->setVertical('center');

        // Center alignment for headers and numeric data
        $sheet->getStyle('A1:G2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A3:A' . $highestRow)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C3:G' . $highestRow)->getAlignment()->setHorizontal('center');

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFED7D31'], // Orange
                ],
            ],
            2 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFED7D31'], // Orange
                ],
            ],
        ];
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->data as $idx => $item) {
            $rows[] = [
                $idx + 1,
                $item['nama'],
                $item['kode'],
                $item['pemutakhiran'],
                $item['susenas'],
                $item['seruti'],
                $item['total'],
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return [
            ['Rekap Penugasan Petugas Entry'],
            [
                'No',
                'Nama Petugas',
                'Kode Petugas',
                'ENTRY PEMUTAKHIRAN',
                'ENTRY SUSENAS',
                'ENTRY SERUTI',
                'TOTAL TUGAS',
            ],
        ];
    }
}
