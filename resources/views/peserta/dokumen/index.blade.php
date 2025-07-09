@extends('layouts.peserta')

@section('title', 'Dokumen Saya')
@section('page-title', 'Dokumen Saya')

@section('content')
    <!-- Progress Overview -->
    <div class="bg-gradient-to-r from-blue-500 to-green-600 rounded-xl shadow-lg p-6 text-white mb-8">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <h3 class="text-xl font-bold mb-2">Progress Upload Dokumen</h3>
                <div class="w-full bg-white bg-opacity-20 rounded-full h-3 mb-2">
                    <div class="bg-white h-3 rounded-full transition-all duration-500" style="width: {{ $progressDokumen }}%">
                    </div>
                </div>
                <p class="text-blue-100">{{ $statistikDokumen['disetujui'] }} dari {{ count($dokumenWajib) }} dokumen sudah
                    disetujui ({{ $progressDokumen }}%)</p>
            </div>
            <div class="ml-6 text-center">
                <div class="text-3xl font-bold mb-1">{{ $statistikDokumen['disetujui'] }}/{{ count($dokumenWajib) }}</div>
                <div class="text-sm text-blue-100">Dokumen Lengkap</div>
            </div>
        </div>
    </div>

    <!-- Statistik Dokumen -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-blue-600">{{ $statistikDokumen['total'] }}</div>
                    <div class="text-sm text-gray-600">Total Upload</div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-green-600">{{ $statistikDokumen['disetujui'] }}</div>
                    <div class="text-sm text-gray-600">Disetujui</div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $statistikDokumen['pending'] }}</div>
                    <div class="text-sm text-gray-600">Menunggu</div>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-red-600">{{ $statistikDokumen['ditolak'] }}</div>
                    <div class="text-sm text-gray-600">Ditolak</div>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert jika semua dokumen sudah lengkap -->
    @if ($progressDokumen >= 100)
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-8">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Selamat!</h3>
                    <p class="text-sm text-green-700">Semua dokumen wajib sudah lengkap dan disetujui. Status Anda akan
                        segera diperbarui.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Daftar Dokumen Wajib -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-list-check mr-2"></i>Dokumen Wajib
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach ($dokumenWajib as $jenis => $label)
                    @php
                        $dokumen = $dokumenByJenis->get($jenis);
                    @endphp
                    <div
                        class="border rounded-xl 
                        @if ($dokumen && $dokumen->status_verifikasi == 'disetujui') border-green-300 bg-green-50
                        @elseif($dokumen && $dokumen->status_verifikasi == 'pending') border-yellow-300 bg-yellow-50
                        @elseif($dokumen && $dokumen->status_verifikasi == 'ditolak') border-red-300 bg-red-50
                        @else border-gray-200 @endif">

                        <!-- Header -->
                        <div
                            class="px-6 py-4 border-b 
                            @if ($dokumen && $dokumen->status_verifikasi == 'disetujui') border-green-200
                            @elseif($dokumen && $dokumen->status_verifikasi == 'pending') border-yellow-200
                            @elseif($dokumen && $dokumen->status_verifikasi == 'ditolak') border-red-200
                            @else border-gray-200 @endif">

                            <div class="flex justify-between items-center">
                                <h4 class="font-medium text-gray-900">{{ $label }}</h4>

                                @if ($dokumen)
                                    @switch($dokumen->status_verifikasi)
                                        @case('disetujui')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>Disetujui
                                            </span>
                                        @break

                                        @case('pending')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>Menunggu
                                            </span>
                                        @break

                                        @case('ditolak')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times mr-1"></i>Ditolak
                                            </span>
                                        @break
                                    @endswitch
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-upload mr-1"></i>Belum Upload
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="p-6">
                            @if ($dokumen)
                                <!-- Info Dokumen -->
                                <div class="mb-4">
                                    <p class="text-sm text-gray-900 mb-1"><strong>File:</strong> {{ $dokumen->nama_file }}
                                    </p>
                                    <p class="text-sm text-gray-600 mb-1"><strong>Upload:</strong>
                                        {{ $dokumen->tanggal_upload->format('d/m/Y H:i') }}</p>
                                    @if ($dokumen->tanggal_verifikasi)
                                        <p class="text-sm text-gray-600"><strong>Verifikasi:</strong>
                                            {{ $dokumen->tanggal_verifikasi->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>

                                <!-- Catatan Admin -->
                                @if ($dokumen->catatan)
                                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <p class="text-sm text-blue-800"><strong>Catatan Admin:</strong></p>
                                        <p class="text-sm text-blue-700">{{ $dokumen->catatan }}</p>
                                    </div>
                                @endif

                                <!-- Actions -->
                                <div class="flex flex-wrap gap-2">
                                    <button onclick="previewDokumen('{{ $dokumen->id }}')"
                                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-eye mr-1"></i>Lihat
                                    </button>
                                    <a href="{{ route('peserta.dokumen.download', $dokumen->id) }}"
                                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>

                                    @if ($dokumen->status_verifikasi != 'disetujui')
                                        <button onclick="uploadUlang('{{ $jenis }}', '{{ $label }}')"
                                            class="px-3 py-2 text-sm bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                                            <i class="fas fa-redo mr-1"></i>Upload Ulang
                                        </button>

                                        @if ($dokumen->status_verifikasi == 'pending')
                                            <button onclick="hapusDokumen('{{ $dokumen->id }}', '{{ $label }}')"
                                                class="px-3 py-2 text-sm border border-red-300 text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                                <i class="fas fa-trash mr-1"></i>Hapus
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            @else
                                <!-- Upload Form -->
                                <form action="{{ route('peserta.dokumen.upload') }}" method="POST"
                                    enctype="multipart/form-data" class="upload-form">
                                    @csrf
                                    <input type="hidden" name="jenis_dokumen" value="{{ $jenis }}">

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File</label>
                                        <input type="file" name="file_dokumen"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                                            accept=".pdf,.jpg,.jpeg,.png" required>
                                        <p class="text-xs text-gray-500 mt-1">Format: PDF, JPG, PNG. Maksimal 2MB</p>
                                    </div>

                                    <button type="submit"
                                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-upload mr-2"></i>Upload {{ $label }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Upload Multiple Files -->
            @if ($statistikDokumen['total'] < count($dokumenWajib))
                <div class="mt-8">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-2">
                            <i class="fas fa-upload mr-2"></i>Upload Multiple Files
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Upload beberapa dokumen sekaligus untuk mempercepat proses.
                        </p>

                        <form action="{{ route('peserta.dokumen.uploadMultiple') }}" method="POST"
                            enctype="multipart/form-data" id="multipleUploadForm">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Files (Maksimal
                                        5)</label>
                                    <input type="file" name="dokumen_files[]"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                                        multiple accept=".pdf,.jpg,.jpeg,.png" id="multipleFiles">
                                    <p class="text-xs text-gray-500 mt-1">Ctrl+Click untuk pilih multiple files</p>
                                </div>
                                <div class="flex items-end">
                                    <button type="button" onclick="showMultipleUploadModal()"
                                        class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-upload mr-2"></i>Upload Multiple
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Preview Dokumen -->
    <div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-screen overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Preview Dokumen</h3>
                <button onclick="closePreviewModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 max-h-96 overflow-auto">
                <div id="previewContent" class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Upload Ulang -->
    <div id="uploadUlangModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Upload Ulang Dokumen</h3>
            </div>
            <form action="{{ route('peserta.dokumen.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6">
                    <input type="hidden" name="jenis_dokumen" id="jenisUploadUlang">

                    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                            <span class="text-sm text-yellow-700">File lama akan diganti dengan file baru yang Anda
                                upload.</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Dokumen</label>
                        <input type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50"
                            id="labelUploadUlang" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Baru</label>
                        <input type="file" name="file_dokumen"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                            accept=".pdf,.jpg,.jpeg,.png" required>
                        <p class="text-xs text-gray-500 mt-1">Format: PDF, JPG, PNG. Maksimal 2MB</p>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeUploadUlangModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-upload mr-1"></i>Upload Ulang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Multiple Upload -->
    <div id="multipleUploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Upload Multiple Dokumen</h3>
            </div>
            <form action="{{ route('peserta.dokumen.uploadMultiple') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 max-h-96 overflow-auto">
                    <div id="fileAssignments"></div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeMultipleUploadModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-upload mr-1"></i>Upload Semua
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function previewDokumen(id) {
            const modal = document.getElementById('previewModal');
            const content = document.getElementById('previewContent');

            content.innerHTML =
            '<div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>';

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Load preview content
            fetch(`/peserta/dokumen/${id}/preview`)
                .then(response => {
                    if (response.ok) {
                        return response.blob();
                    }
                    throw new Error('Preview not available');
                })
                .then(blob => {
                    const url = URL.createObjectURL(blob);
                    const mimeType = blob.type;

                    if (mimeType.includes('pdf')) {
                        content.innerHTML =
                            `<embed src="${url}" type="application/pdf" width="100%" height="500px" class="rounded-lg">`;
                    } else if (mimeType.includes('image')) {
                        content.innerHTML =
                            `<img src="${url}" class="max-w-full h-auto rounded-lg" alt="Document Preview">`;
                    } else {
                        content.innerHTML = '<p class="text-gray-500">Preview tidak tersedia untuk file ini.</p>';
                    }
                })
                .catch(error => {
                    content.innerHTML = '<p class="text-red-500">Preview tidak dapat dimuat.</p>';
                });
        }

        function closePreviewModal() {
            const modal = document.getElementById('previewModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function uploadUlang(jenis, label) {
            document.getElementById('jenisUploadUlang').value = jenis;
            document.getElementById('labelUploadUlang').value = label;

            const modal = document.getElementById('uploadUlangModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeUploadUlangModal() {
            const modal = document.getElementById('uploadUlangModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function hapusDokumen(id, label) {
            if (confirm(`Hapus dokumen "${label}"? Tindakan ini tidak dapat dibatalkan.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/peserta/dokumen/${id}`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function showMultipleUploadModal() {
            const files = document.getElementById('multipleFiles').files;

            if (files.length === 0) {
                alert('Pilih file yang akan diupload');
                return;
            }

            if (files.length > 5) {
                alert('Maksimal 5 file dapat diupload sekaligus');
                return;
            }

            const jenisOptions = @json($dokumenWajib);
            const assignmentDiv = document.getElementById('fileAssignments');

            let html =
                '<div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg"><p class="text-sm text-blue-700">Tentukan jenis dokumen untuk setiap file:</p></div>';

            for (let i = 0; i < files.length; i++) {
                html += `
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg mb-3">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">${files[i].name}</p>
                            <p class="text-sm text-gray-500">${(files[i].size / 1024).toFixed(1)} KB</p>
                        </div>
                        <div class="ml-4 w-48">
                            <select name="jenis_dokumen[]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Pilih Jenis Dokumen</option>`;

                Object.entries(jenisOptions).forEach(([key, value]) => {
                    html += `<option value="${key}">${value}</option>`;
                });

                html += `
                            </select>
                        </div>
                    </div>
                `;
            }

            html += '<input type="file" name="dokumen_files[]" style="display:none" multiple>';
            assignmentDiv.innerHTML = html;

            // Copy files to hidden input
            const hiddenInput = assignmentDiv.querySelector('input[type="file"]');
            hiddenInput.files = files;

            const modal = document.getElementById('multipleUploadModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeMultipleUploadModal() {
            const modal = document.getElementById('multipleUploadModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            const previewModal = document.getElementById('previewModal');
            const uploadUlangModal = document.getElementById('uploadUlangModal');
            const multipleUploadModal = document.getElementById('multipleUploadModal');

            if (e.target === previewModal) closePreviewModal();
            if (e.target === uploadUlangModal) closeUploadUlangModal();
            if (e.target === multipleUploadModal) closeMultipleUploadModal();
        });

        // Auto-submit upload forms with loading state
        document.querySelectorAll('.upload-form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
                btn.disabled = true;
            });
        });
    </script>
@endpush
