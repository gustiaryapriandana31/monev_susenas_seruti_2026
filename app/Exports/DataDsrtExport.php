<?php

namespace App\Exports;

use App\Models\DataDsrt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DataDsrtExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DataDsrt::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Kecamatan',
            'Desa',
            'KDBS',
            'Klas',
            'IDBS',
            'Nama Kecamatan',
            'Nama Desa',
            'NKS SAK22',
            'F SERUTI',
            'Nama SLS',
            'R503',
            'R503B',
            'DSRT SSN',
            'NUS SSN',
            'Petugas PPL',
            'Petugas PML',
            'Ceklis Lapangan',
            'Waktu Ceklis Lapangan',
            'Ceklis Sosial',
            'Waktu Ceklis Sosial',
            'Ceklis IPDS',
            'Waktu Ceklis IPDS',
            'Petugas Susenas',
            'Petugas Seruti',
            'Created At',
            'Updated At',
        ];
    }
}
