<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'telepon',
        'tanggal_lahir',
        'alamat',
        'jenis_kelamin',
        'pendidikan_terakhir',
        'level_bahasa_jepang',
        'skor_bahasa_jepang',
        'pengalaman_kerja',
        'pekerjaan_diinginkan',
        'status',
        'skor_prioritas'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tanggal_lahir' => 'date',
        'password' => 'hashed',
    ];

    // ========================================
    // RELATIONSHIP METHODS
    // ========================================

    /**
     * Relasi dengan dokumen yang dimiliki user
     */
    public function dokumen()
    {
        return $this->hasMany(Dokumen::class);
    }

    /**
     * Relasi dengan jadwal keberangkatan (many-to-many)
     */
    public function jadwalKeberangkatan()
    {
        return $this->belongsToMany(JadwalKeberangkatan::class, 'peserta_jadwal', 'user_id', 'jadwal_id')
            ->withPivot('skor_akhir', 'tanggal_penempatan', 'status_keberangkatan', 'catatan')
            ->withTimestamps();
    }

    /**
     * Relasi untuk dokumen yang diverifikasi oleh user ini (untuk admin)
     */
    public function dokumenYangDiverifikasi()
    {
        return $this->hasMany(Dokumen::class, 'verified_by');
    }

    // ========================================
    // ROLE CHECK METHODS
    // ========================================

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah peserta
     */
    public function isPeserta()
    {
        return $this->role === 'peserta';
    }

    // ========================================
    // SKOR PRIORITAS METHODS
    // ========================================

    /**
     * Hitung skor prioritas berdasarkan kriteria algoritma greedy
     */
    public function hitungSkorPrioritas()
    {
        $skor = 0;

        // 1. Skor berdasarkan kelengkapan dokumen (40%)
        $dokumenDisetujui = $this->dokumen()->where('status_verifikasi', 'disetujui')->count();
        $totalDokumenWajib = 5; // KTP, Ijazah, Sertifikat Bahasa, Foto, CV
        $skorDokumen = $totalDokumenWajib > 0 ? ($dokumenDisetujui / $totalDokumenWajib) * 40 : 0;

        // 2. Skor berdasarkan bahasa Jepang (30%)
        $skorBahasa = $this->skor_bahasa_jepang ? ($this->skor_bahasa_jepang / 100) * 30 : 0;

        // 3. Skor berdasarkan pengalaman kerja (20%)
        $skorPengalaman = !empty($this->pengalaman_kerja) ? 20 : 0;

        // 4. Skor berdasarkan waktu pendaftaran (10%)
        $hariPendaftaran = $this->created_at->diffInDays(now());
        $skorWaktu = max(0, 10 - ($hariPendaftaran * 0.1));

        // 5. Bonus untuk kelengkapan profil
        $kelengkapanProfil = $this->hitungKelengkapanProfil();
        $bonusKelengkapan = ($kelengkapanProfil / 100) * 5; // Bonus maksimal 5 poin

        $skor = $skorDokumen + $skorBahasa + $skorPengalaman + $skorWaktu + $bonusKelengkapan;

        // Update skor di database
        $this->update(['skor_prioritas' => round($skor, 2)]);

        return round($skor, 2);
    }

    /**
     * Hitung persentase kelengkapan profil
     */
    public function hitungKelengkapanProfil()
    {
        $fieldsWajib = [
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
        foreach ($fieldsWajib as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }

        return round(($completed / count($fieldsWajib)) * 100);
    }

    /**
     * Get skor prioritas dengan kategori
     */
    public function getSkorPrioritasAttribute($value)
    {
        return $value ?? 0;
    }

    /**
     * Get kategori skor prioritas
     */
    public function getKategoriSkorAttribute()
    {
        $skor = $this->skor_prioritas;

        if ($skor >= 80) return 'Tinggi';
        if ($skor >= 60) return 'Sedang';
        if ($skor >= 40) return 'Rendah';
        return 'Sangat Rendah';
    }

    // ========================================
    // DOKUMEN HELPER METHODS
    // ========================================

    /**
     * Cek apakah semua dokumen wajib sudah diupload
     */
    public function isAllDokumenUploaded()
    {
        $dokumenWajib = ['ktp', 'ijazah', 'sertifikat_bahasa', 'foto', 'cv'];
        $dokumenUser = $this->dokumen()->pluck('jenis_dokumen')->toArray();

        foreach ($dokumenWajib as $jenis) {
            if (!in_array($jenis, $dokumenUser)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Cek apakah semua dokumen sudah diverifikasi
     */
    public function isAllDokumenVerified()
    {
        $dokumenWajib = 5;
        $dokumenDisetujui = $this->dokumen()->where('status_verifikasi', 'disetujui')->count();

        return $dokumenDisetujui >= $dokumenWajib;
    }

    /**
     * Get jumlah dokumen pending
     */
    public function getPendingDokumenCount()
    {
        return $this->dokumen()->where('status_verifikasi', 'pending')->count();
    }

    /**
     * Get jumlah dokumen yang ditolak
     */
    public function getRejectedDokumenCount()
    {
        return $this->dokumen()->where('status_verifikasi', 'ditolak')->count();
    }

    // ========================================
    // STATUS HELPER METHODS
    // ========================================

    /**
     * Cek apakah user sudah terjadwal
     */
    public function isTerjadwal()
    {
        return $this->status === 'terjadwal' || $this->jadwalKeberangkatan()->exists();
    }

    /**
     * Cek apakah user sudah berangkat
     */
    public function isBerangkat()
    {
        return $this->status === 'berangkat';
    }

    /**
     * Cek apakah user sudah terverifikasi
     */
    public function isTerverifikasi()
    {
        return in_array($this->status, ['terverifikasi', 'terjadwal', 'berangkat']);
    }

    /**
     * Get status label yang user-friendly
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu Verifikasi',
            'terverifikasi' => 'Terverifikasi',
            'terjadwal' => 'Terjadwal Berangkat',
            'berangkat' => 'Sudah Berangkat',
            'ditolak' => 'Ditolak'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get status color untuk UI
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'terverifikasi' => 'success',
            'terjadwal' => 'info',
            'berangkat' => 'primary',
            'ditolak' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    // ========================================
    // SCOPE METHODS
    // ========================================

    /**
     * Scope untuk peserta saja
     */
    public function scopePeserta($query)
    {
        return $query->where('role', 'peserta');
    }

    /**
     * Scope untuk admin saja
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope untuk peserta yang terverifikasi
     */
    public function scopeTerverifikasi($query)
    {
        return $query->where('status', 'terverifikasi');
    }

    /**
     * Scope untuk peserta yang belum terjadwal
     */
    public function scopeBelumTerjadwal($query)
    {
        return $query->where('status', 'terverifikasi')
            ->whereDoesntHave('jadwalKeberangkatan');
    }

    /**
     * Scope untuk pencarian berdasarkan nama atau email
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        });
    }

    // ========================================
    // ACCESSOR METHODS
    // ========================================

    /**
     * Get nama with title untuk display
     */
    public function getNamaLengkapAttribute()
    {
        $title = $this->jenis_kelamin === 'L' ? 'Bpk.' : 'Ibu';
        return $title . ' ' . $this->nama;
    }

    /**
     * Get umur dari tanggal lahir
     */
    public function getUmurAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
    }

    /**
     * Get inisial nama untuk avatar
     */
    public function getInisialAttribute()
    {
        $words = explode(' ', $this->nama);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->nama, 0, 2));
    }
}
