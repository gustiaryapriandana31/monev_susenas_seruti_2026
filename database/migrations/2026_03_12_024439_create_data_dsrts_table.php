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
        Schema::create('data_dsrts', function (Blueprint $table) {
            $table->id();
            $table->integer('kec');
            $table->integer('desa');
            $table->integer('kdbs');
            $table->integer('klas');
            $table->integer('idbs');
            $table->string('nmkec');
            $table->string('nmdesa');
            $table->integer('nks_sak22');
            $table->integer('F_SERUTI');
            $table->string('nmslsm');
            $table->string('r503')->nullable();
            $table->string('r503b')->nullable();
            $table->integer('dsrt_ssn');
            $table->integer('nus_ssn');
            $table->string('petugas_ppl')->nullable();
            $table->string('petugas_pml')->nullable();
            $table->boolean('ceklis_lap')->default(false);
            $table->dateTime('waktu_ceklis_lap')->nullable();
            $table->boolean('ceklis_sosial')->default(false);
            $table->dateTime('waktu_ceklis_sosial')->nullable();
            $table->boolean('ceklis_ipds')->default(false);
            $table->dateTime('waktu_ceklis_ipds')->nullable();
            $table->string('petugas_susenas')->nullable();
            $table->string('petugas_seruti')->nullable();

            $table->foreign('petugas_ppl')
                ->references('kode_petugas')
                ->on('petugas_lapangans')
                ->cascadeOnDelete();

            $table->foreign('petugas_pml')
                ->references('kode_petugas')
                ->on('petugas_lapangans')
                ->cascadeOnDelete();

            $table->foreign('petugas_susenas')
                ->references('kode_petugas')
                ->on('petugas_entries')
                ->cascadeOnDelete();

            $table->foreign('petugas_seruti')
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
        Schema::dropIfExists('data_dsrts');
    }
};
