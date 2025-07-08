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
        Schema::create('peserta_jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('jadwal_id')->constrained('jadwal_keberangkatan')->onDelete('cascade');
            $table->integer('skor_akhir');
            $table->date('tanggal_penempatan');
            $table->enum('status_keberangkatan', ['terjadwal', 'siap_berangkat', 'berangkat', 'batal'])->default('terjadwal');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_jadwal');
    }
};
