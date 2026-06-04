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
            'D' => 18, // Ceklis Pemutakhiran Lapangan?
            'E' => 18, // Tanggal Ceklis Pemutakhiran Lapangan
            'F' => 15, // Jumlah Keluarga Awal
            'G' => 15, // Jumlah Keluarga Hasil Updating
            'H' => 15, // Jumlah Rumah Tangga Hasil Updating
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge title row — 8 kolom: A–H
        $sheet->mergeCells('A1:H1');

        // Dynamic borders for all rows
        $highestRow = $sheet->getHighestRow();
        $range = 'A1:H' . $highestRow;
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Alignment & wrap text for all cells
        $sheet->getStyle($range)->getAlignment()->setWrapText(true);
        $sheet->getStyle($range)->getAlignment()->setVertical('top');

        // Center alignment for header rows 1–3
        $sheet->getStyle('A1:H3')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:H3')->getAlignment()->setVertical('center');

        // Warna biru untuk 3 kolom terakhir (F–H) di baris 2
        foreach (['F2', 'G2', 'H2'] as $cell) {
            $sheet->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF1F5C99'); // Biru gelap
        }

        return [
            // Row 1: Title
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFED7D31'], // Orange
                ],
            ],
            // Row 2: Headers (A–E orange, F–H dioverride biru di atas)
            2 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFED7D31'], // Orange
                ],
            ],
            // Row 3: Subheaders (No background color / white fill, black bold text)
            3 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFFFFF'], // White
                ],
            ],
        ];
    }

    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        return DataDssls::query()->with(['ppl', 'pml', 'entry'])
            ->orderBy('ceklis_ipds', 'desc');
    }

    public function map($data): array
    {
        return [
            '16',
            '10',
            $data->nks ?? '',
            $data->ceklis_lap == '1' ? 'Sudah' : 'Belum',
            optional($data->waktu_ceklis_lap)->format('Y-m-d') ?? '-',
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
                'Tanggal penerimaan',
                'Jumlah Keluarga Awal',
                'Jumlah Keluarga Hasil Updating',
                'Jumlah Rumah Tangga Hasil Updating',
            ],
            [
                '',
                '',
                '',
                '',
                'TT-BB-TTTT',
                '(Blok II Rinc.1)',
                '(Blok II Rinc.2)',
                '(Blok II Rinc.3)',
            ],
        ];
    }
}
