<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';

    protected $fillable = [
        'user_id',
        'jenis_dokumen',
        'nama_file',
        'file_path',
        'status_verifikasi',
        'catatan',
        'tanggal_upload',
        'tanggal_verifikasi',
        'verified_by'
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
        'tanggal_verifikasi' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIP METHODS
    // ========================================

    /**
     * Relasi dengan user (pemilik dokumen)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi dengan user (yang melakukan verifikasi)
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ========================================
    // STATIC METHODS
    // ========================================

    /**
     * Daftar jenis dokumen yang wajib
     */
    public static function jenisDokumenWajib()
    {
        return [
            'ktp' => 'KTP (Kartu Tanda Penduduk)',
            'ijazah' => 'Ijazah Terakhir',
            'sertifikat_bahasa' => 'Sertifikat Bahasa Jepang',
            'foto' => 'Pas Foto 4x6',
            'cv' => 'Curriculum Vitae (CV)'
        ];
    }

    /**
     * Get daftar status verifikasi
     */
    public static function statusVerifikasi()
    {
        return [
            'pending' => 'Menunggu Verifikasi',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak'
        ];
    }

    /**
     * Get daftar ekstensi file yang diperbolehkan
     */
    public static function allowedExtensions()
    {
        return ['pdf', 'jpg', 'jpeg', 'png'];
    }

    /**
     * Get maksimal ukuran file (dalam bytes)
     */
    public static function maxFileSize()
    {
        return 2048 * 1024; // 2MB in bytes
    }

    // ========================================
    // INSTANCE METHODS
    // ========================================

    /**
     * Cek apakah dokumen sudah diverifikasi
     */
    public function sudahDiverifikasi()
    {
        return $this->status_verifikasi !== 'pending';
    }

    /**
     * Cek apakah dokumen disetujui
     */
    public function disetujui()
    {
        return $this->status_verifikasi === 'disetujui';
    }

    /**
     * Cek apakah dokumen ditolak
     */
    public function ditolak()
    {
        return $this->status_verifikasi === 'ditolak';
    }

    /**
     * Cek apakah file exists di storage
     */
    public function fileExists()
    {
        return Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Get ukuran file
     */
    public function getFileSize()
    {
        if ($this->fileExists()) {
            return Storage::disk('public')->size($this->file_path);
        }
        return 0;
    }

    /**
     * Get ukuran file dalam format yang mudah dibaca
     */
    public function getFormattedFileSize()
    {
        $bytes = $this->getFileSize();

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get mime type file
     */
    public function getMimeType()
    {
        if ($this->fileExists()) {
            $fullPath = storage_path('app/public/' . $this->file_path);
            return mime_content_type($fullPath);
        }
        return null;
    }

    /**
     * Cek apakah file adalah gambar
     */
    public function isImage()
    {
        $mimeType = $this->getMimeType();
        return $mimeType && str_starts_with($mimeType, 'image/');
    }

    /**
     * Cek apakah file adalah PDF
     */
    public function isPdf()
    {
        $mimeType = $this->getMimeType();
        return $mimeType === 'application/pdf';
    }

    /**
     * Get label jenis dokumen
     */
    public function getJenisLabelAttribute()
    {
        $jenis = self::jenisDokumenWajib();
        return $jenis[$this->jenis_dokumen] ?? $this->jenis_dokumen;
    }

    /**
     * Get status label yang user-friendly
     */
    public function getStatusLabelAttribute()
    {
        $statuses = self::statusVerifikasi();
        return $statuses[$this->status_verifikasi] ?? $this->status_verifikasi;
    }

    /**
     * Get status color untuk UI
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'disetujui' => 'success',
            'ditolak' => 'danger'
        ];

        return $colors[$this->status_verifikasi] ?? 'secondary';
    }

    /**
     * Get icon berdasarkan jenis dokumen
     */
    public function getIconAttribute()
    {
        $icons = [
            'ktp' => 'fas fa-id-card',
            'ijazah' => 'fas fa-graduation-cap',
            'sertifikat_bahasa' => 'fas fa-certificate',
            'foto' => 'fas fa-camera',
            'cv' => 'fas fa-file-alt'
        ];

        return $icons[$this->jenis_dokumen] ?? 'fas fa-file';
    }

    // ========================================
    // SCOPE METHODS
    // ========================================

    /**
     * Scope untuk dokumen pending
     */
    public function scopePending($query)
    {
        return $query->where('status_verifikasi', 'pending');
    }

    /**
     * Scope untuk dokumen yang disetujui
     */
    public function scopeDisetujui($query)
    {
        return $query->where('status_verifikasi', 'disetujui');
    }

    /**
     * Scope untuk dokumen yang ditolak
     */
    public function scopeDitolak($query)
    {
        return $query->where('status_verifikasi', 'ditolak');
    }

    /**
     * Scope untuk dokumen berdasarkan jenis
     */
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_dokumen', $jenis);
    }

    /**
     * Scope untuk dokumen yang sudah diverifikasi
     */
    public function scopeSudahDiverifikasi($query)
    {
        return $query->whereIn('status_verifikasi', ['disetujui', 'ditolak']);
    }

    // ========================================
    // EVENT METHODS
    // ========================================

    /**
     * Boot method untuk event handling
     */
    protected static function boot()
    {
        parent::boot();

        // Event ketika dokumen dibuat
        static::created(function ($dokumen) {
            // Update skor prioritas user
            $dokumen->user->hitungSkorPrioritas();
        });

        // Event ketika dokumen diupdate
        static::updated(function ($dokumen) {
            // Update skor prioritas user jika status verifikasi berubah
            if ($dokumen->isDirty('status_verifikasi')) {
                $dokumen->user->hitungSkorPrioritas();
            }
        });

        // Event ketika dokumen dihapus
        static::deleting(function ($dokumen) {
            // Hapus file dari storage
            if ($dokumen->fileExists()) {
                Storage::disk('public')->delete($dokumen->file_path);
            }
        });

        // Event setelah dokumen dihapus
        static::deleted(function ($dokumen) {
            // Update skor prioritas user
            if ($dokumen->user) {
                $dokumen->user->hitungSkorPrioritas();
            }
        });
    }

    // ========================================
    // VALIDATION METHODS
    // ========================================

    /**
     * Validasi apakah jenis dokumen valid
     */
    public static function isValidJenis($jenis)
    {
        return array_key_exists($jenis, self::jenisDokumenWajib());
    }

    /**
     * Validasi apakah status verifikasi valid
     */
    public static function isValidStatus($status)
    {
        return array_key_exists($status, self::statusVerifikasi());
    }

    /**
     * Validasi ekstensi file
     */
    public static function isValidExtension($extension)
    {
        return in_array(strtolower($extension), self::allowedExtensions());
    }

    /**
     * Validasi ukuran file
     */
    public static function isValidSize($size)
    {
        return $size <= self::maxFileSize();
    }

    // ========================================
    // UTILITY METHODS
    // ========================================

    /**
     * Generate nama file unik
     */
    public static function generateFileName($userId, $jenisDokumen, $extension)
    {
        return $userId . '_' . $jenisDokumen . '_' . time() . '.' . $extension;
    }

    /**
     * Get path untuk upload
     */
    public static function getUploadPath()
    {
        return 'dokumen';
    }

    /**
     * Get rules validasi untuk upload
     */
    public static function getValidationRules()
    {
        $maxSize = self::maxFileSize() / 1024; // Convert to KB
        $extensions = implode(',', self::allowedExtensions());

        return [
            'jenis_dokumen' => 'required|in:' . implode(',', array_keys(self::jenisDokumenWajib())),
            'file_dokumen' => "required|file|mimes:{$extensions}|max:{$maxSize}",
        ];
    }

    /**
     * Get custom validation messages
     */
    public static function getValidationMessages()
    {
        return [
            'jenis_dokumen.required' => 'Jenis dokumen wajib dipilih.',
            'jenis_dokumen.in' => 'Jenis dokumen tidak valid.',
            'file_dokumen.required' => 'File dokumen wajib dipilih.',
            'file_dokumen.file' => 'File yang diupload tidak valid.',
            'file_dokumen.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG.',
            'file_dokumen.max' => 'Ukuran file maksimal 2MB.',
        ];
    }
}
