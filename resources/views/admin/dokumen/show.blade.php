@extends('layouts.admin')

@section('title', 'Detail Dokumen')
@section('page-title', 'Detail Dokumen')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('admin.dokumen') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Dokumen
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Document Preview -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-file-alt mr-2"></i>Preview Dokumen
                        </h3>
                    </div>
                    <div class="p-6">
                        @if ($fileExists)
                            <div class="text-center">
                                @if ($dokumen->isPdf())
                                    <embed src="{{ route('admin.dokumen.preview', $dokumen->id) }}" type="application/pdf"
                                        width="100%" height="600px" class="rounded-lg border border-gray-200">
                                @elseif($dokumen->isImage())
                                    <img src="{{ route('admin.dokumen.preview', $dokumen->id) }}" alt="Preview Dokumen"
                                        class="max-w-full h-auto rounded-lg border border-gray-200">
                                @else
                                    <div class="py-12">
                                        <i class="fas fa-file text-gray-300 text-6xl mb-4"></i>
                                        <p class="text-gray-500">Preview tidak tersedia untuk tipe file ini</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-exclamation-triangle text-red-300 text-6xl mb-4"></i>
                                <h3 class="text-lg font-medium text-red-600 mb-2">File Tidak Ditemukan</h3>
                                <p class="text-red-500">File dokumen tidak dapat ditemukan di server.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Document Information -->
            <div class="lg:col-span-1">
                <!-- Document Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-info-circle mr-2"></i>Informasi Dokumen
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Dokumen</label>
                            <p class="text-gray-900">{{ $dokumen->jenis_label }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama File</label>
                            <p class="text-gray-900 break-all">{{ $dokumen->nama_file }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran File</label>
                            <p class="text-gray-900">{{ $fileSizeFormatted }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Verifikasi</label>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if ($dokumen->status_verifikasi == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($dokumen->status_verifikasi == 'disetujui') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                <i
                                    class="fas fa-{{ $dokumen->status_verifikasi == 'pending' ? 'clock' : ($dokumen->status_verifikasi == 'disetujui' ? 'check' : 'times') }} mr-1"></i>
                                {{ $dokumen->status_label }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Upload</label>
                            <p class="text-gray-900">{{ $dokumen->tanggal_upload->format('d F Y, H:i') }}</p>
                        </div>

                        @if ($dokumen->tanggal_verifikasi)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Verifikasi</label>
                                <p class="text-gray-900">{{ $dokumen->tanggal_verifikasi->format('d F Y, H:i') }}</p>
                            </div>
                        @endif

                        @if ($dokumen->verifiedBy)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Diverifikasi Oleh</label>
                                <p class="text-gray-900">{{ $dokumen->verifiedBy->nama }}</p>
                            </div>
                        @endif

                        @if ($dokumen->catatan)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-blue-800 text-sm">{{ $dokumen->catatan }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Peserta Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user mr-2"></i>Informasi Peserta
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div
                                class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-medium mr-4">
                                {{ strtoupper(substr($dokumen->user->nama, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $dokumen->user->nama }}</h4>
                                <p class="text-sm text-gray-500">{{ $dokumen->user->email }}</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Status:</span>
                                <span class="text-sm font-medium {{ $dokumen->user->status_color }}">
                                    {{ $dokumen->user->status_label }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Skor Prioritas:</span>
                                <span class="text-sm font-medium text-blue-600">
                                    {{ number_format($dokumen->user->skor_prioritas, 1) }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('admin.peserta.show', $dokumen->user->id) }}"
                                class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <i class="fas fa-user mr-1"></i>Lihat Profil Peserta
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                @if ($fileExists)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-cogs mr-2"></i>Aksi
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <a href="{{ route('admin.dokumen.download', $dokumen->id) }}"
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-download mr-2"></i>Download File
                            </a>

                            @if ($dokumen->status_verifikasi == 'pending')
                                <button onclick="verifikasiDokumen('disetujui')"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-check mr-2"></i>Setujui Dokumen
                                </button>

                                <button onclick="verifikasiDokumen('ditolak')"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    <i class="fas fa-times mr-2"></i>Tolak Dokumen
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Verifikasi -->
    <div id="verifikasiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Verifikasi Dokumen</h3>
            </div>
            <form action="{{ route('admin.dokumen.verifikasi', $dokumen->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="px-6 py-4">
                    <input type="hidden" name="status_verifikasi" id="statusVerifikasi">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Verifikasi</label>
                        <textarea name="catatan" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            rows="3" placeholder="Berikan catatan verifikasi (opsional)..."></textarea>
                    </div>

                    <div id="warningMessage" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-700">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Pastikan Anda telah memeriksa dokumen dengan teliti sebelum menolak.
                        </p>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeVerifikasiModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="submitBtn"
                        class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function verifikasiDokumen(status) {
            const modal = document.getElementById('verifikasiModal');
            const statusInput = document.getElementById('statusVerifikasi');
            const submitBtn = document.getElementById('submitBtn');
            const warningMessage = document.getElementById('warningMessage');

            statusInput.value = status;

            if (status === 'disetujui') {
                submitBtn.className =
                    'px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors';
                submitBtn.innerHTML = '<i class="fas fa-check mr-1"></i>Setujui Dokumen';
                warningMessage.classList.add('hidden');
            } else {
                submitBtn.className =
                    'px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors';
                submitBtn.innerHTML = '<i class="fas fa-times mr-1"></i>Tolak Dokumen';
                warningMessage.classList.remove('hidden');
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeVerifikasiModal() {
            const modal = document.getElementById('verifikasiModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('verifikasiModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeVerifikasiModal();
            }
        });
    </script>
@endpush
