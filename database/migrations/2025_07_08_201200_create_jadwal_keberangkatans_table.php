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
        Schema::create('jadwal_keberangkatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_batch');
            $table->date('tanggal_keberangkatan');
            $table->integer('kapasitas_maksimal');
            $table->integer('jumlah_peserta')->default(0);
            $table->string('tujuan_kota');
            $table->string('kategori_pekerjaan');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['aktif', 'penuh', 'selesai', 'dibatalkan'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_keberangkatan');
    }
};
