<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetugasLapangan extends Model
{
    // 1. Beritahu Laravel nama primary key-nya
    protected $primaryKey = 'kode_petugas';

    // 2. Beritahu Laravel bahwa primary key-nya bukan integer (karena string)
    protected $keyType = 'string';

    // 3. Beritahu Laravel bahwa primary key ini tidak auto-increment
    public $incrementing = false;

    protected $fillable = [
        'kode_petugas',
        'provinsi',
        'kabupaten',
        'nama_petugas',
        'no_hp',
        'kode_jabatan',
        'jabatan',
        'status',
    ];
}
