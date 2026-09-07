<?php

namespace App\Services;

use App\Models\DataDsrt;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DsrtTemplateExportService
{
    private const TEMPLATES = [
        'sosial' => 'template_dokkab.xls',
        'sosial-kab' => 'template_dokkirimkab.xls',
        'lapangan' => 'template_cacah.xls',
        'pemeriksaan' => 'template_periksa.xls',
    ];

    public function download(string $type): StreamedResponse
    {
        if (!isset(self::TEMPLATES[$type])) {
            abort(404, 'Template export DSRT tidak ditemukan.');
        }

        $template = resource_path('templates/dsrt/' . self::TEMPLATES[$type]);

        if (!is_file($template)) {
            abort(500, 'File template Excel DSRT tidak tersedia.');
        }

        $spreadsheet = IOFactory::load($template);

        // Template memiliki sheet "contoh" sebagai sheet aktif. Pastikan file hasil
        // langsung membuka sheet "data", tempat seluruh hasil export ditulis.
        $dataSheet = $spreadsheet->getSheetByName('data');
        if ($dataSheet) {
            $spreadsheet->setActiveSheetIndex($spreadsheet->getIndex($dataSheet));
        }
        $sheet = $dataSheet ?? $spreadsheet->getActiveSheet();

        // Template asli memiliki header pada baris 1-3 dan baris data mulai dari 4.
        $this->clearDataRows($sheet, 4);

        $rowNumber = 4;
        foreach ($this->query($type)->cursor() as $data) {
            if ($rowNumber > 4) {
                $this->copyTemplateRowStyle($sheet, 4, $rowNumber, $this->columnCount($type));
            }

            $values = $this->map($type, $data);

            foreach ($values as $index => $value) {
                $this->setCellValue($sheet, $rowNumber, $index + 1, $value, $index < 3);
            }

            $rowNumber++;
        }

        // Pastikan baris data terakhir tetap memiliki style template.
        $lastDataRow = max(4, $rowNumber - 1);
        $sheet->getStyle('A4:' . $this->columnLetter($this->columnCount($type)) . $lastDataRow)
            ->getAlignment()->setVertical('top');

        $filename = $this->outputFilename($type);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function query(string $type)
    {
        return match ($type) {
            'sosial' => DataDsrt::query()->orderBy('ceklis_sosial', 'desc'),
            'sosial-kab' => DataDsrt::query()->orderBy('ceklis_sosial', 'desc'),
            'lapangan' => DataDsrt::query()->orderBy('ceklis_lap', 'desc'),
            'pemeriksaan' => DataDsrt::query()
                // Export Pemeriksaan hanya berisi ruta yang sudah dicentang pada
                // kolom Pemeriksaan di tabel DSRT.
                ->where('ceklis_pemeriksaan', true)
                ->orderBy('updated_at', 'desc'),
        };
    }

    private function map(string $type, DataDsrt $data): array
    {
        return match ($type) {
            'sosial' => [
                $this->code('16'),
                $this->code('02'),
                $this->code($this->fiveDigit($data->nks_sak22)),
                $data->nus_ssn ?? '',
                $data->ceklis_sosial ? 'sudah' : 'belum',
                $data->waktu_ceklis_sosial?->format('d-m-Y') ?? '',
            ],

            'sosial-kab' => [
                $this->code('16'),
                $this->code('02'),
                $this->code($this->fiveDigit($data->nks_sak22)),
                $data->nus_ssn ?? '',
                $data->ceklis_sosial ? 'sudah' : 'belum',
                $data->blok_catatan_kor ? 1 : 0,
                $data->blok_catatan_kp ? 1 : 0,
                $data->waktu_ceklis_sosial?->format('d-m-Y') ?? '',
            ],

            'lapangan' => [
                $this->code('16'),
                $this->code('02'),
                $this->code($this->fiveDigit($data->nks_sak22)),
                $data->nus_ssn ?? '',
                $data->ceklis_lap ? 'sudah' : 'belum',
                $data->r203_kor?->value ?? '',
                $data->r203_kp?->value ?? '',
            ],

            'pemeriksaan' => [
                $this->code('16'),
                $this->code('02'),
                $this->code($this->fiveDigit($data->nks_sak22)),
                $data->nus_ssn ?? '',
                $data->ceklis_pemeriksaan ? 'sudah' : 'belum',
                $data->r301_jumlah_art ?? '',
                // Template periksa kolom G = R407 (Jml ART usia 2-4 tahun).
                // Di project, nilai yang ditampilkan/edit pada kolom ini disimpan
                // pada field r304_vsen26kp.
                $data->r304_vsen26kp ?? '',
                // Template periksa kolom H = jumlah ART berkode 2 pada R503.
                // Nilai yang ditampilkan/edit pada kolom ini disimpan pada
                // field r305_vsen26kp.
                $data->r305_vsen26kp ?? '',
            ],
        };
    }

    private function fiveDigit($value): string
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return '';
        }

        return str_pad($value, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Tiga kode pertama sengaja diberi apostrophe sebagai bagian dari value.
     * Ditulis sebagai string eksplisit agar Excel tidak mengonversinya menjadi angka.
     */
    private function code($value): string
    {
        $value = $value === null ? '' : (string) $value;
        return "'" . $value;
    }

    private function setCellValue(Worksheet $sheet, int $row, int $column, $value, bool $isCode): void
    {
        $cell = $sheet->getCellByColumnAndRow($column, $row);

        // Baris data pada template memang memakai font putih di A:E (baris
        // contoh/template). Untuk data hasil export, nilainya harus terlihat.
        $cell->getStyle()->getFont()->getColor()->setARGB('FF000000');

        if ($isCode) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            $cell->setDataType(DataType::TYPE_STRING);
            $cell->getStyle()->getNumberFormat()->setFormatCode('@');
            return;
        }

        // Tanggal dan field teks ditulis sebagai string agar mengikuti tampilan
        // template dan tidak diubah menjadi serial number Excel.
        if ($value === null) {
            $value = '';
        }

        if (is_int($value) || is_float($value)) {
            $cell->setValue($value);
        } else {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
        }
    }

    private function clearDataRows(Worksheet $sheet, int $startRow): void
    {
        $highestRow = max($sheet->getHighestRow(), $startRow);

        for ($row = $startRow; $row <= $highestRow; $row++) {
            foreach (range(1, $sheet->getHighestColumn() === 'A' ? 1 : \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn())) as $column) {
                $sheet->getCellByColumnAndRow($column, $row)->setValue(null);
            }
        }
    }

    private function copyTemplateRowStyle(Worksheet $sheet, int $sourceRow, int $targetRow, int $columnCount): void
    {
        $lastColumn = $this->columnLetter($columnCount);
        $sheet->duplicateStyle(
            $sheet->getStyle("A{$sourceRow}:{$lastColumn}{$sourceRow}"),
            "A{$targetRow}:{$lastColumn}{$targetRow}"
        );

        if ($sheet->getRowDimension($sourceRow)->getRowHeight() !== null) {
            $sheet->getRowDimension($targetRow)->setRowHeight(
                $sheet->getRowDimension($sourceRow)->getRowHeight()
            );
        }
    }

    private function columnCount(string $type): int
    {
        return match ($type) {
            'sosial' => 6,
            'sosial-kab' => 8,
            'lapangan' => 7,
            'pemeriksaan' => 8,
        };
    }

    private function columnLetter(int $column): string
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column);
    }

    private function outputFilename(string $type): string
    {
        return match ($type) {
            'sosial' => 'Export Data DSRT Sosial Penerimaan oleh Kabupaten.xls',
            'sosial-kab' => 'Export Data DSRT Sosial Pengiriman ke Kabupaten.xls',
            'lapangan' => 'Export Data DSRT untuk Lapangan.xls',
            'pemeriksaan' => 'Export Data DSRT untuk Pemeriksaan.xls',
        };
    }
}
