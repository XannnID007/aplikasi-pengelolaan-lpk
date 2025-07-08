<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('peserta');
    }

    public function index()
    {
        $user = auth()->user();

        // Hitung kelengkapan profil
        $kelengkapanProfil = $this->hitungKelengkapanProfil($user);

        // Field yang masih kosong
        $fieldKosong = $this->getFieldKosong($user);

        return view('peserta.profil.index', compact('user', 'kelengkapanProfil', 'fieldKosong'));
    }

    public function edit()
    {
        $user = auth()->user();

        // Daftar pilihan untuk form
        $levelBahasaJepang = [
            'N5' => 'N5 (Pemula)',
            'N4' => 'N4 (Dasar)',
            'N3' => 'N3 (Menengah Bawah)',
            'N2' => 'N2 (Menengah Atas)',
            'N1' => 'N1 (Mahir)'
        ];

        $pendidikanOptions = [
            'SD' => 'Sekolah Dasar (SD)',
            'SMP' => 'Sekolah Menengah Pertama (SMP)',
            'SMA/SMK' => 'SMA/SMK Sederajat',
            'D1' => 'Diploma 1 (D1)',
            'D2' => 'Diploma 2 (D2)',
            'D3' => 'Diploma 3 (D3)',
            'D4/S1' => 'Diploma 4/Sarjana (D4/S1)',
            'S2' => 'Magister (S2)',
            'S3' => 'Doktor (S3)'
        ];

        $pekerjaanOptions = [
            'Teknologi Informasi' => 'Teknologi Informasi',
            'Perhotelan' => 'Perhotelan dan Restoran',
            'Manufaktur' => 'Manufaktur dan Pabrik',
            'Administrasi' => 'Administrasi dan Perkantoran',
            'Otomotif' => 'Otomotif dan Mekanik',
            'Konstruksi' => 'Konstruksi dan Bangunan',
            'Pertanian' => 'Pertanian dan Peternakan',
            'Perikanan' => 'Perikanan dan Kelautan',
            'Kesehatan' => 'Kesehatan dan Medis',
            'Pendidikan' => 'Pendidikan dan Pengajaran',
            'Lainnya' => 'Lainnya'
        ];

        return view('peserta.profil.edit', compact('user', 'levelBahasaJepang', 'pendidikanOptions', 'pekerjaanOptions'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'telepon' => 'required|string|max:20',
            'tanggal_lahir' => 'required|date|before:today',
            'alamat' => 'required|string|max:500',
            'jenis_kelamin' => 'required|in:L,P',
            'pendidikan_terakhir' => 'required|string|max:255',
            'level_bahasa_jepang' => 'nullable|in:N5,N4,N3,N2,N1',
            'skor_bahasa_jepang' => 'nullable|integer|min:0|max:100',
            'pengalaman_kerja' => 'nullable|string|max:1000',
            'pekerjaan_diinginkan' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'telepon.required' => 'Nomor telepon wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'alamat.required' => 'Alamat wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'pendidikan_terakhir.required' => 'Pendidikan terakhir wajib diisi.',
            'skor_bahasa_jepang.integer' => 'Skor bahasa Jepang harus berupa angka.',
            'skor_bahasa_jepang.min' => 'Skor bahasa Jepang minimal 0.',
            'skor_bahasa_jepang.max' => 'Skor bahasa Jepang maksimal 100.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Validasi khusus: jika mengisi level bahasa Jepang, skor wajib diisi
        if ($request->filled('level_bahasa_jepang') && !$request->filled('skor_bahasa_jepang')) {
            return redirect()->back()->withErrors([
                'skor_bahasa_jepang' => 'Skor bahasa Jepang wajib diisi jika level bahasa Jepang dipilih.'
            ])->withInput();
        }

        $data = $request->except(['password', 'password_confirmation']);

        // Update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Update skor prioritas
        $user->hitungSkorPrioritas();

        return redirect()->route('peserta.profil')
            ->with('success', 'Profil berhasil diperbarui.');
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

    private function getFieldKosong($user)
    {
        $fieldsLabels = [
            'nama' => 'Nama Lengkap',
            'email' => 'Email',
            'telepon' => 'Nomor Telepon',
            'tanggal_lahir' => 'Tanggal Lahir',
            'jenis_kelamin' => 'Jenis Kelamin',
            'pendidikan_terakhir' => 'Pendidikan Terakhir',
            'level_bahasa_jepang' => 'Level Bahasa Jepang',
            'skor_bahasa_jepang' => 'Skor Bahasa Jepang',
            'pengalaman_kerja' => 'Pengalaman Kerja',
            'pekerjaan_diinginkan' => 'Pekerjaan yang Diinginkan'
        ];

        $fieldKosong = [];
        foreach ($fieldsLabels as $field => $label) {
            if (empty($user->$field)) {
                $fieldKosong[] = $label;
            }
        }

        return $fieldKosong;
    }
}
