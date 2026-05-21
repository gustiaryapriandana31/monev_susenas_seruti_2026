<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('data_dssls', function (Blueprint $table) {
            $table->integer('jumlah_keluarga_awal')->nullable()->after('perkiraan_jumlah_keluarga');
            $table->integer('jumlah_keluarga_hasil_updating')->nullable()->after('jumlah_keluarga_awal');
            $table->integer('jumlah_rumah_tangga_hasil_updating')->nullable()->after('jumlah_keluarga_hasil_updating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_dssls', function (Blueprint $table) {
            $table->dropColumn(['jumlah_keluarga_awal', 'jumlah_keluarga_hasil_updating', 'jumlah_rumah_tangga_hasil_updating']);
        });
    }
};
