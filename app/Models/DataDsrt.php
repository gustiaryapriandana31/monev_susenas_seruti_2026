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
        'petugas_susenas',
        'petugas_seruti'
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
