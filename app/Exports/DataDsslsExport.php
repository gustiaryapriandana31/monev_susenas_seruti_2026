<?php

namespace App\Exports;

use App\Models\DataDssls;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DataDsslsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DataDssls::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Provinsi',
            'Nama Provinsi',
            'Kabupaten',
            'Nama Kabupaten',
            'Kecamatan',
            'Nama Kecamatan',
            'Desa/Kelurahan',
            'Nama Desa/Kelurahan',
            'Klasifikasi Desa (K/P)',
            'Strata Konsentrasi Kesejahteraan',
            'Kode SLS',
            'Kode Sub SLS',
            'Nama SLS',
            'NKS',
            'Perkiraan Jumlah Keluarga',
            'Sampel Seruti',
            'Sampel Sakernas Total',
            'Petugas PPL',
            'Petugas PML',
            'Ceklis Lapangan',
            'Waktu Ceklis Lapangan',
            'Ceklis Sosial',
            'Waktu Ceklis Sosial',
            'Ceklis IPDS',
            'Waktu Ceklis IPDS',
            'Petugas Entry',
            'Created At',
            'Updated At',
        ];
    }
}
