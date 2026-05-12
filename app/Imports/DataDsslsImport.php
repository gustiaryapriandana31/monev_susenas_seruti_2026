<?php

namespace App\Imports;

use App\Models\DataDssls;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DataDsslsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Sanitize: convert empty strings to null, as Excel may import them as '' instead of null
        $row = array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $row);

        return new DataDssls([
            'provinsi' => $row['provinsi'] ?? null,
            'nama_provinsi' => $row['nama_provinsi'] ?? null,
            'kabupaten' => $row['kabupaten'] ?? null,
            'nama_kabupaten' => $row['nama_kabupaten'] ?? null,
            'kecamatan' => $row['kecamatan'] ?? null,
            'nama_kecamatan' => $row['nama_kecamatan'] ?? null,
            'desa_kelurahan' => $row['desa_kelurahan'] ?? $row['desakelurahan'] ?? null,
            'nama_desa_kelurahan' => $row['nama_desa_kelurahan'] ?? $row['nama_desakelurahan'] ?? null,
            'klasifikasi_desa(k/p)' => $row['klasifikasi_desa_kp'] ?? $row['klasifikasi_desakp'] ?? $row['klasifikasi_desa(k/p)'] ?? null,
            'strata_konsentrasi_kesejahteraan' => $row['strata_konsentrasi_kesejahteraan'] ?? null,
            'kode_sls' => $row['kode_sls'] ?? null,
            'kode_sub_sls' => $row['kode_sub_sls'] ?? null,
            'nama_sls' => $row['nama_sls'] ?? null,
            'nks' => $row['nks'] ?? null,
            'perkiraan_jumlah_keluarga' => $row['perkiraan_jumlah_keluarga'] ?? null,
            'sampel_seruti' => $row['sampel_seruti'] ?? null,
            'sampel_sakernas_total' => $row['sampel_sakernas_total'] ?? null,
            'petugas_ppl' => $row['petugas_ppl'] ?? null,
            'petugas_pml' => $row['petugas_pml'] ?? null,
            'ceklis_lap' => in_array(strtolower(trim($row['ceklis_lap'] ?? '')), ['v', '1', 'ya', 'yes', 'true', 'x']) ? true : false,
            'waktu_ceklis_lap' => $row['waktu_ceklis_lap'] ?? null,
            'ceklis_sosial' => in_array(strtolower(trim($row['ceklis_sosial'] ?? '')), ['v', '1', 'ya', 'yes', 'true', 'x']) ? true : false,
            'waktu_ceklis_sosial' => $row['waktu_ceklis_sosial'] ?? null,
            'ceklis_ipds' => in_array(strtolower(trim($row['ceklis_ipds'] ?? '')), ['v', '1', 'ya', 'yes', 'true', 'x']) ? true : false,
            'waktu_ceklis_ipds' => $row['waktu_ceklis_ipds'] ?? null,
            'petugas_entry' => $row['petugas_entry'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            // Check NKS uniqueness if it's the identifier, else just check kode_sls as example
            'nks' => 'unique:data_dssls,nks',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nks.unique' => 'Data DSSLS dengan NKS tersebut sudah ada di sistem. Harap cek kembali file Excel Anda.',
        ];
    }
}
