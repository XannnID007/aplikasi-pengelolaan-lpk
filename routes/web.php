<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PesertaController;
use App\Http\Controllers\Admin\DokumenController as AdminDokumenController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\PenjadwalanController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Peserta\PesertaDashboardController;
use App\Http\Controllers\Peserta\ProfilController;
use App\Http\Controllers\Peserta\DokumenController as PesertaDokumenController;
use App\Http\Controllers\Peserta\JadwalController as PesertaJadwalController;
use App\Http\Controllers\Peserta\StatusController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Kelola Peserta
        Route::get('/peserta', [PesertaController::class, 'index'])->name('peserta');
        Route::get('/peserta/{id}', [PesertaController::class, 'show'])->name('peserta.show');
        Route::put('/peserta/{id}/status', [PesertaController::class, 'updateStatus'])->name('peserta.updateStatus');
        Route::delete('/peserta/{id}', [PesertaController::class, 'destroy'])->name('peserta.destroy');

        // Verifikasi Dokumen
        Route::get('/dokumen', [AdminDokumenController::class, 'index'])->name('dokumen');
        Route::get('/dokumen/{id}', [AdminDokumenController::class, 'show'])->name('dokumen.show');
        Route::put('/dokumen/{id}/verifikasi', [AdminDokumenController::class, 'verifikasi'])->name('dokumen.verifikasi');

        // Jadwal Keberangkatan
        Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal');
        Route::get('/jadwal/create', [JadwalController::class, 'create'])->name('jadwal.create');
        Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
        Route::get('/jadwal/{id}', [JadwalController::class, 'show'])->name('jadwal.show');
        Route::get('/jadwal/{id}/edit', [JadwalController::class, 'edit'])->name('jadwal.edit');
        Route::put('/jadwal/{id}', [JadwalController::class, 'update'])->name('jadwal.update');
        Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
        Route::post('/jadwal/{id}/peserta', [JadwalController::class, 'tambahPeserta'])->name('jadwal.tambahPeserta');
        Route::delete('/jadwal/{jadwalId}/peserta/{pesertaId}', [JadwalController::class, 'hapusPeserta'])->name('jadwal.hapusPeserta');

        // Algoritma Penjadwalan
        Route::get('/penjadwalan', [PenjadwalanController::class, 'index'])->name('penjadwalan');
        Route::post('/penjadwalan/generate', [PenjadwalanController::class, 'generateSchedule'])->name('penjadwalan.generate');
        Route::get('/penjadwalan/analisis', [PenjadwalanController::class, 'analisis'])->name('penjadwalan.analisis');

        // Laporan
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/peserta', [LaporanController::class, 'laporanPeserta'])->name('laporan.peserta');
        Route::get('/laporan/keberangkatan', [LaporanController::class, 'laporanKeberangkatan'])->name('laporan.keberangkatan');
        Route::get('/laporan/export/{type}', [LaporanController::class, 'export'])->name('laporan.export');
    });
});

// Peserta Routes
Route::middleware(['auth'])->group(function () {
    Route::prefix('peserta')->name('peserta.')->middleware('peserta')->group(function () {
        // Dashboard
        Route::get('/dashboard', [PesertaDashboardController::class, 'index'])->name('dashboard');

        // Profil
        Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
        Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');
        Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');

        // Dokumen
        Route::get('/dokumen', [PesertaDokumenController::class, 'index'])->name('dokumen');
        Route::post('/dokumen/upload', [PesertaDokumenController::class, 'upload'])->name('dokumen.upload');
        Route::delete('/dokumen/{id}', [PesertaDokumenController::class, 'destroy'])->name('dokumen.destroy');
        Route::get('/dokumen/{id}/download', [PesertaDokumenController::class, 'download'])->name('dokumen.download');

        // Jadwal
        Route::get('/jadwal', [PesertaJadwalController::class, 'index'])->name('jadwal');

        // Status
        Route::get('/status', [StatusController::class, 'index'])->name('status');
    });
});

// Middleware untuk role admin
Route::middleware('admin')->group(function () {
    // Additional admin-only routes if needed
});

// Middleware untuk role peserta
Route::middleware('peserta')->group(function () {
    // Additional peserta-only routes if needed
});
