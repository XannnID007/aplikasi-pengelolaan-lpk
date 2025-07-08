<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Dokumen::with(['user']);

        // Filter berdasarkan status verifikasi
        if ($request->has('status') && $request->status != '') {
            $query->where('status_verifikasi', $request->status);
        }

        // Filter berdasarkan jenis dokumen
        if ($request->has('jenis') && $request->jenis != '') {
            $query->where('jenis_dokumen', $request->jenis);
        }

        // Search berdasarkan nama peserta
        if ($request->has('search') && $request->search != '') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }

        $dokumen = $query->orderBy('tanggal_upload', 'desc')->paginate(20);

        // Statistik untuk dashboard
        $statistik = [
            'total' => Dokumen::count(),
            'pending' => Dokumen::where('status_verifikasi', 'pending')->count(),
            'disetujui' => Dokumen::where('status_verifikasi', 'disetujui')->count(),
            'ditolak' => Dokumen::where('status_verifikasi', 'ditolak')->count(),
        ];

        // Daftar jenis dokumen untuk filter
        $jenisDokumen = Dokumen::jenisDokumenWajib();

        return view('admin.dokumen.index', compact('dokumen', 'statistik', 'jenisDokumen'));
    }

    public function show($id)
    {
        $dokumen = Dokumen::with(['user', 'verifiedBy'])->findOrFail($id);

        // Cek apakah file exists
        $fileExists = Storage::disk('public')->exists($dokumen->file_path);
        $fileSize = $fileExists ? Storage::disk('public')->size($dokumen->file_path) : 0;
        $fileSizeFormatted = $this->formatFileSize($fileSize);

        return view('admin.dokumen.show', compact('dokumen', 'fileExists', 'fileSizeFormatted'));
    }

    public function verifikasi(Request $request, $id)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string|max:500'
        ]);

        $dokumen = Dokumen::findOrFail($id);

        $dokumen->update([
            'status_verifikasi' => $request->status_verifikasi,
            'catatan' => $request->catatan,
            'tanggal_verifikasi' => now(),
            'verified_by' => auth()->id()
        ]);

        // Update skor prioritas peserta
        $dokumen->user->hitungSkorPrioritas();

        // Cek apakah semua dokumen sudah disetujui untuk update status peserta
        $this->cekStatusPeserta($dokumen->user);

        $status = $request->status_verifikasi == 'disetujui' ? 'disetujui' : 'ditolak';

        return redirect()->back()->with('success', "Dokumen berhasil {$status}.");
    }

    public function download($id)
    {
        $dokumen = Dokumen::findOrFail($id);

        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($dokumen->file_path, $dokumen->nama_file);
    }

    public function preview($id)
    {
        $dokumen = Dokumen::findOrFail($id);

        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }

        $fullPath = storage_path('app/public/' . $dokumen->file_path);

        // Cek apakah file exists
        if (!file_exists($fullPath)) {
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }

        // Get mime type
        $mimeType = mime_content_type($fullPath);

        // Return file dengan proper headers
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $dokumen->nama_file . '"'
        ]);
    }

    public function batchVerifikasi(Request $request)
    {
        $request->validate([
            'dokumen_ids' => 'required|array',
            'dokumen_ids.*' => 'exists:dokumen,id',
            'status_verifikasi' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string|max:500'
        ]);

        $dokumenList = Dokumen::whereIn('id', $request->dokumen_ids)->get();

        foreach ($dokumenList as $dokumen) {
            $dokumen->update([
                'status_verifikasi' => $request->status_verifikasi,
                'catatan' => $request->catatan,
                'tanggal_verifikasi' => now(),
                'verified_by' => auth()->id()
            ]);

            // Update skor prioritas peserta
            $dokumen->user->hitungSkorPrioritas();

            // Cek status peserta
            $this->cekStatusPeserta($dokumen->user);
        }

        $jumlah = count($request->dokumen_ids);
        $status = $request->status_verifikasi == 'disetujui' ? 'disetujui' : 'ditolak';

        return redirect()->back()->with('success', "{$jumlah} dokumen berhasil {$status}.");
    }

    public function tolakSemua(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'catatan' => 'required|string|max:500'
        ]);

        $peserta = User::findOrFail($request->user_id);

        // Tolak semua dokumen pending milik peserta
        $dokumenPending = $peserta->dokumen()->where('status_verifikasi', 'pending')->get();

        foreach ($dokumenPending as $dokumen) {
            $dokumen->update([
                'status_verifikasi' => 'ditolak',
                'catatan' => $request->catatan,
                'tanggal_verifikasi' => now(),
                'verified_by' => auth()->id()
            ]);
        }

        // Update status peserta jadi ditolak
        $peserta->update(['status' => 'ditolak']);

        return redirect()->back()->with('success', 'Semua dokumen peserta telah ditolak.');
    }

    private function cekStatusPeserta(User $peserta)
    {
        // Hitung jumlah dokumen yang disetujui
        $dokumenDisetujui = $peserta->dokumen()->where('status_verifikasi', 'disetujui')->count();
        $dokumenDitolak = $peserta->dokumen()->where('status_verifikasi', 'ditolak')->count();
        $totalDokumenWajib = 5;

        // Jika semua dokumen wajib sudah disetujui
        if ($dokumenDisetujui >= $totalDokumenWajib) {
            $peserta->update(['status' => 'terverifikasi']);
        }
        // Jika ada dokumen yang ditolak dan peserta belum terverifikasi
        elseif ($dokumenDitolak > 0 && $peserta->status == 'pending') {
            $peserta->update(['status' => 'pending']); // Tetap pending sampai semua dokumen disetujui
        }
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getFileInfo($id)
    {
        $dokumen = Dokumen::findOrFail($id);

        $fileExists = Storage::disk('public')->exists($dokumen->file_path);
        $fileSize = $fileExists ? Storage::disk('public')->size($dokumen->file_path) : 0;

        return response()->json([
            'exists' => $fileExists,
            'size' => $fileSize,
            'size_formatted' => $this->formatFileSize($fileSize),
            'path' => $dokumen->file_path,
            'name' => $dokumen->nama_file
        ]);
    }

    public function massDelete(Request $request)
    {
        $request->validate([
            'dokumen_ids' => 'required|array',
            'dokumen_ids.*' => 'exists:dokumen,id'
        ]);

        $dokumenList = Dokumen::whereIn('id', $request->dokumen_ids)->get();
        $deletedCount = 0;

        foreach ($dokumenList as $dokumen) {
            // Hapus file dari storage
            if (Storage::disk('public')->exists($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
            }

            // Hapus record dari database
            $dokumen->delete();
            $deletedCount++;

            // Update skor prioritas peserta
            $dokumen->user->hitungSkorPrioritas();
        }

        return redirect()->back()->with('success', "{$deletedCount} dokumen berhasil dihapus.");
    }
}
