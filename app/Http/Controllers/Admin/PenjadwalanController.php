<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GreedySchedulerService;
use App\Models\User;
use App\Models\JadwalKeberangkatan;
use Illuminate\Http\Request;

class PenjadwalanController extends Controller
{
    protected $schedulerService;

    public function __construct(GreedySchedulerService $schedulerService)
    {
        $this->middleware('auth');
        $this->middleware('admin');
        $this->schedulerService = $schedulerService;
    }

    public function index()
    {
        // Data untuk simulasi algoritma
        $pesertaTersedia = User::where('role', 'peserta')
            ->where('status', 'terverifikasi')
            ->whereDoesntHave('jadwalKeberangkatan')
            ->orderBy('skor_prioritas', 'desc')
            ->get();

        $jadwalTersedia = JadwalKeberangkatan::where('status', 'aktif')
            ->where('tanggal_keberangkatan', '>=', now())
            ->orderBy('tanggal_keberangkatan')
            ->get();

        // Analisis efektivitas algoritma
        $analisisEfektivitas = $this->schedulerService->analisisEfektivitas();

        // Simulasi penjadwalan untuk preview
        $simulasiPenjadwalan = $this->simulasiPenjadwalan($pesertaTersedia, $jadwalTersedia);

        return view('admin.penjadwalan.index', compact(
            'pesertaTersedia',
            'jadwalTersedia',
            'analisisEfektivitas',
            'simulasiPenjadwalan'
        ));
    }

