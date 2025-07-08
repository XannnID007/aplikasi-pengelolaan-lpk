<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JadwalKeberangkatan;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Akses ditolak');
            }
            return $next($request);
        });
    }

    public function index()
    {
        // Statistik utama
        $totalPeserta = User::where('role', 'peserta')->count();
        $pesertaTerverifikasi = User::where('role', 'peserta')
            ->where('status', 'terverifikasi')->count();
        $pesertaTerjadwal = User::where('role', 'peserta')
            ->where('status', 'terjadwal')->count();
        $pesertaBerangkat = User::where('role', 'peserta')
            ->where('status', 'berangkat')->count();

        // Jadwal aktif
        $jadwalAktif = JadwalKeberangkatan::where('status', 'aktif')
            ->orWhere('status', 'penuh')
            ->count();

        // Dokumen pending verifikasi
        $dokumenPending = Dokumen::where('status_verifikasi', 'pending')->count();

        // Grafik pendaftaran bulanan
        $pendaftaranBulanan = User::where('role', 'peserta')
            ->select(DB::raw('MONTH(created_at) as bulan'), DB::raw('COUNT(*) as jumlah'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Peserta terbaru
        $pesertaTerbaru = User::where('role', 'peserta')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Jadwal terdekat
        $jadwalTerdekat = JadwalKeberangkatan::where('tanggal_keberangkatan', '>=', now())
            ->orderBy('tanggal_keberangkatan')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPeserta',
            'pesertaTerverifikasi',
            'pesertaTerjadwal',
            'pesertaBerangkat',
            'jadwalAktif',
            'dokumenPending',
            'pendaftaranBulanan',
            'pesertaTerbaru',
            'jadwalTerdekat'
        ));
    }
}
