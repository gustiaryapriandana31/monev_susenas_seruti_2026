<?php

namespace App\Imports;

use App\Models\PetugasLapangan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PetugasLapanganImport implements ToModel, WithHeadingRow, WithValidation
{
    private function getValue(array $row, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row)) {
                return $row[$key];
            }
        }
        
        // Coba cari nama kolom secara parsial/kasar untuk antisipasi spasi tersembunyi
        foreach ($row as $rowKey => $val) {
            $cleanRowKey = str_replace(['_', ' '], '', strtolower($rowKey));
            foreach ($keys as $key) {
                $cleanKey = str_replace(['_', ' '], '', strtolower($key));
                if ($cleanRowKey === $cleanKey || str_contains($cleanRowKey, $cleanKey)) {
                    return $val;
                }
            }
        }

        return null;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new PetugasLapangan([
            'kode_petugas' => $this->getValue($row, ['kode_petugas']),
            'provinsi' => $this->getValue($row, ['provinsi']),
            'kabupaten' => $this->getValue($row, ['kabupaten']),
            'nama_petugas' => $this->getValue($row, ['nama_petugas']),
            'no_hp' => $this->getValue($row, ['no_hp']),
            'kode_jabatan' => $this->getValue($row, ['kode_jabatan']),
            'jabatan' => $this->getValue($row, ['jabatan']),
            'status' => $this->getValue($row, ['status']),
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_petugas' => 'unique:petugas_lapangans,kode_petugas',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kode_petugas.unique' => 'Data petugas dengan kode ini sudah ada di sistem. Gagal import duplikat.',
        ];
    }
}
