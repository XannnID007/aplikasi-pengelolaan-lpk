<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JadwalKeberangkatan;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        // Ringkasan laporan
        $ringkasanLaporan = [
            'total_peserta' => User::where('role', 'peserta')->count(),
            'peserta_aktif_bulan_ini' => User::where('role', 'peserta')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total_keberangkatan' => User::where('status', 'berangkat')->count(),
            'keberangkatan_bulan_ini' => $this->getKeberangkatanBulanIni(),
            'tingkat_verifikasi' => $this->getTingkatVerifikasi(),
            'efektivitas_penjadwalan' => $this->getEfektivitasPenjadwalan(),
        ];

        // Chart data untuk dashboard laporan
        $chartData = [
            'pendaftaran_bulanan' => $this->getPendaftaranBulanan(),
            'distribusi_status' => $this->getDistribusiStatus(),
            'keberangkatan_bulanan' => $this->getKeberangkatanBulanan(),
        ];

        return view('admin.laporan.index', compact('ringkasanLaporan', 'chartData'));
    }

    public function laporanPeserta(Request $request)
    {
        $query = User::where('role', 'peserta');

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan level bahasa Jepang
        if ($request->filled('level_bahasa')) {
            $query->where('level_bahasa_jepang', $request->level_bahasa);
        }

        $peserta = $query->orderBy('created_at', 'desc')->get();

        // Statistik peserta
        $statistikPeserta = [
            'total' => $peserta->count(),
            'rata_rata_skor' => $peserta->avg('skor_prioritas'),
            'skor_tertinggi' => $peserta->max('skor_prioritas'),
            'skor_terendah' => $peserta->min('skor_prioritas'),
            'distribusi_pendidikan' => $peserta->groupBy('pendidikan_terakhir')
                ->map(function ($group) {
                    return $group->count();
                }),
            'distribusi_jenis_kelamin' => $peserta->groupBy('jenis_kelamin')
                ->map(function ($group) {
                    return $group->count();
                }),
        ];

        return view('admin.laporan.peserta', compact('peserta', 'statistikPeserta'));
    }

    public function laporanKeberangkatan(Request $request)
    {
        $query = JadwalKeberangkatan::with(['peserta']);

        // Filter berdasarkan tanggal keberangkatan
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_keberangkatan', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_keberangkatan', '<=', $request->tanggal_sampai);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan kota tujuan
        if ($request->filled('tujuan_kota')) {
            $query->where('tujuan_kota', $request->tujuan_kota);
        }

        $jadwalKeberangkatan = $query->orderBy('tanggal_keberangkatan', 'desc')->get();

        // Statistik keberangkatan
        $statistikKeberangkatan = [
            'total_jadwal' => $jadwalKeberangkatan->count(),
            'total_peserta_berangkat' => $jadwalKeberangkatan->sum('jumlah_peserta'),
            'rata_rata_kapasitas_terisi' => $jadwalKeberangkatan->avg(function ($jadwal) {
                return ($jadwal->jumlah_peserta / $jadwal->kapasitas_maksimal) * 100;
            }),
            'distribusi_kota' => $jadwalKeberangkatan->groupBy('tujuan_kota')
                ->map(function ($group) {
                    return $group->count();
                }),
            'distribusi_kategori' => $jadwalKeberangkatan->groupBy('kategori_pekerjaan')
                ->map(function ($group) {
                    return $group->count();
                }),
        ];

        return view('admin.laporan.keberangkatan', compact('jadwalKeberangkatan', 'statistikKeberangkatan'));
    }

    public function export($type, Request $request)
    {
        switch ($type) {
            case 'peserta-excel':
                return $this->exportPesertaExcel($request);
            case 'peserta-pdf':
                return $this->exportPesertaPdf($request);
            case 'keberangkatan-excel':
                return $this->exportKeberangkatanExcel($request);
            case 'keberangkatan-pdf':
                return $this->exportKeberangkatanPdf($request);
            default:
                return redirect()->back()->with('error', 'Tipe export tidak valid.');
        }
    }

    private function exportPesertaExcel($request)
    {
        // Implementasi export Excel untuk data peserta
        // Menggunakan library seperti PhpSpreadsheet atau Laravel Excel

        $query = User::where('role', 'peserta');

        // Apply filters sama seperti di laporanPeserta
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        $peserta = $query->get();

        // Untuk demo, kita return CSV sederhana
        $filename = 'laporan_peserta_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($peserta) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, [
                'Nama',
                'Email',
                'Telepon',
                'Tanggal Lahir',
                'Jenis Kelamin',
                'Pendidikan Terakhir',
                'Level Bahasa Jepang',
                'Skor Bahasa Jepang',
                'Pekerjaan Diinginkan',
                'Status',
                'Skor Prioritas',
                'Tanggal Daftar'
            ]);

            // Data peserta
            foreach ($peserta as $p) {
                fputcsv($file, [
                    $p->nama,
                    $p->email,
                    $p->telepon,
                    $p->tanggal_lahir ? $p->tanggal_lahir->format('d/m/Y') : '',
                    $p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                    $p->pendidikan_terakhir,
                    $p->level_bahasa_jepang,
                    $p->skor_bahasa_jepang,
                    $p->pekerjaan_diinginkan,
                    $p->status,
                    $p->skor_prioritas,
                    $p->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPesertaPdf($request)
    {
        // Implementasi export PDF menggunakan library seperti DomPDF
        // Untuk demo, kita redirect dengan pesan
        return redirect()->back()->with('info', 'Fitur export PDF akan segera tersedia.');
    }

    private function exportKeberangkatanExcel($request)
    {
        $query = JadwalKeberangkatan::with(['peserta']);

        // Apply filters
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_keberangkatan', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_keberangkatan', '<=', $request->tanggal_sampai);
        }

        $jadwal = $query->get();

        $filename = 'laporan_keberangkatan_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($jadwal) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, [
                'Nama Batch',
                'Tanggal Keberangkatan',
                'Tujuan Kota',
                'Kategori Pekerjaan',
                'Kapasitas Maksimal',
                'Jumlah Peserta',
                'Persentase Terisi',
                'Status'
            ]);

            // Data jadwal
            foreach ($jadwal as $j) {
                $persentase = $j->kapasitas_maksimal > 0 ?
                    round(($j->jumlah_peserta / $j->kapasitas_maksimal) * 100, 2) : 0;

                fputcsv($file, [
                    $j->nama_batch,
                    $j->tanggal_keberangkatan->format('d/m/Y'),
                    $j->tujuan_kota,
                    $j->kategori_pekerjaan,
                    $j->kapasitas_maksimal,
                    $j->jumlah_peserta,
                    $persentase . '%',
                    ucfirst($j->status)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportKeberangkatanPdf($request)
    {
        return redirect()->back()->with('info', 'Fitur export PDF akan segera tersedia.');
    }

    // Helper methods untuk data statistik
    private function getKeberangkatanBulanIni()
    {
        return JadwalKeberangkatan::whereMonth('tanggal_keberangkatan', now()->month)
            ->whereYear('tanggal_keberangkatan', now()->year)
            ->where('status', 'selesai')
            ->sum('jumlah_peserta');
    }

    private function getTingkatVerifikasi()
    {
        $totalPeserta = User::where('role', 'peserta')->count();
        $terverifikasi = User::where('role', 'peserta')->where('status', 'terverifikasi')->count();

        return $totalPeserta > 0 ? round(($terverifikasi / $totalPeserta) * 100, 2) : 0;
    }

    private function getEfektivitasPenjadwalan()
    {
        $terverifikasi = User::where('role', 'peserta')->where('status', 'terverifikasi')->count();
        $terjadwal = User::where('role', 'peserta')->where('status', 'terjadwal')->count();

        return $terverifikasi > 0 ? round(($terjadwal / $terverifikasi) * 100, 2) : 0;
    }

    private function getPendaftaranBulanan()
    {
        return User::where('role', 'peserta')
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as jumlah')
            ->whereYear('created_at', date('Y'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
    }

    private function getDistribusiStatus()
    {
        return User::where('role', 'peserta')
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->get();
    }

    private function getKeberangkatanBulanan()
    {
        return JadwalKeberangkatan::selectRaw('MONTH(tanggal_keberangkatan) as bulan, SUM(jumlah_peserta) as jumlah')
            ->whereYear('tanggal_keberangkatan', date('Y'))
            ->where('status', 'selesai')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
    }
}
