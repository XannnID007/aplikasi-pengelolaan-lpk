<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PesertaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = User::where('role', 'peserta');

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan level bahasa Jepang
        if ($request->has('level_bahasa') && $request->level_bahasa != '') {
            $query->where('level_bahasa_jepang', $request->level_bahasa);
        }

        // Search berdasarkan nama atau email
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $peserta = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistik untuk filter
        $statistik = [
            'total' => User::where('role', 'peserta')->count(),
            'pending' => User::where('role', 'peserta')->where('status', 'pending')->count(),
            'terverifikasi' => User::where('role', 'peserta')->where('status', 'terverifikasi')->count(),
            'terjadwal' => User::where('role', 'peserta')->where('status', 'terjadwal')->count(),
            'berangkat' => User::where('role', 'peserta')->where('status', 'berangkat')->count(),
            'ditolak' => User::where('role', 'peserta')->where('status', 'ditolak')->count(),
        ];

        return view('admin.peserta.index', compact('peserta', 'statistik'));
    }

    public function show($id)
    {
        $peserta = User::where('role', 'peserta')->findOrFail($id);

        // Hitung kelengkapan profil
        $kelengkapanProfil = $this->hitungKelengkapanProfil($peserta);

        // Ambil dokumen peserta
        $dokumen = $peserta->dokumen()->get();

        // Ambil jadwal keberangkatan jika ada
        $jadwalKeberangkatan = $peserta->jadwalKeberangkatan()->first();

        return view('admin.peserta.show', compact('peserta', 'kelengkapanProfil', 'dokumen', 'jadwalKeberangkatan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,terverifikasi,terjadwal,berangkat,ditolak',
            'catatan' => 'nullable|string|max:500'
        ]);

        $peserta = User::where('role', 'peserta')->findOrFail($id);

        $peserta->update([
            'status' => $request->status
        ]);

        // Log perubahan status jika diperlukan
        // Bisa ditambahkan tabel log_status untuk tracking

        return redirect()->back()->with('success', 'Status peserta berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $peserta = User::where('role', 'peserta')->findOrFail($id);

        // Hapus dokumen terkait
        foreach ($peserta->dokumen as $dokumen) {
            if (file_exists(storage_path('app/public/' . $dokumen->file_path))) {
                unlink(storage_path('app/public/' . $dokumen->file_path));
            }
        }

        $peserta->delete();

        return redirect()->route('admin.peserta')->with('success', 'Data peserta berhasil dihapus.');
    }

    public function edit($id)
    {
        $peserta = User::where('role', 'peserta')->findOrFail($id);
        return view('admin.peserta.edit', compact('peserta'));
    }

    public function update(Request $request, $id)
    {
        $peserta = User::where('role', 'peserta')->findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $peserta->id,
            'telepon' => 'required|string|max:20',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'jenis_kelamin' => 'required|in:L,P',
            'pendidikan_terakhir' => 'nullable|string|max:255',
            'level_bahasa_jepang' => 'nullable|string|max:10',
            'skor_bahasa_jepang' => 'nullable|integer|min:0|max:100',
            'pengalaman_kerja' => 'nullable|string',
            'pekerjaan_diinginkan' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->except(['password', 'password_confirmation']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $peserta->update($data);

        // Update skor prioritas
        $peserta->hitungSkorPrioritas();

        return redirect()->route('admin.peserta.show', $peserta->id)
            ->with('success', 'Data peserta berhasil diperbarui.');
    }

    private function hitungKelengkapanProfil($peserta)
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
            if (!empty($peserta->$field)) {
                $completed++;
            }
        }

        return round(($completed / count($fields)) * 100);
    }
}
