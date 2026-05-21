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
        Schema::table('data_dsrts', function (Blueprint $table) {
            $table->boolean('ceklis_pemeriksaan')->default(false)->after('waktu_ceklis_ipds');
            $table->dateTime('waktu_ceklis_pemeriksaan')->nullable()->after('ceklis_pemeriksaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_dsrts', function (Blueprint $table) {
            $table->dropColumn(['ceklis_pemeriksaan', 'waktu_ceklis_pemeriksaan']);
        });
    }
};
