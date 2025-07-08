<?php

namespace App\Services;

use App\Models\User;
use App\Models\JadwalKeberangkatan;
use Illuminate\Support\Collection;

class GreedySchedulerService
{
     /**
      * Algoritma Greedy untuk penjadwalan optimal
      * Tujuan: Memaksimalkan jumlah peserta yang dapat dijadwalkan
      * dengan mempertimbangkan skor prioritas dan kecocokan kategori
      */
     public function generateOptimalSchedule()
     {
          // Ambil semua peserta yang terverifikasi dan belum terjadwal
          $pesertaTersedia = User::where('role', 'peserta')
               ->where('status', 'terverifikasi')
               ->whereDoesntHave('jadwalKeberangkatan')
               ->get();

          // Ambil semua jadwal yang masih aktif
          $jadwalTersedia = JadwalKeberangkatan::where('status', 'aktif')
               ->where('tanggal_keberangkatan', '>=', now())
               ->orderBy('tanggal_keberangkatan')
               ->get();

          $hasilPenjadwalan = [];

          // Update skor prioritas untuk semua peserta
          foreach ($pesertaTersedia as $peserta) {
               $peserta->hitungSkorPrioritas();
          }

          // Urutkan peserta berdasarkan skor prioritas (descending)
          $pesertaTersedia = $pesertaTersedia->sortByDesc('skor_prioritas');

          // Algoritma Greedy: untuk setiap jadwal, pilih peserta terbaik
          foreach ($jadwalTersedia as $jadwal) {
               $pesertaUntukJadwal = $this->pilihPesertaOptimal($pesertaTersedia, $jadwal);

               foreach ($pesertaUntukJadwal as $peserta) {
                    $skorAkhir = $this->hitungSkorAkhir($peserta, $jadwal);

                    // Tambahkan peserta ke jadwal
                    $jadwal->tambahPeserta($peserta->id, $skorAkhir, 'Dijadwalkan otomatis oleh sistem');

                    // Update status peserta
                    $peserta->update(['status' => 'terjadwal']);

                    // Hapus peserta dari daftar tersedia
                    $pesertaTersedia = $pesertaTersedia->reject(function ($item) use ($peserta) {
                         return $item->id === $peserta->id;
                    });

                    $hasilPenjadwalan[] = [
                         'peserta' => $peserta,
                         'jadwal' => $jadwal,
                         'skor_akhir' => $skorAkhir
                    ];

                    // Jika jadwal sudah penuh, lanjut ke jadwal berikutnya
                    if ($jadwal->sudahPenuh()) {
                         break;
                    }
               }
          }

          return $hasilPenjadwalan;
     }

     /**
      * Pilih peserta optimal untuk jadwal tertentu
      */
     private function pilihPesertaOptimal(Collection $pesertaTersedia, JadwalKeberangkatan $jadwal)
     {
          $pesertaTerpilih = collect();
          $sisaKapasitas = $jadwal->sisaKapasitas();

          // Filter peserta yang cocok dengan kategori pekerjaan
          $pesertaCocok = $pesertaTersedia->filter(function ($peserta) use ($jadwal) {
               return $this->cekKesesuaianKategori($peserta, $jadwal);
          });

          // Jika tidak ada peserta yang cocok, ambil semua peserta
          if ($pesertaCocok->isEmpty()) {
               $pesertaCocok = $pesertaTersedia;
          }

          // Urutkan berdasarkan skor prioritas
          $pesertaCocok = $pesertaCocok->sortByDesc('skor_prioritas');

          // Ambil peserta sebanyak kapasitas yang tersedia
          $pesertaTerpilih = $pesertaCocok->take($sisaKapasitas);

          return $pesertaTerpilih;
     }

     /**
      * Cek kesesuaian kategori pekerjaan peserta dengan jadwal
      */
     private function cekKesesuaianKategori(User $peserta, JadwalKeberangkatan $jadwal)
     {
          // Jika peserta tidak menentukan pekerjaan yang diinginkan, dianggap cocok
          if (empty($peserta->pekerjaan_diinginkan)) {
               return true;
          }

          // Cek kesesuaian kategori pekerjaan
          return stripos($peserta->pekerjaan_diinginkan, $jadwal->kategori_pekerjaan) !== false ||
               stripos($jadwal->kategori_pekerjaan, $peserta->pekerjaan_diinginkan) !== false;
     }

     /**
      * Hitung skor akhir peserta untuk jadwal tertentu
      */
     private function hitungSkorAkhir(User $peserta, JadwalKeberangkatan $jadwal)
     {
          $skorDasar = $peserta->skor_prioritas;

          // Bonus jika kategori pekerjaan cocok
          if ($this->cekKesesuaianKategori($peserta, $jadwal)) {
               $skorDasar += 10;
          }

          // Bonus jika skor bahasa Jepang tinggi
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

     /**
      * Analisis efektivitas algoritma
      */
     public function analisisEfektivitas()
     {
          $totalPesertaTerverifikasi = User::where('role', 'peserta')
               ->where('status', 'terverifikasi')
               ->count();

          $totalPesertaTerjadwal = User::where('role', 'peserta')
               ->where('status', 'terjadwal')
               ->count();

          $totalKapasitasJadwal = JadwalKeberangkatan::where('status', 'aktif')
               ->orWhere('status', 'penuh')
               ->sum('kapasitas_maksimal');

          $totalPesertaDijadwalkan = JadwalKeberangkatan::where('status', 'aktif')
               ->orWhere('status', 'penuh')
               ->sum('jumlah_peserta');

          $efektivitasPenjadwalan = $totalPesertaTerverifikasi > 0
               ? ($totalPesertaTerjadwal / $totalPesertaTerverifikasi) * 100
               : 0;

          $pemanfaatanKapasitas = $totalKapasitasJadwal > 0
               ? ($totalPesertaDijadwalkan / $totalKapasitasJadwal) * 100
               : 0;

          return [
               'total_peserta_terverifikasi' => $totalPesertaTerverifikasi,
               'total_peserta_terjadwal' => $totalPesertaTerjadwal,
               'efektivitas_penjadwalan' => round($efektivitasPenjadwalan, 2),
               'pemanfaatan_kapasitas' => round($pemanfaatanKapasitas, 2),
               'total_kapasitas' => $totalKapasitasJadwal,
               'total_terjadwalkan' => $totalPesertaDijadwalkan
          ];
     }
}
