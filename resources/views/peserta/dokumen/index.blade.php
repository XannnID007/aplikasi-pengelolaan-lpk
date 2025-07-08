@extends('layouts.peserta')

@section('title', 'Dokumen Saya')
@section('page-title', 'Dokumen Saya')

@section('content')
    <!-- Progress Overview -->
    <div class="progress-card mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-2">Progress Upload Dokumen</h5>
                <div class="progress mb-2" style="height: 12px;">
                    <div class="progress-bar" style="width: {{ $progressDokumen }}%"></div>
                </div>
                <p class="mb-0 text-muted">{{ $statistikDokumen['disetujui'] }} dari {{ count($dokumenWajib) }} dokumen sudah
                    disetujui ({{ $progressDokumen }}%)</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="h2 text-primary mb-1">{{ $statistikDokumen['disetujui'] }}/{{ count($dokumenWajib) }}</div>
                <small class="text-muted">Dokumen Lengkap</small>
            </div>
        </div>
    </div>

    <!-- Statistik Dokumen -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-primary">{{ $statistikDokumen['total'] }}</div>
                        <div class="stat-label">Total Upload</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-success">{{ $statistikDokumen['disetujui'] }}</div>
                        <div class="stat-label">Disetujui</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-warning">{{ $statistikDokumen['pending'] }}</div>
                        <div class="stat-label">Menunggu</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-danger">{{ $statistikDokumen['ditolak'] }}</div>
                        <div class="stat-label">Ditolak</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Dokumen Wajib -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list-check me-2"></i>Dokumen Wajib</h5>
        </div>
        <div class="card-body">
            @if ($progressDokumen >= 100)
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Selamat!</strong> Semua dokumen wajib sudah lengkap dan disetujui. Status Anda akan segera
                    diperbarui.
                </div>
            @endif

            <div class="row">
                @foreach ($dokumenWajib as $jenis => $label)
                    @php
                        $dokumen = $dokumenByJenis->get($jenis);
                    @endphp
                    <div class="col-lg-6 mb-4">
                        <div
                            class="card h-100 
                        @if ($dokumen && $dokumen->status_verifikasi == 'disetujui') border-success
                        @elseif($dokumen && $dokumen->status_verifikasi == 'pending') border-warning  
                        @elseif($dokumen && $dokumen->status_verifikasi == 'ditolak') border-danger
                        @else border-secondary @endif">

                            <div
                                class="card-header d-flex justify-content-between align-items-center
                            @if ($dokumen && $dokumen->status_verifikasi == 'disetujui') bg-light-success
                            @elseif($dokumen && $dokumen->status_verifikasi == 'pending') bg-light-warning
                            @elseif($dokumen && $dokumen->status_verifikasi == 'ditolak') bg-light-danger @endif">

                                <h6 class="mb-0">{{ $label }}</h6>

                                @if ($dokumen)
                                    @switch($dokumen->status_verifikasi)
                                        @case('disetujui')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Disetujui
                                            </span>
                                        @break

                                        @case('pending')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Menunggu
                                            </span>
                                        @break

                                        @case('ditolak')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>Ditolak
                                            </span>
                                        @break
                                    @endswitch
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-upload me-1"></i>Belum Upload
                                    </span>
                                @endif
                            </div>

                            <div class="card-body">
                                @if ($dokumen)
                                    <!-- Info Dokumen -->
                                    <div class="mb-3">
                                        <p class="mb-1"><strong>File:</strong> {{ $dokumen->nama_file }}</p>
                                        <p class="mb-1"><strong>Upload:</strong>
                                            {{ $dokumen->tanggal_upload->format('d/m/Y H:i') }}</p>
                                        @if ($dokumen->tanggal_verifikasi)
                                            <p class="mb-1"><strong>Verifikasi:</strong>
                                                {{ $dokumen->tanggal_verifikasi->format('d/m/Y H:i') }}</p>
                                        @endif
                                    </div>

                                    <!-- Catatan Admin -->
                                    @if ($dokumen->catatan)
                                        <div class="alert alert-info small">
                                            <strong>Catatan Admin:</strong><br>
                                            {{ $dokumen->catatan }}
                                        </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="previewDokumen('{{ $dokumen->id }}')">
                                            <i class="fas fa-eye me-1"></i>Lihat
                                        </button>
                                        <a href="{{ route('peserta.dokumen.download', $dokumen->id) }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>

                                        @if ($dokumen->status_verifikasi != 'disetujui')
                                            <button class="btn btn-sm btn-warning"
                                                onclick="uploadUlang('{{ $jenis }}', '{{ $label }}')">
                                                <i class="fas fa-redo me-1"></i>Upload Ulang
                                            </button>

                                            @if ($dokumen->status_verifikasi == 'pending')
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="hapusDokumen('{{ $dokumen->id }}', '{{ $label }}')">
                                                    <i class="fas fa-trash me-1"></i>Hapus
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

                                        <div class="mb-3">
                                            <label class="form-label">Pilih File</label>
                                            <input type="file" name="file_dokumen" class="form-control"
                                                accept=".pdf,.jpg,.jpeg,.png" required>
                                            <small class="text-muted">Format: PDF, JPG, PNG. Maksimal 2MB</small>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-upload me-1"></i>Upload {{ $label }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Upload Multiple Files -->
            @if ($statistikDokumen['total'] < count($dokumenWajib))
                <div class="mt-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6><i class="fas fa-upload me-2"></i>Upload Multiple Files</h6>
                            <p class="text-muted small">Upload beberapa dokumen sekaligus untuk mempercepat proses.</p>

                            <form action="{{ route('peserta.dokumen.uploadMultiple') }}" method="POST"
                                enctype="multipart/form-data" id="multipleUploadForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <label class="form-label">Pilih Files (Maksimal 5)</label>
                                        <input type="file" name="dokumen_files[]" class="form-control" multiple
                                            accept=".pdf,.jpg,.jpeg,.png" id="multipleFiles">
                                        <small class="text-muted">Ctrl+Click untuk pilih multiple files</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-success w-100"
                                            onclick="showMultipleUploadModal()">
                                            <i class="fas fa-upload me-1"></i>Upload Multiple
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Preview Dokumen -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="previewContent" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Upload Ulang -->
    <div class="modal fade" id="uploadUlangModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Ulang Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('peserta.dokumen.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="jenis_dokumen" id="jenisUploadUlang">

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            File lama akan diganti dengan file baru yang Anda upload.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Dokumen</label>
                            <input type="text" class="form-control" id="labelUploadUlang" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pilih File Baru</label>
                            <input type="file" name="file_dokumen" class="form-control" accept=".pdf,.jpg,.jpeg,.png"
                                required>
                            <small class="text-muted">Format: PDF, JPG, PNG. Maksimal 2MB</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>Upload Ulang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Multiple Upload -->
    <div class="modal fade" id="multipleUploadModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Multiple Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('peserta.dokumen.uploadMultiple') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div id="fileAssignments"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>Upload Semua
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function previewDokumen(id) {
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            const content = document.getElementById('previewContent');

            content.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    `;

            modal.show();

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
                        content.innerHTML = `<embed src="${url}" type="application/pdf" width="100%" height="500px">`;
                    } else if (mimeType.includes('image')) {
                        content.innerHTML = `<img src="${url}" class="img-fluid" alt="Document Preview">`;
                    } else {
                        content.innerHTML = '<p class="text-muted">Preview tidak tersedia untuk file ini.</p>';
                    }
                })
                .catch(error => {
                    content.innerHTML = '<p class="text-danger">Preview tidak dapat dimuat.</p>';
                });
        }

        function uploadUlang(jenis, label) {
            document.getElementById('jenisUploadUlang').value = jenis;
            document.getElementById('labelUploadUlang').value = label;

            const modal = new bootstrap.Modal(document.getElementById('uploadUlangModal'));
            modal.show();
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

            let html = '<div class="alert alert-info">Tentukan jenis dokumen untuk setiap file:</div>';

            for (let i = 0; i < files.length; i++) {
                html += `
            <div class="row mb-3 align-items-center">
                <div class="col-md-6">
                    <strong>${files[i].name}</strong>
                    <small class="text-muted d-block">${(files[i].size / 1024).toFixed(1)} KB</small>
                </div>
                <div class="col-md-6">
                    <select name="jenis_dokumen[]" class="form-control" required>
                        <option value="">Pilih Jenis Dokumen</option>`;

                Object.entries(jenisOptions).forEach(([key, value]) => {
                    html += `<option value="${key}">${value}</option>`;
                });

                html += `
                    </select>
                    <input type="file" name="dokumen_files[]" style="display:none" multiple>
                </div>
            </div>
        `;
            }

            assignmentDiv.innerHTML = html;

            // Copy files to hidden input
            const hiddenInput = assignmentDiv.querySelector('input[type="file"]');
            hiddenInput.files = files;

            const modal = new bootstrap.Modal(document.getElementById('multipleUploadModal'));
            modal.show();
        }

        // Auto-submit upload forms with loading state
        document.querySelectorAll('.upload-form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Uploading...';
                btn.disabled = true;
            });
        });
    </script>
@endpush
