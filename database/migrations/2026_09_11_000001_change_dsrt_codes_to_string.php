<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // NBS/NKS adalah kode, bukan angka hitung. Simpan sebagai teks
        // agar leading zero seperti 00223 tidak pernah hilang.
        if (Schema::hasColumn('data_dsrts', 'kdbs')) {
            DB::statement("ALTER TABLE data_dsrts MODIFY kdbs VARCHAR(50) NOT NULL");
        }

        if (Schema::hasColumn('data_dsrts', 'nks_sak22')) {
            DB::statement("ALTER TABLE data_dsrts MODIFY nks_sak22 VARCHAR(50) NOT NULL");
        }
    }

    public function down(): void
    {
        // Jangan mengembalikan ke integer karena dapat menghilangkan leading zero.
        // Rollback sengaja dibiarkan aman/tidak destruktif.
    }
};
