<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('peserta');
    }

    public function index()
    {
        $user = auth()->user();

        // Timeline status pendaftaran
        $timelineStatus = $this->getTimelineStatus($user);

        // Detail progress
        $progressDetail = $this->getProgressDetail($user);

        // Riwayat aktivitas
        $riwayatAktivitas = $this->getRiwayatAktivitas($user);

        // Next steps - langkah selanjutnya yang harus dilakukan
        $nextSteps = $this->getNextSteps($user);

        // Estimasi waktu proses
        $estimasiProses = $this->getEstimasiProses($user);

        return view('peserta.status.index', compact(
            'timelineStatus',
            'progressDetail',
            'riwayatAktivitas',
            'nextSteps',
            'estimasiProses'
        ));
    }

    private function getTimelineStatus($user)
    {
        $timeline = [
            [
                'step' => 1,
                'title' => 'Pendaftaran Akun',
                'description' => 'Membuat akun dan verifikasi email',
                'status' => 'completed',
                'date' => $user->created_at,
                'icon' => 'fas fa-user-plus',
                'color' => 'success'
            ],
            [
                'step' => 2,
                'title' => 'Melengkapi Profil',
                'description' => 'Mengisi data pribadi dan informasi pendukung',
                'status' => $this->getProfilStatus($user),
                'date' => $user->updated_at,
                'icon' => 'fas fa-user-edit',
                'color' => $this->getProfilStatus($user) == 'completed' ? 'success' : ($this->getProfilStatus($user) == 'in_progress' ? 'warning' : 'secondary')
            ],
            [
                'step' => 3,
                'title' => 'Upload Dokumen',
                'description' => 'Mengunggah semua dokumen yang diperlukan',
                'status' => $this->getDokumenStatus($user),
                'date' => $this->getLatestDokumenDate($user),
                'icon' => 'fas fa-file-upload',
                'color' => $this->getDokumenStatus($user) == 'completed' ? 'success' : ($this->getDokumenStatus($user) == 'in_progress' ? 'warning' : 'secondary')
            ],
            [
                'step' => 4,
                'title' => 'Verifikasi Admin',
                'description' => 'Menunggu verifikasi dokumen oleh admin',
                'status' => $this->getVerifikasiStatus($user),
                'date' => $this->getLatestVerifikasiDate($user),
                'icon' => 'fas fa-shield-alt',
                'color' => $this->getVerifikasiStatus($user) == 'completed' ? 'success' : ($this->getVerifikasiStatus($user) == 'in_progress' ? 'warning' : 'secondary')
            ],
            [
                'step' => 5,
                'title' => 'Penjadwalan',
                'description' => 'Penempatan dalam jadwal keberangkatan',
                'status' => $this->getPenjadwalanStatus($user),
                'date' => $this->getPenjadwalanDate($user),
                'icon' => 'fas fa-calendar-check',
                'color' => $this->getPenjadwalanStatus($user) == 'completed' ? 'success' : ($this->getPenjadwalanStatus($user) == 'in_progress' ? 'warning' : 'secondary')
            ],
            [
                'step' => 6,
                'title' => 'Keberangkatan',
                'description' => 'Persiapan dan keberangkatan ke Jepang',
                'status' => $user->status == 'berangkat' ? 'completed' : 'pending',
                'date' => $user->status == 'berangkat' ? now() : null,
                'icon' => 'fas fa-plane-departure',
                'color' => $user->status == 'berangkat' ? 'success' : 'secondary'
            ]
        ];

        return $timeline;
    }

    private function getProgressDetail($user)
    {
        $profilLengkap = $this->hitungKelengkapanProfil($user);
        $dokumenDisetujui = $user->dokumen()->where('status_verifikasi', 'disetujui')->count();
        $totalDokumenWajib = 5;

        return [
            'profil' => [
                'persentase' => $profilLengkap,
                'status' => $profilLengkap >= 90 ? 'Lengkap' : 'Perlu Dilengkapi',
                'color' => $profilLengkap >= 90 ? 'success' : 'warning'
            ],
            'dokumen' => [
                'persentase' => round(($dokumenDisetujui / $totalDokumenWajib) * 100),
                'status' => $dokumenDisetujui >= $totalDokumenWajib ? 'Lengkap' : ($dokumenDisetujui > 0 ? 'Sebagian' : 'Belum Ada'),
                'color' => $dokumenDisetujui >= $totalDokumenWajib ? 'success' : ($dokumenDisetujui > 0 ? 'warning' : 'danger'),
                'disetujui' => $dokumenDisetujui,
                'total' => $totalDokumenWajib
            ],
            'verifikasi' => [
                'status' => $user->status,
                'label' => $this->getStatusLabel($user->status),
                'color' => $this->getStatusColor($user->status),
                'skor_prioritas' => $user->skor_prioritas
            ]
        ];
    }

    private function getRiwayatAktivitas($user)
    {
        $aktivitas = collect();

        // Aktivitas pendaftaran
        $aktivitas->push([
            'tanggal' => $user->created_at,
            'aktivitas' => 'Pendaftaran akun berhasil',
            'keterangan' => 'Akun telah dibuat dan aktif',
            'icon' => 'fas fa-user-plus',
            'color' => 'success'
        ]);

        // Aktivitas update profil
        if ($user->updated_at != $user->created_at) {
            $aktivitas->push([
                'tanggal' => $user->updated_at,
                'aktivitas' => 'Profil diperbarui',
                'keterangan' => 'Data profil telah diperbarui',
                'icon' => 'fas fa-user-edit',
                'color' => 'info'
            ]);
        }

        // Aktivitas dokumen
        foreach ($user->dokumen()->orderBy('tanggal_upload', 'desc')->get() as $dokumen) {
            $jenisDokumen = Dokumen::jenisDokumenWajib()[$dokumen->jenis_dokumen] ?? $dokumen->jenis_dokumen;

            $aktivitas->push([
                'tanggal' => $dokumen->tanggal_upload,
                'aktivitas' => 'Upload dokumen',
                'keterangan' => "Dokumen {$jenisDokumen} berhasil diupload",
                'icon' => 'fas fa-file-upload',
                'color' => 'primary'
            ]);

            if ($dokumen->tanggal_verifikasi) {
                $statusVerifikasi = $dokumen->status_verifikasi == 'disetujui' ? 'disetujui' : 'ditolak';
                $aktivitas->push([
                    'tanggal' => $dokumen->tanggal_verifikasi,
                    'aktivitas' => 'Verifikasi dokumen',
                    'keterangan' => "Dokumen {$jenisDokumen} {$statusVerifikasi}" .
                        ($dokumen->catatan ? " - {$dokumen->catatan}" : ''),
                    'icon' => $dokumen->status_verifikasi == 'disetujui' ? 'fas fa-check-circle' : 'fas fa-times-circle',
                    'color' => $dokumen->status_verifikasi == 'disetujui' ? 'success' : 'danger'
                ]);
            }
        }

        // Aktivitas penjadwalan
        $jadwal = $user->jadwalKeberangkatan()->first();
        if ($jadwal) {
            $aktivitas->push([
                'tanggal' => $jadwal->pivot->tanggal_penempatan,
                'aktivitas' => 'Dijadwalkan keberangkatan',
                'keterangan' => "Dijadwalkan dalam {$jadwal->nama_batch} - {$jadwal->tujuan_kota}",
                'icon' => 'fas fa-calendar-check',
                'color' => 'success'
            ]);
        }

        return $aktivitas->sortByDesc('tanggal')->values();
    }

    private function getNextSteps($user)
    {
        $steps = [];

        // Cek kelengkapan profil
        $profilLengkap = $this->hitungKelengkapanProfil($user);
        if ($profilLengkap < 100) {
            $steps[] = [
                'title' => 'Lengkapi Profil',
                'description' => 'Pastikan semua data profil telah diisi dengan lengkap',
                'action' => 'Lengkapi Sekarang',
                'url' => route('peserta.profil.edit'),
                'priority' => 'high',
                'icon' => 'fas fa-user-edit'
            ];
        }

        // Cek dokumen
        $dokumenWajib = Dokumen::jenisDokumenWajib();
        $dokumenUser = $user->dokumen()->get()->keyBy('jenis_dokumen');

        foreach ($dokumenWajib as $jenis => $label) {
            $dokumen = $dokumenUser->get($jenis);
            if (!$dokumen) {
                $steps[] = [
                    'title' => "Upload {$label}",
                    'description' => "Dokumen {$label} belum diupload",
                    'action' => 'Upload Dokumen',
                    'url' => route('peserta.dokumen'),
                    'priority' => 'high',
                    'icon' => 'fas fa-file-upload'
                ];
            } elseif ($dokumen->status_verifikasi == 'ditolak') {
                $steps[] = [
                    'title' => "Upload Ulang {$label}",
                    'description' => "Dokumen {$label} ditolak: {$dokumen->catatan}",
                    'action' => 'Upload Ulang',
                    'url' => route('peserta.dokumen'),
                    'priority' => 'high',
                    'icon' => 'fas fa-redo'
                ];
            }
        }

        // Status-specific steps
        switch ($user->status) {
            case 'pending':
                if (empty($steps)) {
                    $steps[] = [
                        'title' => 'Menunggu Verifikasi',
                        'description' => 'Dokumen Anda sedang dalam proses verifikasi admin',
                        'action' => 'Lihat Status',
                        'url' => route('peserta.status'),
                        'priority' => 'medium',
                        'icon' => 'fas fa-clock'
                    ];
                }
                break;

            case 'terverifikasi':
                $steps[] = [
                    'title' => 'Menunggu Penjadwalan',
                    'description' => 'Anda sudah terverifikasi dan menunggu dijadwalkan keberangkatan',
                    'action' => 'Lihat Jadwal Tersedia',
                    'url' => route('peserta.jadwal'),
                    'priority' => 'medium',
                    'icon' => 'fas fa-calendar-alt'
                ];
                break;

            case 'terjadwal':
                $steps[] = [
                    'title' => 'Persiapan Keberangkatan',
                    'description' => 'Mulai persiapkan dokumen dan keperluan untuk keberangkatan',
                    'action' => 'Lihat Detail Jadwal',
                    'url' => route('peserta.jadwal'),
                    'priority' => 'high',
                    'icon' => 'fas fa-suitcase'
                ];
                break;
        }

        return collect($steps)->sortBy('priority')->values();
    }

    private function getEstimasiProses($user)
    {
        $estimasi = [];

        switch ($user->status) {
            case 'pending':
                $dokumenPending = $user->dokumen()->where('status_verifikasi', 'pending')->count();
                if ($dokumenPending > 0) {
                    $estimasi['verifikasi'] = '3-7 hari kerja';
                    $estimasi['total'] = '2-4 minggu';
                } else {
                    $estimasi['lengkapi_dokumen'] = '1-2 minggu';
                    $estimasi['total'] = '3-5 minggu';
                }
                break;

            case 'terverifikasi':
                $estimasi['penjadwalan'] = '1-2 minggu';
                $estimasi['persiapan'] = '2-4 minggu';
                $estimasi['total'] = '1-2 bulan';
                break;

            case 'terjadwal':
                $jadwal = $user->jadwalKeberangkatan()->first();
                if ($jadwal) {
                    $hariSampaiBerangkat = now()->diffInDays($jadwal->tanggal_keberangkatan);
                    $estimasi['keberangkatan'] = "{$hariSampaiBerangkat} hari lagi";
                    $estimasi['persiapan'] = 'Segera mulai persiapan';
                }
                break;

            case 'berangkat':
                $estimasi['status'] = 'Proses selesai - Selamat bekerja di Jepang!';
                break;
        }

        return $estimasi;
    }

    // Helper methods
    private function getProfilStatus($user)
    {
        $kelengkapan = $this->hitungKelengkapanProfil($user);
        if ($kelengkapan >= 90) return 'completed';
        if ($kelengkapan > 0) return 'in_progress';
        return 'pending';
    }

    private function getDokumenStatus($user)
    {
        $disetujui = $user->dokumen()->where('status_verifikasi', 'disetujui')->count();
        if ($disetujui >= 5) return 'completed';
        if ($disetujui > 0) return 'in_progress';
        return 'pending';
    }

    private function getVerifikasiStatus($user)
    {
        if (in_array($user->status, ['terverifikasi', 'terjadwal', 'berangkat'])) return 'completed';
        if ($user->dokumen()->where('status_verifikasi', 'pending')->count() > 0) return 'in_progress';
        return 'pending';
    }

    private function getPenjadwalanStatus($user)
    {
        if (in_array($user->status, ['terjadwal', 'berangkat'])) return 'completed';
        if ($user->status == 'terverifikasi') return 'in_progress';
        return 'pending';
    }

    private function getLatestDokumenDate($user)
    {
        return $user->dokumen()->latest('tanggal_upload')->first()?->tanggal_upload;
    }

    private function getLatestVerifikasiDate($user)
    {
        return $user->dokumen()->whereNotNull('tanggal_verifikasi')
            ->latest('tanggal_verifikasi')->first()?->tanggal_verifikasi;
    }

    private function getPenjadwalanDate($user)
    {
        return $user->jadwalKeberangkatan()->first()?->pivot?->tanggal_penempatan;
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

    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Menunggu Verifikasi',
            'terverifikasi' => 'Terverifikasi',
            'terjadwal' => 'Terjadwal',
            'berangkat' => 'Sudah Berangkat',
            'ditolak' => 'Ditolak'
        ];

        return $labels[$status] ?? $status;
    }

    private function getStatusColor($status)
    {
        $colors = [
            'pending' => 'warning',
            'terverifikasi' => 'success',
            'terjadwal' => 'info',
            'berangkat' => 'primary',
            'ditolak' => 'danger'
        ];

        return $colors[$status] ?? 'secondary';
    }
}
