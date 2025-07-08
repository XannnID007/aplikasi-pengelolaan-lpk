<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\JadwalKeberangkatan;
use App\Models\Dokumen;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create Admin User
        $admin = User::create([
            'nama' => 'Administrator LPK',
            'email' => 'admin@lpkjepang.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'telepon' => '081234567890',
            'tanggal_lahir' => '1990-01-01',
            'alamat' => 'Jakarta, Indonesia',
            'jenis_kelamin' => 'L',
            'pendidikan_terakhir' => 'S1 Manajemen',
            'status' => 'terverifikasi',
            'skor_prioritas' => 100
        ]);

        // Create Sample Peserta
        $pesertaData = [
            [
                'nama' => 'Andi Prasetyo',
                'email' => 'andi@email.com',
                'password' => Hash::make('password123'),
                'role' => 'peserta',
                'telepon' => '081234567891',
                'tanggal_lahir' => '1995-05-15',
                'alamat' => 'Jl. Merdeka No. 123, Bandung, Jawa Barat',
                'jenis_kelamin' => 'L',
                'pendidikan_terakhir' => 'S1 Teknik Informatika',
                'level_bahasa_jepang' => 'N4',
                'skor_bahasa_jepang' => 75,
                'pengalaman_kerja' => 'Staff IT di PT ABC Jakarta selama 2 tahun. Menangani maintenance sistem dan development website perusahaan.',
                'pekerjaan_diinginkan' => 'Teknologi Informasi',
                'status' => 'terverifikasi',
                'skor_prioritas' => 85.5
            ],
            [
                'nama' => 'Sari Dewi Lestari',
                'email' => 'sari@email.com',
                'password' => Hash::make('password123'),
                'role' => 'peserta',
                'telepon' => '081234567892',
                'tanggal_lahir' => '1993-08-22',
                'alamat' => 'Jl. Diponegoro No. 45, Surabaya, Jawa Timur',
                'jenis_kelamin' => 'P',
                'pendidikan_terakhir' => 'D3 Perhotelan',
                'level_bahasa_jepang' => 'N3',
                'skor_bahasa_jepang' => 82,
                'pengalaman_kerja' => 'Resepsionis hotel bintang 4 selama 3 tahun. Melayani tamu domestik dan internasional dengan baik.',
                'pekerjaan_diinginkan' => 'Perhotelan',
                'status' => 'terverifikasi',
                'skor_prioritas' => 88.2
            ],
            [
                'nama' => 'Budi Santoso',
                'email' => 'budi@email.com',
                'password' => Hash::make('password123'),
                'role' => 'peserta',
                'telepon' => '081234567893',
                'tanggal_lahir' => '1992-12-10',
                'alamat' => 'Jl. Malioboro No. 78, Yogyakarta, DIY',
                'jenis_kelamin' => 'L',
                'pendidikan_terakhir' => 'SMK Teknik Mesin',
                'level_bahasa_jepang' => 'N5',
                'skor_bahasa_jepang' => 65,
                'pengalaman_kerja' => 'Operator mesin di pabrik otomotif selama 1 tahun. Menguasai pengoperasian mesin CNC dan maintenance dasar.',
                'pekerjaan_diinginkan' => 'Manufaktur',
                'status' => 'pending',
                'skor_prioritas' => 72.1
            ],
            [
                'nama' => 'Maya Sari Indah',
                'email' => 'maya@email.com',
                'password' => Hash::make('password123'),
                'role' => 'peserta',
                'telepon' => '081234567894',
                'tanggal_lahir' => '1994-03-18',
                'alamat' => 'Jl. Imam Bonjol No. 234, Medan, Sumatera Utara',
                'jenis_kelamin' => 'P',
                'pendidikan_terakhir' => 'S1 Ekonomi Akuntansi',
                'level_bahasa_jepang' => 'N4',
                'skor_bahasa_jepang' => 78,
                'pengalaman_kerja' => 'Admin keuangan di perusahaan trading selama 2 tahun. Mengelola pembukuan dan laporan keuangan bulanan.',
                'pekerjaan_diinginkan' => 'Administrasi',
                'status' => 'terverifikasi',
                'skor_prioritas' => 81.3
            ],
            [
                'nama' => 'Rizki Firmansyah',
                'email' => 'rizki@email.com',
                'password' => Hash::make('password123'),
                'role' => 'peserta',
                'telepon' => '081234567895',
                'tanggal_lahir' => '1996-07-25',
                'alamat' => 'Jl. Veteran No. 567, Makassar, Sulawesi Selatan',
                'jenis_kelamin' => 'L',
                'pendidikan_terakhir' => 'SMK Otomotif',
                'level_bahasa_jepang' => 'N5',
                'skor_bahasa_jepang' => 70,
                'pengalaman_kerja' => 'Mekanik bengkel mobil selama 1.5 tahun. Berpengalaman dalam service engine dan sistem kelistrikan kendaraan.',
                'pekerjaan_diinginkan' => 'Otomotif',
                'status' => 'pending',
                'skor_prioritas' => 74.8
            ],
            [
                'nama' => 'Dewi Kartika Sari',
                'email' => 'dewi@email.com',
                'password' => Hash::make('password123'),
                'role' => 'peserta',
                'telepon' => '081234567896',
                'tanggal_lahir' => '1997-11-30',
                'alamat' => 'Jl. Gajah Mada No. 89, Denpasar, Bali',
                'jenis_kelamin' => 'P',
                'pendidikan_terakhir' => 'D3 Keperawatan',
                'level_bahasa_jepang' => 'N4',
                'skor_bahasa_jepang' => 80,
                'pengalaman_kerja' => 'Perawat di rumah sakit selama 2 tahun. Berpengalaman merawat pasien geriatri dan lansia.',
                'pekerjaan_diinginkan' => 'Kesehatan',
                'status' => 'terverifikasi',
                'skor_prioritas' => 83.7
            ],
            [
                'nama' => 'Ahmad Fauzi',
                'email' => 'ahmad@email.com',
                'password' => Hash::make('password123'),
                'role' => 'peserta',
                'telepon' => '081234567897',
                'tanggal_lahir' => '1991-09-14',
                'alamat' => 'Jl. Sudirman No. 456, Palembang, Sumatera Selatan',
                'jenis_kelamin' => 'L',
                'pendidikan_terakhir' => 'SMK Teknik Bangunan',
                'level_bahasa_jepang' => 'N5',
                'skor_bahasa_jepang' => 68,
                'pengalaman_kerja' => 'Tukang bangunan dan konstruksi selama 3 tahun. Menguasai teknik finishing dan instalasi.',
                'pekerjaan_diinginkan' => 'Konstruksi',
                'status' => 'pending',
                'skor_prioritas' => 76.4
            ],
            [
                'nama' => 'Linda Permata',
                'email' => 'linda@email.com',
                'password' => Hash::make('password123'),
                'role' => 'peserta',
                'telepon' => '081234567898',
                'tanggal_lahir' => '1998-04-05',
                'alamat' => 'Jl. Ahmad Yani No. 321, Banjarmasin, Kalimantan Selatan',
                'jenis_kelamin' => 'P',
                'pendidikan_terakhir' => 'SMA IPA',
                'level_bahasa_jepang' => 'N5',
                'skor_bahasa_jepang' => 72,
                'pengalaman_kerja' => 'Pelayan restoran Jepang selama 1 tahun. Memahami budaya pelayanan dan etika kerja Jepang.',
                'pekerjaan_diinginkan' => 'Perhotelan',
                'status' => 'pending',
                'skor_prioritas' => 68.9
            ]
        ];

        foreach ($pesertaData as $data) {
            User::create($data);
        }

        // Create Sample Jadwal Keberangkatan
        $jadwalData = [
            [
                'nama_batch' => 'Batch Tokyo-IT-01-2025',
                'tanggal_keberangkatan' => '2025-03-15',
                'kapasitas_maksimal' => 30,
                'jumlah_peserta' => 0,
                'tujuan_kota' => 'Tokyo',
                'kategori_pekerjaan' => 'Teknologi Informasi',
                'deskripsi' => 'Program pekerja IT untuk perusahaan teknologi di Tokyo. Fokus pada web development, mobile app, dan sistem informasi.',
                'status' => 'aktif'
            ],
            [
                'nama_batch' => 'Batch Osaka-Hotel-01-2025',
                'tanggal_keberangkatan' => '2025-04-10',
                'kapasitas_maksimal' => 25,
                'jumlah_peserta' => 0,
                'tujuan_kota' => 'Osaka',
                'kategori_pekerjaan' => 'Perhotelan',
                'deskripsi' => 'Program pekerja hotel dan restoran di Osaka. Meliputi front office, housekeeping, dan food & beverage service.',
                'status' => 'aktif'
            ],
            [
                'nama_batch' => 'Batch Nagoya-Manufaktur-01-2025',
                'tanggal_keberangkatan' => '2025-05-05',
                'kapasitas_maksimal' => 35,
                'jumlah_peserta' => 0,
                'tujuan_kota' => 'Nagoya',
                'kategori_pekerjaan' => 'Manufaktur',
                'deskripsi' => 'Program pekerja pabrik dan manufaktur di Nagoya. Industri otomotif, elektronik, dan machinery.',
                'status' => 'aktif'
            ],
            [
                'nama_batch' => 'Batch Tokyo-Admin-01-2025',
                'tanggal_keberangkatan' => '2025-06-20',
                'kapasitas_maksimal' => 20,
                'jumlah_peserta' => 0,
                'tujuan_kota' => 'Tokyo',
                'kategori_pekerjaan' => 'Administrasi',
                'deskripsi' => 'Program pekerja administrasi dan perkantoran di Tokyo. Accounting, HR, dan general office work.',
                'status' => 'aktif'
            ],
            [
                'nama_batch' => 'Batch Kyoto-Otomotif-01-2025',
                'tanggal_keberangkatan' => '2025-07-15',
                'kapasitas_maksimal' => 15,
                'jumlah_peserta' => 0,
                'tujuan_kota' => 'Kyoto',
                'kategori_pekerjaan' => 'Otomotif',
                'deskripsi' => 'Program pekerja otomotif dan mekanik di Kyoto. Maintenance, repair, dan assembly kendaraan.',
                'status' => 'aktif'
            ],
            [
                'nama_batch' => 'Batch Sendai-Kesehatan-01-2025',
                'tanggal_keberangkatan' => '2025-08-10',
                'kapasitas_maksimal' => 18,
                'jumlah_peserta' => 0,
                'tujuan_kota' => 'Sendai',
                'kategori_pekerjaan' => 'Kesehatan',
                'deskripsi' => 'Program perawat dan caregiver untuk lansia di Sendai. Perawatan medis dan pendampingan harian.',
                'status' => 'aktif'
            ],
            [
                'nama_batch' => 'Batch Hiroshima-Konstruksi-01-2025',
                'tanggal_keberangkatan' => '2025-09-05',
                'kapasitas_maksimal' => 22,
                'jumlah_peserta' => 0,
                'tujuan_kota' => 'Hiroshima',
                'kategori_pekerjaan' => 'Konstruksi',
                'deskripsi' => 'Program pekerja konstruksi dan bangunan di Hiroshima. Pembangunan infrastruktur dan renovasi.',
                'status' => 'aktif'
            ]
        ];

        foreach ($jadwalData as $data) {
            JadwalKeberangkatan::create($data);
        }

        // Create sample dokumen untuk beberapa peserta
        $this->createSampleDokumen();

        $this->command->info('Database seeder completed successfully!');
        $this->command->info('=====================================');
        $this->command->info('LOGIN CREDENTIALS:');
        $this->command->info('=====================================');
        $this->command->info('🔑 ADMIN LOGIN:');
        $this->command->info('   Email: admin@lpkjepang.com');
        $this->command->info('   Password: admin123');
        $this->command->info('');
        $this->command->info('👤 SAMPLE PESERTA LOGIN:');
        $this->command->info('   Email: andi@email.com');
        $this->command->info('   Password: password123');
        $this->command->info('');
        $this->command->info('   Email: sari@email.com');
        $this->command->info('   Password: password123');
        $this->command->info('=====================================');
    }

    private function createSampleDokumen()
    {
        // Get beberapa peserta untuk dibuat sample dokumen
        $pesertaWithDocs = User::where('role', 'peserta')
            ->whereIn('email', ['andi@email.com', 'sari@email.com', 'maya@email.com', 'dewi@email.com'])
            ->get();

        $jenisDokumen = ['ktp', 'ijazah', 'sertifikat_bahasa', 'foto', 'cv'];

        foreach ($pesertaWithDocs as $peserta) {
            foreach ($jenisDokumen as $jenis) {
                // Buat beberapa dokumen sudah disetujui, beberapa pending
                $status = 'disetujui';
                $tanggalVerifikasi = now()->subDays(rand(1, 10));
                $verifiedBy = 1; // Admin ID

                // Untuk peserta tertentu, buat beberapa dokumen masih pending
                if ($peserta->email == 'andi@email.com' && in_array($jenis, ['cv'])) {
                    $status = 'pending';
                    $tanggalVerifikasi = null;
                    $verifiedBy = null;
                } elseif ($peserta->email == 'maya@email.com' && in_array($jenis, ['sertifikat_bahasa', 'cv'])) {
                    $status = 'pending';
                    $tanggalVerifikasi = null;
                    $verifiedBy = null;
                }

                Dokumen::create([
                    'user_id' => $peserta->id,
                    'jenis_dokumen' => $jenis,
                    'nama_file' => "sample_{$jenis}_{$peserta->id}.pdf",
                    'file_path' => "dokumen/sample_{$jenis}_{$peserta->id}.pdf",
                    'status_verifikasi' => $status,
                    'catatan' => $status == 'disetujui' ? 'Dokumen lengkap dan sesuai persyaratan.' : null,
                    'tanggal_upload' => now()->subDays(rand(5, 15)),
                    'tanggal_verifikasi' => $tanggalVerifikasi,
                    'verified_by' => $verifiedBy
                ]);
            }
        }

        // Update skor prioritas setelah dokumen dibuat
        foreach ($pesertaWithDocs as $peserta) {
            $peserta->hitungSkorPrioritas();
        }
    }
}
