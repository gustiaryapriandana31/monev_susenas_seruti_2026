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

class DataDsrtLapanganExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Lapangan';
    }
    public function columnWidths(): array
    {
        return [
            'A' => 12, // Kode Prop
            'B' => 12, // Kode Kab
            'C' => 18, // Kode NKS
            'D' => 14, // No Urut Ruta
            'E' => 16, // Ceklis Lapangan
            'F' => 16, // Tanggal Penerimaan
            'G' => 22, // R203 KOR
            'H' => 22, // R203 KP
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge title row (8 columns: A–H)
        $sheet->mergeCells('A1:H1');

        // Dynamic borders for all rows
        $highestRow = $sheet->getHighestRow();
        $range = 'A1:H' . $highestRow;
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Wrap text for ALL cells
        $sheet->getStyle($range)->getAlignment()->setWrapText(true);
        $sheet->getStyle($range)->getAlignment()->setVertical('top');

        // Center alignment for header rows 1–3
        $sheet->getStyle('A1:H3')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:H3')->getAlignment()->setVertical('center');

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
            // Row 4: Enum description - white background, normal text
            4 => [
                'font' => ['bold' => false, 'color' => ['argb' => 'FF000000']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFFFFF'],
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
        return DataDsrt::query()->orderBy('ceklis_lap', 'desc');
    }

    public function map($data): array
    {
        return [
            '16',
            '10',
            $data->nks_sak22 ?? '',
            $data->nus_ssn ?? '',
            $data->ceklis_lap == '1' ? 'Sudah' : 'Belum',
            optional($data->waktu_ceklis_lap)->format('d-m-Y') ?? '',
            $data->r203_kor?->value ?? '',
            $data->r203_kp?->value ?? '',
        ];
    }

    public function headings(): array
    {
        $enumDesc = '1= Terisi Lengkap, 2=Terisi tdk lengkap, 3= Tidak ada ART/responden yang memberikan informasi sampai akhir masa pencacahan, 4= menolak, 5=Ruta pindah';

        return [
            ['Data Progress Pencacahan Susenas'],
            [
                'Kode Prop',
                'Kode Kab',
                'Kode NKS',
                'No Urut Ruta',
                'Ceklis Pencacahan Lapangan?',
                'Tanggal Ceklis Pencacahan Lapangan',
                'Hasil Pencacahan Ruta (R203) KOR',
                'Hasil Pencacahan Ruta (R203) KP',
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
            ],
            [
                '',
                '',
                '',
                '',
                '',
                '',
                $enumDesc,
                $enumDesc,
            ],
        ];
    }
}
