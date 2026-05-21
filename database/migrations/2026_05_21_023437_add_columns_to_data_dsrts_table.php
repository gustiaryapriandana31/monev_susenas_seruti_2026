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
            $table->enum('r203_kor', array_column(\App\Enums\R203Status::cases(), 'value'))->nullable()->after('r503b');
            $table->enum('r203_kp', array_column(\App\Enums\R203Status::cases(), 'value'))->nullable()->after('r203_kor');
            $table->integer('r301_jumlah_art')->nullable()->after('r203_kp');
            $table->integer('r304_vsen26kp')->nullable()->after('r301_jumlah_art');
            $table->integer('r305_vsen26kp')->nullable()->after('r304_vsen26kp');
            $table->boolean('blok_catatan_kor')->nullable()->after('r305_vsen26kp');
            $table->boolean('blok_catatan_kp')->nullable()->after('blok_catatan_kor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_dsrts', function (Blueprint $table) {
            $table->dropColumn(['r203_kor', 'r203_kp', 'r301_jumlah_art', 'r304_vsen26kp', 'r305_vsen26kp', 'blok_catatan_kor', 'blok_catatan_kp']);
        });
    }
};
