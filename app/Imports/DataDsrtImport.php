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
    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Sanitize empty strings to null
        $row = array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $row);

        return new DataDsrt([
            'kec' => $row['kec'] ?? null,
            'desa' => $row['desa'] ?? null,
            'kdbs' => $row['kdbs'] ?? null,
            'klas' => $row['klas'] ?? null,
            'idbs' => $row['idbs'] ?? null,
            'nmkec' => $row['nmkec'] ?? null,
            'nmdesa' => $row['nmdesa'] ?? null,
            'nks_sak22' => $row['nks_sak22'] ?? null,
            'F_SERUTI' => $row['f_seruti'] ?? $row['F_SERUTI'] ?? null,
            'nmslsm' => $row['nmslsm'] ?? null,
            'r503' => $row['r503'] ?? null,
            'r503b' => $row['r503b'] ?? null,
            'dsrt_ssn' => $row['dsrt_ssn'] ?? null,
            'nus_ssn' => $row['nus_ssn'] ?? null,
            'petugas_ppl' => $row['petugas_ppl'] ?? null,
            'petugas_pml' => $row['petugas_pml'] ?? null,
            'ceklis_lap' => in_array(strtolower(trim($row['ceklis_lap'] ?? '')), ['v', '1', 'ya', 'yes', 'true', 'x']) ? true : false,
            'waktu_ceklis_lap' => $row['waktu_ceklis_lap'] ?? null,
            'ceklis_sosial' => in_array(strtolower(trim($row['ceklis_sosial'] ?? '')), ['v', '1', 'ya', 'yes', 'true', 'x']) ? true : false,
            'waktu_ceklis_sosial' => $row['waktu_ceklis_sosial'] ?? null,
            'ceklis_ipds' => in_array(strtolower(trim($row['ceklis_ipds'] ?? '')), ['v', '1', 'ya', 'yes', 'true', 'x']) ? true : false,
            'waktu_ceklis_ipds' => $row['waktu_ceklis_ipds'] ?? null,
            'petugas_susenas' => $row['petugas_susenas'] ?? null,
            'petugas_seruti' => $row['petugas_seruti'] ?? null,
            'r203_kor' => $row['r203_kor'] ?? null,
            'r203_kp' => $row['r203_kp'] ?? null,
            'r301_jumlah_art' => $row['r301_jumlah_art'] ?? null,
            'r304_vsen26kp' => $row['r304_vsen26kp'] ?? null,
            'r305_vsen26kp' => $row['r305_vsen26kp'] ?? null,
            'blok_catatan_kor' => in_array(strtolower(trim($row['blok_catatan_kor'] ?? '')), ['v', '1', 'ya', 'yes', 'true', 'x']) ? true : false,
            'blok_catatan_kp' => in_array(strtolower(trim($row['blok_catatan_kp'] ?? '')), ['v', '1', 'ya', 'yes', 'true', 'x']) ? true : false,
        ]);
    }

    public function rules(): array
    {
        return [
            // No unique constraint on IDBS required
        ];
    }
}
