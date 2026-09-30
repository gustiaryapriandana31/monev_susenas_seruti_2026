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
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;

class DataDsrtIPDSExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithCustomValueBinder
{
    /**
     * Force only the first three data columns to be real Excel text values.
     * This prevents values such as 16/10 from being auto-converted to numbers,
     * while quotePrefix makes Excel display the text without showing the apostrophe.
     */
    public function bindValue(Cell $cell, $value)
    {
        $column = $cell->getColumn();
        $row = $cell->getRow();

        if ($row >= 4 && in_array($column, ['A', 'B', 'C'], true)) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            $cell->getStyle()->setQuotePrefix(true);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

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

        // Tiga kode awal harus menjadi text dengan Excel quote prefix.
        // Apostrophe bukan bagian dari isi cell, sehingga tidak terlihat di cell
        // tetapi tetap diperlakukan sebagai prefix teks oleh Excel.
        if ($highestRow >= 4) {
            $sheet->getStyle('A4:C' . $highestRow)->setQuotePrefix(true);
        }

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
        // Return query for export sorted numerically by Kode NKS, then No Urut Ruta.
        // Both columns are stored as strings in the database, so cast them to
        // unsigned integers for numeric sorting without changing their output format.
        return DataDsrt::query()
            ->uniqueForExport()
            ->orderByRaw('CAST(nks_sak22 AS UNSIGNED) ASC')
            ->orderByRaw('CAST(nus_ssn AS UNSIGNED) ASC');
    }

    public function map($data): array
    {
        return [
            '16',
            '10',
            str_pad((string) ($data->nks_sak22 ?? ''), 5, '0', STR_PAD_LEFT),
            $data->nus_ssn ?? '',
            $data->ceklis_ipds == '1' ? 'sudah' : 'belum',
            optional($data->waktu_ceklis_ipds)->format('d-m-Y') ? "'" . optional($data->waktu_ceklis_ipds)->format('d-m-Y') : '',
        ];
    }

    public function headings(): array
    {
        return [
            ['Data Progress Penerimaan Kuesioner Susenas oleh IPDS'],
            [
                'kode prop [2 digit]',
                'kode kab [2 digit]',
                'kode NKS [5 digit]',
                'No Urut Ruta [max: 2 digit]',
                'Sudah Selesai? [sudah/belum]',
                'Tanggal penerimaan'
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
