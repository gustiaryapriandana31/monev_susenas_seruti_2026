<?php

namespace App\Exports;

use App\Models\DataDssls;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataDsslsOriExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'IPDS';
    }

    public function styles(Worksheet $sheet)
    {
        // Dynamic borders for all rows
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:AF' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

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
        return DataDssls::query()->with(['ppl', 'pml', 'entry'])
            ->orderBy('ceklis_ipds', 'desc');
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
            $data->jumlah_keluarga_awal,
            $data->jumlah_keluarga_hasil_updating,
            $data->jumlah_rumah_tangga_hasil_updating,
            $data->sampel_seruti,
            $data->sampel_sakernas_total,
            $data->ppl?->nama_petugas ?? $data->petugas_ppl,
            $data->pml?->nama_petugas ?? $data->petugas_pml,
            $data->ceklis_lap == '1' ? 'Sudah' : 'Belum',
            optional($data->waktu_ceklis_lap)->format('Y-m-d') ?? '-',
            $data->ceklis_sosial == '1' ? 'Sudah' : 'Belum',
            optional($data->waktu_ceklis_sosial)->format('Y-m-d') ?? '-',
            $data->ceklis_ipds == '1' ? 'Sudah' : 'Belum',
            optional($data->waktu_ceklis_ipds)->format('Y-m-d') ?? '-',
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
            'Jumlah Keluarga Awal',
            'Jumlah Keluarga Hasil Updating',
            'Jumlah Rumah Tangga Hasil Updating',
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
