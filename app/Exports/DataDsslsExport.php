<?php

namespace App\Exports;

use App\Models\DataDssls;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataDsslsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Pemutakhiran Lapangan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10, // Kode Prop
            'B' => 10, // Kode Kab
            'C' => 15, // Kode NKS
            'D' => 18, // Sudah Selesai 1 BS? [sudah/belum]
            'E' => 15, // Jumlah Keluarga Awal
            'F' => 15, // Jumlah Keluarga Hasil Updating
            'G' => 15, // Jumlah Rumah Tangga Hasil Updating
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge title row — 7 kolom: A–G
        $sheet->mergeCells('A1:G1');

        $highestRow = $sheet->getHighestRow();
        $range = 'A1:G' . $highestRow;
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(
            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        );
        $sheet->getStyle($range)->getAlignment()->setWrapText(true);
        $sheet->getStyle($range)->getAlignment()->setVertical('top');
        $sheet->getStyle('A1:G3')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:G3')->getAlignment()->setVertical('center');

        foreach (['E2', 'F2', 'G2'] as $cell) {
            $sheet->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF1F5C99');
        }

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFED7D31'],
                ],
            ],
            2 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFED7D31'],
                ],
            ],
            3 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFFFFF'],
                ],
            ],
        ];
    }

    public function query()
    {
        return DataDssls::query()
            ->with(['ppl', 'pml', 'entry'])
            ->orderBy('ceklis_ipds', 'desc');
    }

    public function map($data): array
    {
        // Keep the source value as a string so leading zeroes are preserved.
        $nks = $data->nks === null ? '' : (string) $data->nks;

        // The apostrophe is intentionally part of the exported cell value.
        // Do not cast the first three values to numeric values.
        $kodeProp = "'16";
        $kodeKab  = "'10";
        $kodeNks  = "'" . $nks;

        // Read the actual checkbox state from the DSSLS record.
        $sudahSelesai = ((string) $data->ceklis_lap === '1') ? 'sudah' : 'belum';

        return [
            $kodeProp,
            $kodeKab,
            $kodeNks,
            $sudahSelesai,
            $data->jumlah_keluarga_awal ?? '',
            $data->jumlah_keluarga_hasil_updating ?? '',
            $data->jumlah_rumah_tangga_hasil_updating ?? '',
        ];
    }

    public function headings(): array
    {
        return [
            ['Data Progress Pemutakhiran Rumah Tangga'],
            [
                'kode prop [2 digit]',
                'kode kab [2 digit]',
                'kode NKS [5 digit]',
                'Sudah Selesai 1 BS? [sudah/belum]',
                'Jumlah Keluarga Awal',
                'Jumlah Keluarga Hasil Updating',
                'Jumlah Rumah Tangga Hasil Updating',
            ],
            [
                '',
                '',
                '',
                '',
                '(Blok II Rinc.1)',
                '(Blok II Rinc.2)',
                '(Blok II Rinc.3)',
            ],
        ];
    }
}
