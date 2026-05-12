<?php

namespace App\Imports;

use App\Models\DataDsrt;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DataDsrtImport implements ToModel, WithHeadingRow, WithValidation
{
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
        ]);
    }

    public function rules(): array
    {
        return [
            // No unique constraint on IDBS required
        ];
    }
}
