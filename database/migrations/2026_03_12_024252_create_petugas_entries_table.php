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
        Schema::create('petugas_entries', function (Blueprint $table) {
            $table->string('kode_petugas')->primary();
            $table->integer('provinsi');
            $table->integer('kabupaten');
            $table->string('nama_petugas');
            $table->string('email');
            $table->string('no_hp');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petugas_entries');
    }
};
