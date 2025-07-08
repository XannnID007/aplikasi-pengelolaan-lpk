<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('peserta');
    }

    public function index()
    {
        $user = auth()->user();

        // Ambil semua dokumen milik user
        $dokumenUser = $user->dokumen()->orderBy('tanggal_upload', 'desc')->get();

        // Daftar dokumen wajib
        $dokumenWajib = Dokumen::jenisDokumenWajib();

        // Kelompokkan dokumen berdasarkan jenis
        $dokumenByJenis = $dokumenUser->keyBy('jenis_dokumen');

        // Statistik dokumen
        $statistikDokumen = [
            'total' => $dokumenUser->count(),
            'disetujui' => $dokumenUser->where('status_verifikasi', 'disetujui')->count(),
            'pending' => $dokumenUser->where('status_verifikasi', 'pending')->count(),
            'ditolak' => $dokumenUser->where('status_verifikasi', 'ditolak')->count(),
        ];

        // Progress kelengkapan dokumen
        $progressDokumen = round(($statistikDokumen['disetujui'] / count($dokumenWajib)) * 100);

        return view('peserta.dokumen.index', compact(
            'dokumenUser',
            'dokumenWajib',
            'dokumenByJenis',
            'statistikDokumen',
            'progressDokumen'
        ));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'jenis_dokumen' => 'required|in:' . implode(',', array_keys(Dokumen::jenisDokumenWajib())),
            'file_dokumen' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // Max 2MB
        ], [
            'jenis_dokumen.required' => 'Jenis dokumen wajib dipilih.',
            'jenis_dokumen.in' => 'Jenis dokumen tidak valid.',
            'file_dokumen.required' => 'File dokumen wajib dipilih.',
            'file_dokumen.file' => 'File yang diupload tidak valid.',
            'file_dokumen.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG.',
            'file_dokumen.max' => 'Ukuran file maksimal 2MB.',
        ]);

        $user = auth()->user();

        // Cek apakah dokumen jenis ini sudah ada dan disetujui
        $dokumenExisting = $user->dokumen()
            ->where('jenis_dokumen', $request->jenis_dokumen)
            ->where('status_verifikasi', 'disetujui')
            ->first();

        if ($dokumenExisting) {
            return redirect()->back()->with('error', 'Dokumen jenis ini sudah disetujui. Tidak dapat mengupload ulang.');
        }

        // Hapus dokumen lama jika ada (yang pending atau ditolak)
        $dokumenLama = $user->dokumen()
            ->where('jenis_dokumen', $request->jenis_dokumen)
            ->whereIn('status_verifikasi', ['pending', 'ditolak'])
            ->first();

        if ($dokumenLama) {
            // Hapus file lama
            if (Storage::disk('public')->exists($dokumenLama->file_path)) {
                Storage::disk('public')->delete($dokumenLama->file_path);
            }
            $dokumenLama->delete();
        }

        // Upload file baru
        $file = $request->file('file_dokumen');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        // Generate nama file unik
        $fileName = $user->id . '_' . $request->jenis_dokumen . '_' . time() . '.' . $extension;

        // Simpan file
        $filePath = $file->storeAs('dokumen', $fileName, 'public');

        // Simpan ke database
        Dokumen::create([
            'user_id' => $user->id,
            'jenis_dokumen' => $request->jenis_dokumen,
            'nama_file' => $originalName,
            'file_path' => $filePath,
            'status_verifikasi' => 'pending',
            'tanggal_upload' => now(),
        ]);

        // Update skor prioritas user
        $user->hitungSkorPrioritas();

        $jenisDokumen = Dokumen::jenisDokumenWajib()[$request->jenis_dokumen];

        return redirect()->back()->with('success', "Dokumen {$jenisDokumen} berhasil diupload. Menunggu verifikasi admin.");
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $dokumen = Dokumen::where('user_id', $user->id)->findOrFail($id);

        // Tidak bisa hapus dokumen yang sudah disetujui
        if ($dokumen->status_verifikasi === 'disetujui') {
            return redirect()->back()->with('error', 'Tidak dapat menghapus dokumen yang sudah disetujui.');
        }

        // Hapus file dari storage
        if (Storage::disk('public')->exists($dokumen->file_path)) {
            Storage::disk('public')->delete($dokumen->file_path);
        }

        $dokumen->delete();

        // Update skor prioritas user
        $user->hitungSkorPrioritas();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function download($id)
    {
        $user = auth()->user();
        $dokumen = Dokumen::where('user_id', $user->id)->findOrFail($id);

        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($dokumen->file_path, $dokumen->nama_file);
    }

    public function preview($id)
    {
        $user = auth()->user();
        $dokumen = Dokumen::where('user_id', $user->id)->findOrFail($id);

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

    public function uploadMultiple(Request $request)
    {
        $request->validate([
            'dokumen_files' => 'required|array|min:1|max:5',
            'dokumen_files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'jenis_dokumen' => 'required|array|min:1',
            'jenis_dokumen.*' => 'in:' . implode(',', array_keys(Dokumen::jenisDokumenWajib())),
        ], [
            'dokumen_files.required' => 'Minimal satu file harus dipilih.',
            'dokumen_files.array' => 'Format file tidak valid.',
            'dokumen_files.max' => 'Maksimal 5 file dapat diupload sekaligus.',
            'dokumen_files.*.file' => 'Semua file harus valid.',
            'dokumen_files.*.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG.',
            'dokumen_files.*.max' => 'Ukuran setiap file maksimal 2MB.',
            'jenis_dokumen.required' => 'Jenis dokumen wajib dipilih untuk setiap file.',
        ]);

        $user = auth()->user();
        $uploadedCount = 0;
        $errors = [];

        foreach ($request->file('dokumen_files') as $index => $file) {
            if (!isset($request->jenis_dokumen[$index])) {
                $errors[] = "Jenis dokumen untuk file ke-" . ($index + 1) . " tidak dipilih.";
                continue;
            }

            $jenisDokumen = $request->jenis_dokumen[$index];

            // Cek apakah dokumen jenis ini sudah ada dan disetujui
            $dokumenExisting = $user->dokumen()
                ->where('jenis_dokumen', $jenisDokumen)
                ->where('status_verifikasi', 'disetujui')
                ->first();

            if ($dokumenExisting) {
                $jenisDokumenLabel = Dokumen::jenisDokumenWajib()[$jenisDokumen];
                $errors[] = "Dokumen {$jenisDokumenLabel} sudah disetujui.";
                continue;
            }

            try {
                // Hapus dokumen lama jika ada
                $dokumenLama = $user->dokumen()
                    ->where('jenis_dokumen', $jenisDokumen)
                    ->whereIn('status_verifikasi', ['pending', 'ditolak'])
                    ->first();

                if ($dokumenLama) {
                    if (Storage::disk('public')->exists($dokumenLama->file_path)) {
                        Storage::disk('public')->delete($dokumenLama->file_path);
                    }
                    $dokumenLama->delete();
                }

                // Upload file baru
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = $user->id . '_' . $jenisDokumen . '_' . time() . '_' . $index . '.' . $extension;
                $filePath = $file->storeAs('dokumen', $fileName, 'public');

                // Simpan ke database
                Dokumen::create([
                    'user_id' => $user->id,
                    'jenis_dokumen' => $jenisDokumen,
                    'nama_file' => $originalName,
                    'file_path' => $filePath,
                    'status_verifikasi' => 'pending',
                    'tanggal_upload' => now(),
                ]);

                $uploadedCount++;
            } catch (\Exception $e) {
                $errors[] = "Gagal mengupload file: " . $file->getClientOriginalName() . " - " . $e->getMessage();
            }
        }

        // Update skor prioritas user
        $user->hitungSkorPrioritas();

        $message = "{$uploadedCount} dokumen berhasil diupload.";
        if (!empty($errors)) {
            $message .= " Namun ada beberapa error: " . implode(', ', $errors);
        }

        return redirect()->back()->with($uploadedCount > 0 ? 'success' : 'error', $message);
    }

    public function getFileSize($id)
    {
        $user = auth()->user();
        $dokumen = Dokumen::where('user_id', $user->id)->findOrFail($id);

        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            return 0;
        }

        return Storage::disk('public')->size($dokumen->file_path);
    }

    public function checkFileExists($id)
    {
        $user = auth()->user();
        $dokumen = Dokumen::where('user_id', $user->id)->findOrFail($id);

        return Storage::disk('public')->exists($dokumen->file_path);
    }
}
