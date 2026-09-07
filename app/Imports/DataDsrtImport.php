<?php

namespace App\Imports;

use App\Models\DataDsrt;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class DataDsrtImport extends DefaultValueBinder implements ToModel, WithHeadingRow, WithValidation, WithCustomValueBinder
{
    /**
     * Cache record yang sudah ditemukan/dibuat selama 1 proses import.
     * Tujuannya agar baris duplikat di file yang sama tidak membuat record baru.
     */
    protected array $processed = [];

    public function bindValue(Cell $cell, $value): bool
    {
        if (is_numeric($value)) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function model(array $row)
    {
        $row = array_map(function ($value) {
            if (is_string($value)) {
                $value = trim($value);
            }

            return $value === '' ? null : $value;
        }, $row);

        // Hanya import DSRT dengan dsrt_ssn = 1.
        $dsrtSsn = $this->intValue($row, 'dsrt_ssn', 0);
        if ($dsrtSsn !== 1) {
            return null;
        }

        $key = $this->logicalKey($row);

        /*
         * Cari record berdasarkan IDENTITAS DSRT yang stabil.
         *
         * Field r503/r503b TIDAK dipakai sebagai kunci karena keduanya
         * dapat diubah dari halaman Data DSRT. Kalau dipakai sebagai kunci,
         * perubahan nilai tersebut akan dianggap record baru dan menyebabkan
         * duplikasi saat Excel di-import ulang.
         *
         * nmslsm juga tidak dipakai sebagai kunci karena merupakan informasi
         * wilayah/nama SLS yang dapat berubah tanpa mengubah identitas ruta.
         */
        if (isset($this->processed[$key])) {
            $model = $this->processed[$key];
        } else {
            $model = DataDsrt::query()
                ->where('kec', $this->intValue($row, 'kec'))
                ->where('desa', $this->intValue($row, 'desa'))
                ->where('kdbs', $this->intValue($row, 'kdbs'))
                ->where('klas', $this->intValue($row, 'klas'))
                ->where('idbs', $this->intValue($row, 'idbs'))
                ->where('nks_sak22', $this->intValue($row, 'nks_sak22'))
                ->where('F_SERUTI', $this->intValue($row, 'f_seruti', $row['F_SERUTI'] ?? null))
                ->where('dsrt_ssn', $dsrtSsn)
                ->where('nus_ssn', $this->intValue($row, 'nus_ssn'))
                ->orderBy('id')
                ->first();

            if (!$model) {
                $model = new DataDsrt();
            }

            $this->processed[$key] = $model;
        }

        $isNew = !$model->exists;

        /*
         * Field sumber dari Excel.
         * Untuk record lama, field-field ini boleh diperbarui mengikuti Excel.
         * Field operasional yang dikerjakan petugas/admin TIDAK disentuh.
         */
        $sourceData = [
            'kec'        => $this->intValue($row, 'kec'),
            'desa'       => $this->intValue($row, 'desa'),
            'kdbs'       => $this->intValue($row, 'kdbs'),
            'klas'       => $this->intValue($row, 'klas'),
            'idbs'       => $this->intValue($row, 'idbs'),
            'nmkec'      => $this->value($row, 'nmkec'),
            'nmdesa'     => $this->value($row, 'nmdesa'),
            'nks_sak22'  => $this->intValue($row, 'nks_sak22'),
            'F_SERUTI'   => $this->intValue($row, 'f_seruti', $row['F_SERUTI'] ?? null),
            'nmslsm'     => $this->value($row, 'nmslsm'),
            'dsrt_ssn'   => $dsrtSsn,
            'nus_ssn'    => $this->intValue($row, 'nus_ssn'),
        ];

        $model->fill($sourceData);

        /*
         * Untuk record BARU, data awal yang memang ada di Excel tetap diimport.
         * Untuk record LAMA, seluruh field di bawah ini sengaja dipertahankan
         * supaya import ulang tidak menghapus/mengubah pekerjaan yang sudah ada.
         */
        if ($isNew) {
            $model->fill([
                'r503'              => $this->value($row, 'r503'),
                'r503b'             => $this->value($row, 'r503b'),
                'petugas_ppl'       => $this->value($row, 'petugas_ppl'),
                'petugas_pml'       => $this->value($row, 'petugas_pml'),
                'ceklis_lap'       => $this->checkbox($row, 'ceklis_lap'),
                'waktu_ceklis_lap' => $this->value($row, 'waktu_ceklis_lap'),
                'ceklis_sosial'    => $this->checkbox($row, 'ceklis_sosial'),
                'waktu_ceklis_sosial' => $this->value($row, 'waktu_ceklis_sosial'),
                'ceklis_ipds'      => $this->checkbox($row, 'ceklis_ipds'),
                'waktu_ceklis_ipds' => $this->value($row, 'waktu_ceklis_ipds'),
                'ceklis_pemeriksaan' => $this->checkbox($row, 'ceklis_pemeriksaan'),
                'waktu_ceklis_pemeriksaan' => $this->value($row, 'waktu_ceklis_pemeriksaan'),
                'petugas_susenas'  => $this->value($row, 'petugas_susenas'),
                'petugas_seruti'   => $this->value($row, 'petugas_seruti'),
                'r203_kor'         => $this->value($row, 'r203_kor'),
                'r203_kp'          => $this->value($row, 'r203_kp'),
                'r301_jumlah_art'  => $this->value($row, 'r301_jumlah_art'),
                'r304_vsen26kp'    => $this->value($row, 'r304_vsen26kp'),
                'r305_vsen26kp'    => $this->value($row, 'r305_vsen26kp'),
                'blok_catatan_kor' => $this->checkbox($row, 'blok_catatan_kor'),
                'blok_catatan_kp'  => $this->checkbox($row, 'blok_catatan_kp'),
            ]);
        }

        $model->save();

        // Simpan instance terbaru supaya baris duplikat dalam file yang sama
        // tetap menggunakan record yang sama.
        $this->processed[$key] = $model;

        return $model;
    }

    /**
     * Kunci identitas DSRT yang stabil.
     * Tidak memasukkan field yang dapat berubah karena pekerjaan pemeriksaan.
     */
    protected function logicalKey(array $row): string
    {
        return implode('|', [
            $this->intValue($row, 'kec'),
            $this->intValue($row, 'desa'),
            $this->intValue($row, 'kdbs'),
            $this->intValue($row, 'klas'),
            $this->intValue($row, 'idbs'),
            $this->intValue($row, 'nks_sak22'),
            $this->intValue($row, 'f_seruti', $row['F_SERUTI'] ?? null),
            $this->intValue($row, 'dsrt_ssn', 0),
            $this->intValue($row, 'nus_ssn'),
        ]);
    }

    protected function value(array $row, string $key, $fallback = null)
    {
        if (array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
            return $row[$key];
        }

        if ($fallback !== null) {
            if (is_string($fallback) && array_key_exists($fallback, $row)) {
                return $row[$fallback];
            }

            return $fallback;
        }

        return null;
    }

    protected function intValue(array $row, string $key, $fallback = null): int
    {
        $value = $this->value($row, $key, $fallback);

        if ($value === null || $value === '') {
            return 0;
        }

        // Excel dapat mengirim kode sebagai "00223", 223, atau 223.0.
        // Untuk field integer database, simpan nilai numeriknya.
        return (int) ((float) str_replace(',', '.', trim((string) $value)));
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
