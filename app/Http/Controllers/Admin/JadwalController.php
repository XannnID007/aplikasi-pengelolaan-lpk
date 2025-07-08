<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalKeberangkatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = JadwalKeberangkatan::withCount('peserta');

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan bulan/tahun
        if ($request->has('bulan') && $request->bulan != '') {
            $query->whereMonth('tanggal_keberangkatan', $request->bulan);
        }

        if ($request->has('tahun') && $request->tahun != '') {
            $query->whereYear('tanggal_keberangkatan', $request->tahun);
        }

        // Filter berdasarkan kategori pekerjaan
        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('kategori_pekerjaan', 'like', '%' . $request->kategori . '%');
        }

        $jadwal = $query->orderBy('tanggal_keberangkatan', 'asc')->paginate(15);

        // Statistik
        $statistik = [
            'total' => JadwalKeberangkatan::count(),
            'aktif' => JadwalKeberangkatan::where('status', 'aktif')->count(),
            'penuh' => JadwalKeberangkatan::where('status', 'penuh')->count(),
            'selesai' => JadwalKeberangkatan::where('status', 'selesai')->count(),
            'total_kapasitas' => JadwalKeberangkatan::sum('kapasitas_maksimal'),
            'total_terjadwal' => JadwalKeberangkatan::sum('jumlah_peserta'),
        ];

        return view('admin.jadwal.index', compact('jadwal', 'statistik'));
    }

    public function create()
    {
        // Daftar kategori pekerjaan yang tersedia
        $kategoriPekerjaan = [
            'Teknologi Informasi',
            'Perhotelan',
            'Manufaktur',
            'Administrasi',
            'Otomotif',
            'Konstruksi',
            'Pertanian',
            'Perikanan',
            'Kesehatan',
            'Pendidikan'
        ];

        // Daftar kota tujuan populer
        $kotaTujuan = [
            'Tokyo',
            'Osaka',
            'Nagoya',
            'Yokohama',
            'Kyoto',
            'Kobe',
            'Sendai',
            'Hiroshima',
            'Fukuoka',
            'Sapporo'
        ];

        return view('admin.jadwal.create', compact('kategoriPekerjaan', 'kotaTujuan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_batch' => 'required|string|max:255|unique:jadwal_keberangkatan',
            'tanggal_keberangkatan' => 'required|date|after:today',
            'kapasitas_maksimal' => 'required|integer|min:1|max:100',
            'tujuan_kota' => 'required|string|max:255',
            'kategori_pekerjaan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        JadwalKeberangkatan::create($request->all());

        return redirect()->route('admin.jadwal')->with('success', 'Jadwal keberangkatan berhasil dibuat.');
    }

    public function show($id)
    {
        $jadwal = JadwalKeberangkatan::with(['peserta' => function ($query) {
            $query->orderBy('pivot_skor_akhir', 'desc');
        }])->findOrFail($id);

        // Peserta yang tersedia untuk ditambahkan (terverifikasi tapi belum terjadwal)
        $pesertaTersedia = User::where('role', 'peserta')
            ->where('status', 'terverifikasi')
            ->whereDoesntHave('jadwalKeberangkatan')
            ->orderBy('skor_prioritas', 'desc')
            ->get();

        return view('admin.jadwal.show', compact('jadwal', 'pesertaTersedia'));
    }

    public function edit($id)
    {
        $jadwal = JadwalKeberangkatan::findOrFail($id);

        $kategoriPekerjaan = [
            'Teknologi Informasi',
            'Perhotelan',
            'Manufaktur',
            'Administrasi',
            'Otomotif',
            'Konstruksi',
            'Pertanian',
            'Perikanan',
            'Kesehatan',
            'Pendidikan'
        ];

        $kotaTujuan = [
            'Tokyo',
            'Osaka',
            'Nagoya',
            'Yokohama',
            'Kyoto',
            'Kobe',
            'Sendai',
            'Hiroshima',
            'Fukuoka',
            'Sapporo'
        ];

        return view('admin.jadwal.edit', compact('jadwal', 'kategoriPekerjaan', 'kotaTujuan'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalKeberangkatan::findOrFail($id);

        $request->validate([
            'nama_batch' => 'required|string|max:255|unique:jadwal_keberangkatan,nama_batch,' . $jadwal->id,
            'tanggal_keberangkatan' => 'required|date',
            'kapasitas_maksimal' => 'required|integer|min:1|max:100',
            'tujuan_kota' => 'required|string|max:255',
            'kategori_pekerjaan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'status' => 'required|in:aktif,penuh,selesai,dibatalkan'
        ]);

        // Validasi kapasitas tidak boleh kurang dari jumlah peserta yang sudah terjadwal
        if ($request->kapasitas_maksimal < $jadwal->jumlah_peserta) {
            return redirect()->back()->withErrors([
                'kapasitas_maksimal' => 'Kapasitas tidak boleh kurang dari jumlah peserta yang sudah terjadwal (' . $jadwal->jumlah_peserta . ')'
            ]);
        }

        $jadwal->update($request->all());

        return redirect()->route('admin.jadwal.show', $jadwal->id)
            ->with('success', 'Jadwal keberangkatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jadwal = JadwalKeberangkatan::findOrFail($id);

        // Cek apakah ada peserta yang terjadwal
        if ($jadwal->jumlah_peserta > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus jadwal yang sudah memiliki peserta terjadwal.');
        }

        $jadwal->delete();

        return redirect()->route('admin.jadwal')->with('success', 'Jadwal keberangkatan berhasil dihapus.');
    }

    public function tambahPeserta(Request $request, $id)
    {
        $request->validate([
            'peserta_ids' => 'required|array',
            'peserta_ids.*' => 'exists:users,id',
        ]);

        $jadwal = JadwalKeberangkatan::findOrFail($id);

        // Cek kapasitas
        $jumlahPesertaBaru = count($request->peserta_ids);
        if (($jadwal->jumlah_peserta + $jumlahPesertaBaru) > $jadwal->kapasitas_maksimal) {
            return redirect()->back()->with('error', 'Kapasitas jadwal tidak mencukupi.');
        }

        DB::beginTransaction();
        try {
            foreach ($request->peserta_ids as $pesertaId) {
                $peserta = User::findOrFail($pesertaId);

                // Hitung skor akhir
                $skorAkhir = $this->hitungSkorAkhir($peserta, $jadwal);

                // Tambah ke jadwal
                $jadwal->tambahPeserta($pesertaId, $skorAkhir, 'Ditambahkan manual oleh admin');

                // Update status peserta
                $peserta->update(['status' => 'terjadwal']);
            }

            DB::commit();

            return redirect()->back()->with('success', "{$jumlahPesertaBaru} peserta berhasil ditambahkan ke jadwal.");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan peserta.');
        }
    }

    public function hapusPeserta($jadwalId, $pesertaId)
    {
        $jadwal = JadwalKeberangkatan::findOrFail($jadwalId);
        $peserta = User::findOrFail($pesertaId);

        // Hapus dari jadwal
        $jadwal->hapusPeserta($pesertaId);

        // Update status peserta kembali ke terverifikasi
        $peserta->update(['status' => 'terverifikasi']);

        return redirect()->back()->with('success', 'Peserta berhasil dihapus dari jadwal.');
    }

    public function selesaikanJadwal($id)
    {
        $jadwal = JadwalKeberangkatan::findOrFail($id);

        // Update status jadwal
        $jadwal->update(['status' => 'selesai']);

        // Update status semua peserta menjadi 'berangkat'
        foreach ($jadwal->peserta as $peserta) {
            $peserta->update(['status' => 'berangkat']);
        }

        return redirect()->back()->with('success', 'Jadwal keberangkatan berhasil diselesaikan. Status peserta diperbarui menjadi "Berangkat".');
    }

    private function hitungSkorAkhir(User $peserta, JadwalKeberangkatan $jadwal)
    {
        $skorDasar = $peserta->skor_prioritas;

        // Bonus jika kategori pekerjaan cocok
        if ($this->cekKesesuaianKategori($peserta, $jadwal)) {
            $skorDasar += 10;
        }

        // Bonus berdasarkan skor bahasa Jepang
        if ($peserta->skor_bahasa_jepang >= 80) {
            $skorDasar += 5;
        } elseif ($peserta->skor_bahasa_jepang >= 60) {
            $skorDasar += 3;
        }

        // Bonus jika memiliki pengalaman kerja
        if (!empty($peserta->pengalaman_kerja)) {
            $skorDasar += 5;
        }

        return round($skorDasar, 2);
    }

    private function cekKesesuaianKategori(User $peserta, JadwalKeberangkatan $jadwal)
    {
        if (empty($peserta->pekerjaan_diinginkan)) {
            return true;
        }

        return stripos($peserta->pekerjaan_diinginkan, $jadwal->kategori_pekerjaan) !== false ||
            stripos($jadwal->kategori_pekerjaan, $peserta->pekerjaan_diinginkan) !== false;
    }
}
