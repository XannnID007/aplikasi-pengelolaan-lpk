<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKeberangkatan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_keberangkatan';

    protected $fillable = [
        'nama_batch',
        'tanggal_keberangkatan',
        'kapasitas_maksimal',
        'jumlah_peserta',
        'tujuan_kota',
        'kategori_pekerjaan',
        'deskripsi',
        'status'
    ];

    protected $casts = [
        'tanggal_keberangkatan' => 'date',
    ];

    // Relasi dengan peserta
    public function peserta()
    {
        return $this->belongsToMany(User::class, 'peserta_jadwal', 'jadwal_id', 'user_id')
            ->withPivot('skor_akhir', 'tanggal_penempatan', 'status_keberangkatan', 'catatan')
            ->withTimestamps();
    }

    // Cek apakah jadwal masih tersedia
    public function masihTersedia()
    {
        return $this->jumlah_peserta < $this->kapasitas_maksimal && $this->status === 'aktif';
    }

    // Cek apakah jadwal sudah penuh
    public function sudahPenuh()
    {
        return $this->jumlah_peserta >= $this->kapasitas_maksimal;
    }

    // Tambah peserta ke jadwal
    public function tambahPeserta($userId, $skorAkhir, $catatan = null)
    {
        if ($this->masihTersedia()) {
            $this->peserta()->attach($userId, [
                'skor_akhir' => $skorAkhir,
                'tanggal_penempatan' => now(),
                'status_keberangkatan' => 'terjadwal',
                'catatan' => $catatan
            ]);

            $this->increment('jumlah_peserta');

            if ($this->sudahPenuh()) {
                $this->update(['status' => 'penuh']);
            }

            return true;
        }

        return false;
    }

    // Hapus peserta dari jadwal
    public function hapusPeserta($userId)
    {
        $this->peserta()->detach($userId);
        $this->decrement('jumlah_peserta');

        if ($this->status === 'penuh') {
            $this->update(['status' => 'aktif']);
        }
    }

    // Hitung sisa kapasitas
    public function sisaKapasitas()
    {
        return $this->kapasitas_maksimal - $this->jumlah_peserta;
    }
}