    public function generateSchedule(Request $request)
    {
        $request->validate([
            'konfirmasi' => 'required|accepted',
        ], [
            'konfirmasi.required' => 'Anda harus mengonfirmasi untuk menjalankan algoritma penjadwalan.',
            'konfirmasi.accepted' => 'Anda harus menyetujui untuk melanjutkan proses penjadwalan.'
        ]);

        try {
            // Jalankan algoritma greedy
            $hasilPenjadwalan = $this->schedulerService->generateOptimalSchedule();

            $jumlahTerjadwal = count($hasilPenjadwalan);

            if ($jumlahTerjadwal > 0) {
                return redirect()->route('admin.penjadwalan')
                    ->with('success', "Algoritma berhasil dijalankan! {$jumlahTerjadwal} peserta telah dijadwalkan secara optimal.");
            } else {
                return redirect()->route('admin.penjadwalan')
                    ->with('warning', 'Tidak ada peserta yang dapat dijadwalkan saat ini. Pastikan ada peserta terverifikasi dan jadwal yang tersedia.');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.penjadwalan')
                ->with('error', 'Terjadi kesalahan saat menjalankan algoritma: ' . $e->getMessage());
        }
    }

    public function analisis()
    {
        $analisisEfektivitas = $this->schedulerService->analisisEfektivitas();

        // Data tambahan untuk analisis
        $distribusiSkor = User::where('role', 'peserta')
            ->where('status', 'terverifikasi')
            ->selectRaw('
                CASE 
                    WHEN skor_prioritas >= 80 THEN "Tinggi (80-100)"
                    WHEN skor_prioritas >= 60 THEN "Sedang (60-79)"
                    WHEN skor_prioritas >= 40 THEN "Rendah (40-59)"
                    ELSE "Sangat Rendah (<40)"
                END as kategori_skor,
                COUNT(*) as jumlah
            ')
            ->groupBy('kategori_skor')
            ->get();

        // Distribusi berdasarkan level bahasa Jepang
        $distribusiBahasa = User::where('role', 'peserta')
            ->where('status', 'terverifikasi')
            ->selectRaw('level_bahasa_jepang, COUNT(*) as jumlah')
            ->whereNotNull('level_bahasa_jepang')
            ->groupBy('level_bahasa_jepang')
            ->orderBy('level_bahasa_jepang')
            ->get();

        // Tingkat pemanfaatan jadwal
        $pemanfaatanJadwal = JadwalKeberangkatan::selectRaw('
                nama_batch,
                kapasitas_maksimal,
                jumlah_peserta,
                ROUND((jumlah_peserta / kapasitas_maksimal) * 100, 2) as persentase_terisi,
                status
            ')
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('tanggal_keberangkatan')
            ->get();

        return view('admin.penjadwalan.analisis', compact(
            'analisisEfektivitas',
            'distribusiSkor',
            'distribusiBahasa',
            'pemanfaatanJadwal'
        ));
    }

    private function simulasiPenjadwalan($pesertaTersedia, $jadwalTersedia)
    {
        $simulasi = [];

        foreach ($jadwalTersedia as $jadwal) {
            $pesertaCocok = $pesertaTersedia->filter(function ($peserta) use ($jadwal) {
                return $this->cekKesesuaianKategori($peserta, $jadwal);
            });

            if ($pesertaCocok->isEmpty()) {
                $pesertaCocok = $pesertaTersedia;
            }

            $pesertaTerpilih = $pesertaCocok->take($jadwal->sisaKapasitas());

            $simulasi[] = [
                'jadwal' => $jadwal,
                'peserta_terpilih' => $pesertaTerpilih,
                'sisa_kapasitas' => $jadwal->sisaKapasitas(),
                'dapat_diisi' => $pesertaTerpilih->count()
            ];

            // Hapus peserta yang sudah dipilih dari daftar tersedia untuk simulasi selanjutnya
            $pesertaTersedia = $pesertaTersedia->diff($pesertaTerpilih);
        }

        return $simulasi;
    }

    private function cekKesesuaianKategori($peserta, $jadwal)
    {
        if (empty($peserta->pekerjaan_diinginkan)) {
            return true;
        }

        return stripos($peserta->pekerjaan_diinginkan, $jadwal->kategori_pekerjaan) !== false ||
            stripos($jadwal->kategori_pekerjaan, $peserta->pekerjaan_diinginkan) !== false;
    }

    public function resetSchedule(Request $request)
    {
        $request->validate([
            'konfirmasi_reset' => 'required|accepted',
        ], [
            'konfirmasi_reset.required' => 'Anda harus mengonfirmasi untuk mereset penjadwalan.',
            'konfirmasi_reset.accepted' => 'Anda harus menyetujui untuk melanjutkan proses reset.'
        ]);

        try {
            // Reset semua peserta yang terjadwal kembali ke terverifikasi
            $pesertaTerjadwal = User::where('status', 'terjadwal')->get();

            foreach ($pesertaTerjadwal as $peserta) {
                // Hapus dari semua jadwal
                $peserta->jadwalKeberangkatan()->detach();
                // Update status kembali ke terverifikasi
                $peserta->update(['status' => 'terverifikasi']);
            }

            // Reset jumlah peserta di semua jadwal aktif
            JadwalKeberangkatan::where('status', '!=', 'selesai')
                ->update([
                    'jumlah_peserta' => 0,
                    'status' => 'aktif'
                ]);

            return redirect()->route('admin.penjadwalan')
                ->with('success', 'Penjadwalan berhasil direset. Semua peserta dikembalikan ke status terverifikasi.');
        } catch (\Exception $e) {
            return redirect()->route('admin.penjadwalan')
                ->with('error', 'Terjadi kesalahan saat mereset penjadwalan: ' . $e->getMessage());
        }
    }

    public function optimasiParameter(Request $request)
    {
        $request->validate([
            'bobot_dokumen' => 'required|numeric|min:0|max:100',
            'bobot_bahasa' => 'required|numeric|min:0|max:100',
            'bobot_pengalaman' => 'required|numeric|min:0|max:100',
            'bobot_waktu' => 'required|numeric|min:0|max:100',
        ]);

        // Validasi total bobot harus 100%
        $totalBobot = $request->bobot_dokumen + $request->bobot_bahasa +
            $request->bobot_pengalaman + $request->bobot_waktu;

        if ($totalBobot != 100) {
            return redirect()->back()->withErrors([
                'bobot_total' => 'Total bobot harus sama dengan 100%'
            ]);
        }

        // Simpan parameter ke database atau config
        // Untuk sementara kita simpan di session
        session([
            'algoritma_bobot' => [
                'dokumen' => $request->bobot_dokumen,
                'bahasa' => $request->bobot_bahasa,
                'pengalaman' => $request->bobot_pengalaman,
                'waktu' => $request->bobot_waktu,
            ]
        ]);

        return redirect()->route('admin.penjadwalan')
            ->with('success', 'Parameter algoritma berhasil diperbarui.');
    }
}
