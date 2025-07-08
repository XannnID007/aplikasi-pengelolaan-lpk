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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'peserta'])->default('peserta');
            $table->string('telepon')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('level_bahasa_jepang')->nullable();
            $table->integer('skor_bahasa_jepang')->nullable();
            $table->text('pengalaman_kerja')->nullable();
            $table->string('pekerjaan_diinginkan')->nullable();
            $table->enum('status', ['pending', 'terverifikasi', 'terjadwal', 'berangkat', 'ditolak'])->default('pending');
            $table->integer('skor_prioritas')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
