<?php

namespace App\Imports;

use App\Models\DataDsrt;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class DataDsrtImport extends DefaultValueBinder implements ToModel, WithHeadingRow, WithValidation, WithCustomValueBinder
{
    protected array $occurrences = [];

    public function bindValue(Cell $cell, $value)
    {
        /*
         * NBS/NKS harus mengikuti tampilan/nilai asli di Excel.
         * Contoh: sel Excel yang berisi 00223 dibaca sebagai "00223",
         * bukan 223. getFormattedValue() juga menghormati format angka
         * seperti 00000 bila file Excel menyimpannya sebagai angka.
         */
        $column = strtoupper($cell->getColumn());
        $worksheet = $cell->getWorksheet();
        $heading = strtolower(trim((string) $worksheet->getCell($column . '1')->getValue()));

        if (in_array($heading, ['kdbs', 'nks_sak22'], true)) {
            $formatted = $cell->getFormattedValue();
            $cell->setValueExplicit((string) $formatted, DataType::TYPE_STRING);
            return true;
        }

        if (is_numeric($value)) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function model(array $row)
    {
        $row = array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $row);

        // Hanya import baris DSRT yang dsrt_ssn-nya bernilai 1.
        // Baris dengan dsrt_ssn = 0 (atau nilai selain 1) dilewati.
        $dsrtSsn = $this->value($row, 'dsrt_ssn', null, 0);
        if ((string) $dsrtSsn !== '1') {
            return null;
        }

        $key = $this->logicalKey($row);
        $occurrence = $this->occurrences[$key] ?? 0;
        $this->occurrences[$key] = $occurrence + 1;

        $query = DataDsrt::query()
            ->where('kec', $this->value($row, 'kec'))
            ->where('desa', $this->value($row, 'desa'))
            ->where('kdbs', $this->value($row, 'kdbs'))
            ->where('klas', $this->value($row, 'klas'))
            ->where('idbs', $this->value($row, 'idbs'))
            ->where('nks_sak22', $this->codeValue($row, 'nks_sak22'))
            ->where('F_SERUTI', $this->value($row, 'f_seruti', 'F_SERUTI'))
            ->where('nmslsm', $this->value($row, 'nmslsm'))
            ->where('r503', $this->value($row, 'r503'))
            ->where('r503b', $this->value($row, 'r503b'))
            ->where('dsrt_ssn', $this->value($row, 'dsrt_ssn', null, 0))
            ->where('nus_ssn', $this->value($row, 'nus_ssn', null, 0))
            ->orderBy('id');

        $model = $query->get()->get($occurrence) ?? new DataDsrt();

        // Hanya field sumber/import yang di-update.
        // Progress ceklis + timestamp SENGAJA tidak disentuh agar import ulang
        // tidak menghapus progress yang sudah dikerjakan petugas.
        $model->fill([
            'kec' => $this->value($row, 'kec'),
            'desa' => $this->value($row, 'desa'),
            'kdbs' => $this->codeValue($row, 'kdbs'),
            'klas' => $this->value($row, 'klas'),
            'idbs' => $this->value($row, 'idbs'),
            'nmkec' => $this->value($row, 'nmkec'),
            'nmdesa' => $this->value($row, 'nmdesa'),
            'nks_sak22' => $this->codeValue($row, 'nks_sak22'),
            'F_SERUTI' => $this->value($row, 'f_seruti', 'F_SERUTI'),
            'nmslsm' => $this->value($row, 'nmslsm'),
            'r503' => $this->value($row, 'r503'),
            'r503b' => $this->value($row, 'r503b'),
            'dsrt_ssn' => $this->value($row, 'dsrt_ssn', null, 0),
            'nus_ssn' => $this->value($row, 'nus_ssn', null, 0),
            'petugas_ppl' => $this->value($row, 'petugas_ppl'),
            'petugas_pml' => $this->value($row, 'petugas_pml'),
            'petugas_susenas' => $this->value($row, 'petugas_susenas'),
            'petugas_seruti' => $this->value($row, 'petugas_seruti'),
            'r203_kor' => $this->value($row, 'r203_kor'),
            'r203_kp' => $this->value($row, 'r203_kp'),
            'r301_jumlah_art' => $this->value($row, 'r301_jumlah_art'),
            'r304_vsen26kp' => $this->value($row, 'r304_vsen26kp'),
            'r305_vsen26kp' => $this->value($row, 'r305_vsen26kp'),
            'blok_catatan_kor' => $this->checkbox($row, 'blok_catatan_kor'),
            'blok_catatan_kp' => $this->checkbox($row, 'blok_catatan_kp'),
        ]);

        return $model;
    }

    protected function logicalKey(array $row): string
    {
        return sha1(json_encode([
            $this->value($row, 'kec'),
            $this->value($row, 'desa'),
            $this->value($row, 'kdbs'),
            $this->value($row, 'klas'),
            $this->value($row, 'idbs'),
            $this->codeValue($row, 'nks_sak22'),
            $this->value($row, 'f_seruti', 'F_SERUTI'),
            $this->value($row, 'nmslsm'),
            $this->value($row, 'r503'),
            $this->value($row, 'r503b'),
            $this->value($row, 'dsrt_ssn', null, 0),
            $this->value($row, 'nus_ssn', null, 0),
        ], JSON_UNESCAPED_UNICODE));
    }

    /**
     * Kode NBS/NKS disimpan PERSIS seperti yang dibaca dari Excel.
     * Tidak melakukan padding, trimming angka, atau perubahan format.
     * Jika Excel berisi 00223 sebagai teks, database menerima 00223.
     */
    protected function codeValue(array $row, string $key): ?string
    {
        if (!array_key_exists($key, $row) || $row[$key] === null) {
            return null;
        }

        // Hanya ubah tipe menjadi string; jangan mengubah isi kodenya.
        return (string) $row[$key];
    }

    protected function value(array $row, string $key, ?string $fallback = null, $default = null)
    {
        if (array_key_exists($key, $row) && $row[$key] !== null) {
            return $row[$key];
        }

        if ($fallback !== null && array_key_exists($fallback, $row) && $row[$fallback] !== null) {
            return $row[$fallback];
        }

        return $default;
    }

    protected function checkbox(array $row, string $key): bool
    {
        return in_array(
            strtolower(trim((string) ($this->value($row, $key) ?? ''))),
            ['v', '1', 'ya', 'yes', 'true', 'x'],
            true
        );
    }

    public function rules(): array
    {
        return [];
    }
}
