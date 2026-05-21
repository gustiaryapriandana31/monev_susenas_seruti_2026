<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataDsrt extends Model
{
    protected $table = 'data_dsrts';

    protected $fillable = [
        'kec',
        'desa',
        'kdbs',
        'klas',
        'idbs',
        'nmkec',
        'nmdesa',
        'nks_sak22',
        'F_SERUTI',
        'nmslsm',
        'r503',
        'r503b',
        'r203_kor',
        'r203_kp',
        'r301_jumlah_art',
        'r304_vsen26kp',
        'r305_vsen26kp',
        'blok_catatan_kor',
        'blok_catatan_kp',
        'dsrt_ssn',
        'nus_ssn',
        'petugas_ppl',
        'petugas_pml',
        'ceklis_lap',
        'waktu_ceklis_lap',
        'ceklis_sosial',
        'waktu_ceklis_sosial',
        'ceklis_ipds',
        'waktu_ceklis_ipds',
        'ceklis_pemeriksaan',
        'waktu_ceklis_pemeriksaan',
        'petugas_susenas',
        'petugas_seruti',
    ];

    protected $casts = [
        'waktu_ceklis_lap' => 'datetime',
        'waktu_ceklis_sosial' => 'datetime',
        'waktu_ceklis_ipds' => 'datetime',
        'waktu_ceklis_pemeriksaan' => 'datetime',
        'r203_kor' => \App\Enums\R203Status::class,
        'r203_kp' => \App\Enums\R203Status::class,
        'blok_catatan_kor' => 'boolean',
        'blok_catatan_kp' => 'boolean',
    ];

    public function ppl()
    {
        return $this->belongsTo(PetugasLapangan::class, 'petugas_ppl', 'kode_petugas');
    }

    public function pml()
    {
        return $this->belongsTo(PetugasLapangan::class, 'petugas_pml', 'kode_petugas');
    }

    public function susenas()
    {
        return $this->belongsTo(PetugasEntry::class, 'petugas_susenas', 'kode_petugas');
    }

    public function seruti()
    {
        return $this->belongsTo(PetugasEntry::class, 'petugas_seruti', 'kode_petugas');
    }
}
