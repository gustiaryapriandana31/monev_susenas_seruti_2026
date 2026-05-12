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
        Schema::create('data_dssls', function (Blueprint $table) {
            $table->id();
            $table->integer('provinsi');
            $table->string('nama_provinsi');
            $table->integer('kabupaten');
            $table->string('nama_kabupaten');
            $table->integer('kecamatan');
            $table->string('nama_kecamatan');
            $table->integer('desa_kelurahan');
            $table->string('nama_desa_kelurahan');
            $table->string('klasifikasi_desa(k/p)');
            $table->string('strata_konsentrasi_kesejahteraan');
            $table->integer('kode_sls');
            $table->integer('kode_sub_sls');
            $table->string('nama_sls');
            $table->integer('nks');
            $table->integer('perkiraan_jumlah_keluarga');
            $table->integer('sampel_seruti')->nullable();
            $table->integer('sampel_sakernas_total')->nullable();
            $table->string('petugas_ppl')->nullable();
            $table->string('petugas_pml')->nullable();
            $table->boolean('ceklis_lap')->default(false);
            $table->dateTime('waktu_ceklis_lap')->nullable();
            $table->boolean('ceklis_sosial')->default(false);
            $table->dateTime('waktu_ceklis_sosial')->nullable();
            $table->boolean('ceklis_ipds')->default(false);
            $table->dateTime('waktu_ceklis_ipds')->nullable();
            $table->string('petugas_entry')->nullable();

            $table->foreign('petugas_ppl')
                ->references('kode_petugas')
                ->on('petugas_lapangans')
                ->cascadeOnDelete();

            $table->foreign('petugas_pml')
                ->references('kode_petugas')
                ->on('petugas_lapangans')
                ->cascadeOnDelete();

            $table->foreign('petugas_entry')
                ->references('kode_petugas')
                ->on('petugas_entries')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_dssls');
    }
};
