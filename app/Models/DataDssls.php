<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataDssls extends Model
{
    protected $table = 'data_dssls';

    protected $fillable = [
        'provinsi',
        'nama_provinsi',
        'kabupaten',
        'nama_kabupaten',
        'kecamatan',
        'nama_kecamatan',
        'desa_kelurahan',
        'nama_desa_kelurahan',
        'klasifikasi_desa(k/p)',
        'strata_konsentrasi_kesejahteraan',
        'kode_sls',
        'kode_sub_sls',
        'nama_sls',
        'nks',
        'perkiraan_jumlah_keluarga',
        'jumlah_keluarga_awal',
        'jumlah_keluarga_hasil_updating',
        'jumlah_rumah_tangga_hasil_updating',
        'sampel_seruti',
        'sampel_sakernas_total',
        'petugas_ppl',
        'petugas_pml',
        'ceklis_lap',
        'waktu_ceklis_lap',
        'ceklis_sosial',
        'waktu_ceklis_sosial',
        'ceklis_ipds',
        'waktu_ceklis_ipds',
        'petugas_entry'
    ];

    protected $casts = [
        'waktu_ceklis_lap' => 'datetime',
        'waktu_ceklis_sosial' => 'datetime',
        'waktu_ceklis_ipds' => 'datetime',
    ];

    public function ppl()
    {
        return $this->belongsTo(PetugasLapangan::class, 'petugas_ppl', 'kode_petugas');
    }

    public function pml()
    {
        return $this->belongsTo(PetugasLapangan::class, 'petugas_pml', 'kode_petugas');
    }

    public function entry()
    {
        return $this->belongsTo(PetugasEntry::class, 'petugas_entry', 'kode_petugas');
    }
}
