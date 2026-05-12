<?php

namespace App\Exports;

use App\Models\DataDsrt;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataDsrtExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
        return DataDsrt::query()->with(['ppl', 'pml', 'susenas', 'seruti']);
    }

    public function map($data): array
    {
        return [
            $data->id,
            $data->kec,
            $data->desa,
            $data->kdbs,
            $data->klas,
            $data->idbs,
            $data->nmkec,
            $data->nmdesa,
            $data->nks_sak22,
            $data->F_SERUTI,
            $data->nmslsm,
            $data->r503,
            $data->r503b,
            $data->dsrt_ssn,
            $data->nus_ssn,
            $data->ppl?->nama_petugas ?? $data->petugas_ppl,
            $data->pml?->nama_petugas ?? $data->petugas_pml,
            $data->ceklis_lap,
            $data->waktu_ceklis_lap,
            $data->ceklis_sosial,
            $data->waktu_ceklis_sosial,
            $data->ceklis_ipds,
            $data->waktu_ceklis_ipds,
            $data->susenas?->nama_petugas ?? $data->petugas_susenas,
            $data->seruti?->nama_petugas ?? $data->petugas_seruti,
            $data->created_at,
            $data->updated_at,
        ];
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
