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
            $table->string('kec')->change();
            $table->string('desa')->change();
            $table->string('kdbs')->change();
            $table->string('idbs')->change();
            $table->string('nks_sak22')->change();
        });

        Schema::table('data_dssls', function (Blueprint $table) {
            $table->string('provinsi')->change();
            $table->string('kabupaten')->change();
            $table->string('kecamatan')->change();
            $table->string('desa_kelurahan')->change();
            $table->string('kode_sls')->change();
            $table->string('kode_sub_sls')->change();
            $table->string('nks')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_dsrts', function (Blueprint $table) {
            $table->integer('kec')->change();
            $table->integer('desa')->change();
            $table->integer('kdbs')->change();
            $table->integer('idbs')->change();
            $table->integer('nks_sak22')->change();
        });

        Schema::table('data_dssls', function (Blueprint $table) {
            $table->integer('provinsi')->change();
            $table->integer('kabupaten')->change();
            $table->integer('kecamatan')->change();
            $table->integer('desa_kelurahan')->change();
            $table->integer('kode_sls')->change();
            $table->integer('kode_sub_sls')->change();
            $table->integer('nks')->change();
        });
    }
};
