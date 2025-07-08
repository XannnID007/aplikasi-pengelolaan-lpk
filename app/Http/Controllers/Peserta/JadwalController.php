<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\JadwalKeberangkatan;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('peserta');
    }

    public function index()
    {
        $user = auth()->user();

        // Jadwal keberangkatan peserta (jika sudah terjadwal)
        $jadwalPeserta = $user->jadwalKeberangkatan()->first();

        // Jadwal keberangkatan yang tersedia (untuk informasi)
        $jadwalTersedia = JadwalKeberangkatan::where('status', 'aktif')
            ->where('tanggal_keberangkatan', '>=', now())
            ->orderBy('tanggal_keberangkatan')
            ->get();

        // Statistik jadwal
        $statistikJadwal = [
            'total_jadwal_tersedia' => $jadwalTersedia->count(),
            'total_kapasitas' => $jadwalTersedia->sum('kapasitas_maksimal'),
            'total_terisi' => $jadwalTersedia->sum('jumlah_peserta'),
            'rata_rata_terisi' => $jadwalTersedia->count() > 0 ?
                round($jadwalTersedia->avg(function ($jadwal) {
                    return ($jadwal->jumlah_peserta / $jadwal->kapasitas_maksimal) * 100;
                }), 1) : 0,
        ];

        // Rekomendasi jadwal berdasarkan profil peserta
        $rekomendasiJadwal = $this->getRekomendasiJadwal($user, $jadwalTersedia);

        return view('peserta.jadwal.index', compact(
            'jadwalPeserta',
            'jadwalTersedia',
            'statistikJadwal',
            'rekomendasiJadwal'
        ));
    }

    private function getRekomendasiJadwal($user, $jadwalTersedia)
    {
        if ($jadwalTersedia->isEmpty()) {
            return collect();
        }

        // Hitung score kesesuaian untuk setiap jadwal
        $jadwalDenganScore = $jadwalTersedia->map(function ($jadwal) use ($user) {
            $score = 0;

            // Score berdasarkan kesesuaian pekerjaan (40%)
            if (!empty($user->pekerjaan_diinginkan)) {
                if (
                    stripos($user->pekerjaan_diinginkan, $jadwal->kategori_pekerjaan) !== false ||
                    stripos($jadwal->kategori_pekerjaan, $user->pekerjaan_diinginkan) !== false
                ) {
                    $score += 40;
                }
            } else {
                $score += 20; // Score default jika tidak ada preferensi
            }

            // Score berdasarkan kapasitas tersedia (30%)
            $persentaseKapasitas = ($jadwal->jumlah_peserta / $jadwal->kapasitas_maksimal) * 100;
            if ($persentaseKapasitas < 50) {
                $score += 30; // Masih banyak tempat
            } elseif ($persentaseKapasitas < 80) {
                $score += 20; // Tempat cukup tersedia
            } else {
                $score += 10; // Tempat terbatas
            }

            // Score berdasarkan waktu keberangkatan (20%)
            $hariSampaiKeberangkatan = now()->diffInDays($jadwal->tanggal_keberangkatan);
            if ($hariSampaiKeberangkatan > 90) {
                $score += 20; // Masih lama, banyak waktu persiapan
            } elseif ($hariSampaiKeberangkatan > 30) {
                $score += 15; // Waktu cukup
            } else {
                $score += 5; // Waktu terbatas
            }

            // Score berdasarkan skor prioritas peserta (10%)
            if ($user->skor_prioritas >= 80) {
                $score += 10; // Prioritas tinggi
            } elseif ($user->skor_prioritas >= 60) {
                $score += 7;
            } else {
                $score += 3;
            }

            $jadwal->rekomendasi_score = $score;
            $jadwal->alasan_rekomendasi = $this->getAlasanRekomendasi($user, $jadwal, $score);

            return $jadwal;
        });

        // Urutkan berdasarkan score dan ambil top 3
        return $jadwalDenganScore->sortByDesc('rekomendasi_score')->take(3);
    }

    private function getAlasanRekomendasi($user, $jadwal, $score)
    {
        $alasan = [];

        // Alasan berdasarkan kesesuaian pekerjaan
        if (!empty($user->pekerjaan_diinginkan)) {
            if (
                stripos($user->pekerjaan_diinginkan, $jadwal->kategori_pekerjaan) !== false ||
                stripos($jadwal->kategori_pekerjaan, $user->pekerjaan_diinginkan) !== false
            ) {
                $alasan[] = "Sesuai dengan pekerjaan yang diinginkan ({$user->pekerjaan_diinginkan})";
            }
        }

        // Alasan berdasarkan kapasitas
        $sisaKapasitas = $jadwal->sisaKapasitas();
        if ($sisaKapasitas > 10) {
            $alasan[] = "Masih banyak tempat tersedia ({$sisaKapasitas} tempat)";
        } elseif ($sisaKapasitas > 0) {
            $alasan[] = "Tempat terbatas ({$sisaKapasitas} tempat tersisa)";
        }

        // Alasan berdasarkan waktu
        $hariSampaiKeberangkatan = now()->diffInDays($jadwal->tanggal_keberangkatan);
        if ($hariSampaiKeberangkatan > 90) {
            $alasan[] = "Waktu persiapan cukup lama (" . round($hariSampaiKeberangkatan / 30) . " bulan)";
        }

        // Alasan berdasarkan score peserta
        if ($user->skor_prioritas >= 80) {
            $alasan[] = "Skor prioritas Anda tinggi ({$user->skor_prioritas})";
        }

        return implode(', ', $alasan);
    }

    public function detailJadwal($id)
    {
        $user = auth()->user();
        $jadwal = JadwalKeberangkatan::findOrFail($id);

        // Cek apakah user sudah terjadwal di jadwal ini
        $sudahTerjadwal = $user->jadwalKeberangkatan()->where('jadwal_keberangkatan.id', $id)->exists();

        // Hitung peluang diterima berdasarkan skor prioritas
        $peluangDiterima = $this->hitungPeluangDiterima($user, $jadwal);

        // Persyaratan untuk jadwal ini
        $persyaratan = $this->getPersyaratanJadwal($jadwal);

        return view('peserta.jadwal.detail', compact(
            'jadwal',
            'sudahTerjadwal',
            'peluangDiterima',
            'persyaratan'
        ));
    }

    private function hitungPeluangDiterima($user, $jadwal)
    {
        $peluang = 0;

        // Faktor skor prioritas (50%)
        if ($user->skor_prioritas >= 80) {
            $peluang += 50;
        } elseif ($user->skor_prioritas >= 60) {
            $peluang += 35;
        } elseif ($user->skor_prioritas >= 40) {
            $peluang += 20;
        } else {
            $peluang += 10;
        }

        // Faktor kesesuaian pekerjaan (30%)
        if (!empty($user->pekerjaan_diinginkan)) {
            if (
                stripos($user->pekerjaan_diinginkan, $jadwal->kategori_pekerjaan) !== false ||
                stripos($jadwal->kategori_pekerjaan, $user->pekerjaan_diinginkan) !== false
            ) {
                $peluang += 30;
            } else {
                $peluang += 10;
            }
        } else {
            $peluang += 15;
        }

        // Faktor kapasitas tersedia (20%)
        $persentaseKapasitas = ($jadwal->jumlah_peserta / $jadwal->kapasitas_maksimal) * 100;
        if ($persentaseKapasitas < 50) {
            $peluang += 20;
        } elseif ($persentaseKapasitas < 80) {
            $peluang += 15;
        } else {
            $peluang += 5;
        }

        return min(100, $peluang); // Maksimal 100%
    }

    private function getPersyaratanJadwal($jadwal)
    {
        $persyaratan = [
            'Umum' => [
                'Status pendaftaran terverifikasi',
                'Semua dokumen wajib sudah disetujui',
                'Skor prioritas minimal 40',
                'Sehat jasmani dan rohani',
            ]
        ];

        // Persyaratan khusus berdasarkan kategori pekerjaan
        switch ($jadwal->kategori_pekerjaan) {
            case 'Teknologi Informasi':
                $persyaratan['Khusus'] = [
                    'Pendidikan minimal D3 Teknik Informatika atau sederajat',
                    'Memiliki pengalaman programming minimal 1 tahun',
                    'Skor bahasa Jepang minimal N4',
                ];
                break;

            case 'Perhotelan':
                $persyaratan['Khusus'] = [
                    'Pendidikan minimal SMA/SMK Perhotelan',
                    'Pengalaman di bidang hospitality (diutamakan)',
                    'Kemampuan komunikasi yang baik',
                ];
                break;

            case 'Manufaktur':
                $persyaratan['Khusus'] = [
                    'Pendidikan minimal SMK Teknik',
                    'Pengalaman kerja di pabrik/manufaktur',
                    'Kondisi fisik yang prima',
                ];
                break;

            default:
                $persyaratan['Khusus'] = [
                    'Sesuai dengan latar belakang pendidikan',
                    'Memiliki motivasi tinggi untuk bekerja di Jepang',
                ];
        }

        return $persyaratan;
    }
}
