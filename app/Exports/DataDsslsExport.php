<?php

namespace App\Exports;

use App\Models\DataDssls;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataDsslsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFD580'], // Light Orange
                ],
            ],
        ];
    }

    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        return DataDssls::query()->with(['ppl', 'pml', 'entry']);
    }

    public function map($data): array
    {
        return [
            $data->id,
            $data->provinsi,
            $data->nama_provinsi,
            $data->kabupaten,
            $data->nama_kabupaten,
            $data->kecamatan,
            $data->nama_kecamatan,
            $data->desa_kelurahan,
            $data->nama_desa_kelurahan,
            $data->{'klasifikasi_desa(k/p)'},
            $data->strata_konsentrasi_kesejahteraan,
            $data->kode_sls,
            $data->kode_sub_sls,
            $data->nama_sls,
            $data->nks,
            $data->perkiraan_jumlah_keluarga,
            $data->sampel_seruti,
            $data->sampel_sakernas_total,
            $data->ppl?->nama_petugas ?? $data->petugas_ppl,
            $data->pml?->nama_petugas ?? $data->petugas_pml,
            $data->ceklis_lap,
            $data->waktu_ceklis_lap,
            $data->ceklis_sosial,
            $data->waktu_ceklis_sosial,
            $data->ceklis_ipds,
            $data->waktu_ceklis_ipds,
            $data->entry?->nama_petugas ?? $data->petugas_entry,
            $data->created_at,
            $data->updated_at,
        ];
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
