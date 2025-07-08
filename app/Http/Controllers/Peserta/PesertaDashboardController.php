<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use Illuminate\Http\Request;

class PesertaDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isPeserta()) {
                abort(403, 'Akses ditolak');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = auth()->user();

        // Hitung skor prioritas terbaru
        $skorPrioritas = $user->hitungSkorPrioritas();

        // Statistik dokumen
        $totalDokumen = $user->dokumen()->count();
        $dokumenDisetujui = $user->dokumen()->where('status_verifikasi', 'disetujui')->count();
        $dokumenPending = $user->dokumen()->where('status_verifikasi', 'pending')->count();
        $dokumenDitolak = $user->dokumen()->where('status_verifikasi', 'ditolak')->count();

        // Daftar dokumen wajib
        $dokumenWajib = Dokumen::jenisDokumenWajib();
        $dokumenUser = $user->dokumen()->get()->keyBy('jenis_dokumen');

        // Progress kelengkapan profil
        $profilLengkap = $this->hitungKelengkapanProfil($user);

        // Jadwal keberangkatan (jika ada)
        $jadwalKeberangkatan = $user->jadwalKeberangkatan()->first();

        return view('peserta.dashboard', compact(
            'user',
            'skorPrioritas',
            'totalDokumen',
            'dokumenDisetujui',
            'dokumenPending',
            'dokumenDitolak',
            'dokumenWajib',
            'dokumenUser',
            'profilLengkap',
            'jadwalKeberangkatan'
        ));
    }

    private function hitungKelengkapanProfil($user)
    {
        $fields = [
            'nama',
            'email',
            'telepon',
            'tanggal_lahir',
            'alamat',
            'jenis_kelamin',
            'pendidikan_terakhir',
            'level_bahasa_jepang',
            'skor_bahasa_jepang',
            'pengalaman_kerja',
            'pekerjaan_diinginkan'
        ];

        $completed = 0;
        foreach ($fields as $field) {
            if (!empty($user->$field)) {
                $completed++;
            }
        }

        return round(($completed / count($fields)) * 100);
    }
}
