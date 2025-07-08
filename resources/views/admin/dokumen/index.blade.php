@extends('layouts.admin')

@section('title', 'Verifikasi Dokumen')
@section('page-title', 'Verifikasi Dokumen')

@section('content')
    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-primary">{{ $statistik['total'] }}</div>
                        <div class="stat-label">Total Dokumen</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-file-alt text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-warning">{{ $statistik['pending'] }}</div>
                        <div class="stat-label">Menunggu Verifikasi</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-success">{{ $statistik['disetujui'] }}</div>
                        <div class="stat-label">Disetujui</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-danger">{{ $statistik['ditolak'] }}</div>
                        <div class="stat-label">Ditolak</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-times-circle text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter dan Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.dokumen') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Cari Peserta</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama peserta..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui
                            </option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Jenis Dokumen</label>
                        <select name="jenis" class="form-control">
                            <option value="">Semua Jenis</option>
                            @foreach ($jenisDokumen as $key => $label)
                                <option value="{{ $key }}" {{ request('jenis') == $key ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Cari
                            </button>
                            <a href="{{ route('admin.dokumen') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#batchModal">
                                <i class="fas fa-check-double me-1"></i>Batch
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar Dokumen -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-file-alt me-2"></i>Daftar Dokumen ({{ $dokumen->total() }})</h5>
        </div>
        <div class="card-body">
            @if ($dokumen->count() > 0)
                <!-- Bulk Actions -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <input type="checkbox" id="selectAll" class="form-check-input me-2">
                        <label for="selectAll" class="form-check-label">Pilih Semua</label>
                    </div>
                    <div id="bulkActions" style="display: none;">
                        <button class="btn btn-sm btn-success me-2" onclick="bulkApprove()">
                            <i class="fas fa-check me-1"></i>Setujui Terpilih
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="bulkReject()">
                            <i class="fas fa-times me-1"></i>Tolak Terpilih
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" class="form-check-input" id="masterCheck">
                                </th>
                                <th>Peserta</th>
                                <th>Jenis Dokumen</th>
                                <th>File</th>
                                <th>Status</th>
                                <th>Tanggal Upload</th>
                                <th>Verifikasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dokumen as $doc)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input document-check"
                                            value="{{ $doc->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-primary text-white rounded-circle me-3"
                                                style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                                {{ strtoupper(substr($doc->user->nama, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $doc->user->nama }}</div>
                                                <small class="text-muted">{{ $doc->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-info">{{ $jenisDokumen[$doc->jenis_dokumen] ?? $doc->jenis_dokumen }}</span>
                                    </td>
                                    <td>
                                        <div>{{ $doc->nama_file }}</div>
                                        <small
                                            class="text-muted">{{ number_format(Storage::disk('public')->size($doc->file_path) / 1024, 1) }}
                                            KB</small>
                                    </td>
                                    <td>
                                        @switch($doc->status_verifikasi)
                                            @case('pending')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @break

                                            @case('disetujui')
                                                <span class="badge bg-success">Disetujui</span>
                                            @break

                                            @case('ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td>{{ $doc->tanggal_upload->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if ($doc->tanggal_verifikasi)
                                            <div>{{ $doc->tanggal_verifikasi->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $doc->verifiedBy?->nama }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.dokumen.show', $doc->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if ($doc->status_verifikasi == 'pending')
                                                <button class="btn btn-sm btn-success"
                                                    onclick="verifikasiDokumen({{ $doc->id }}, 'disetujui')"
                                                    title="Setujui">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="verifikasiDokumen({{ $doc->id }}, 'ditolak')"
                                                    title="Tolak">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $dokumen->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-times text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">Tidak ada dokumen ditemukan</h5>
                    <p class="text-muted">Coba ubah filter pencarian atau kriteria lainnya.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Verifikasi -->
    <div class="modal fade" id="verifikasiModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="verifikasiForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Status Verifikasi</label>
                            <select name="status_verifikasi" id="statusVerifikasi" class="form-control" required>
                                <option value="disetujui">Disetujui</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Berikan catatan verifikasi..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Verifikasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Batch Verifikasi -->
    <div class="modal fade" id="batchModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.dokumen.batchVerifikasi') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Verifikasi semua dokumen yang dipilih dengan status yang sama.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status Verifikasi</label>
                            <select name="status_verifikasi" class="form-control" required>
                                <option value="disetujui">Setujui Semua</option>
                                <option value="ditolak">Tolak Semua</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan untuk semua dokumen..."></textarea>
                        </div>
                        <input type="hidden" name="dokumen_ids" id="selectedDokumenIds">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Proses Batch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Checkbox functionality
        document.getElementById('masterCheck').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.document-check');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBulkActions();
        });

        document.querySelectorAll('.document-check').forEach(checkbox => {
            checkbox.addEventListener('change', toggleBulkActions);
        });

        function toggleBulkActions() {
            const checkedBoxes = document.querySelectorAll('.document-check:checked');
            const bulkActions = document.getElementById('bulkActions');

            if (checkedBoxes.length > 0) {
                bulkActions.style.display = 'block';
            } else {
                bulkActions.style.display = 'none';
            }
        }

        function verifikasiDokumen(id, status) {
            const form = document.getElementById('verifikasiForm');
            const statusSelect = document.getElementById('statusVerifikasi');

            form.action = `/admin/dokumen/${id}/verifikasi`;
            statusSelect.value = status;

            // Set modal title based on status
            const modalTitle = document.querySelector('#verifikasiModal .modal-title');
            modalTitle.textContent = status === 'disetujui' ? 'Setujui Dokumen' : 'Tolak Dokumen';

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('verifikasiModal'));
            modal.show();
        }

        function bulkApprove() {
            const checkedBoxes = document.querySelectorAll('.document-check:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);

            if (ids.length === 0) {
                alert('Pilih dokumen yang akan disetujui');
                return;
            }

            if (confirm(`Setujui ${ids.length} dokumen yang dipilih?`)) {
                processBatch(ids, 'disetujui');
            }
        }

        function bulkReject() {
            const checkedBoxes = document.querySelectorAll('.document-check:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);

            if (ids.length === 0) {
                alert('Pilih dokumen yang akan ditolak');
                return;
            }

            // Show batch modal for rejection with note
            document.getElementById('selectedDokumenIds').value = ids.join(',');
            document.querySelector('#batchModal select[name="status_verifikasi"]').value = 'ditolak';

            const modal = new bootstrap.Modal(document.getElementById('batchModal'));
            modal.show();
        }

        function processBatch(ids, status) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.dokumen.batchVerifikasi') }}';

            form.innerHTML = `
        @csrf
        <input type="hidden" name="dokumen_ids" value="${ids.join(',')}">
        <input type="hidden" name="status_verifikasi" value="${status}">
        <input type="hidden" name="catatan" value="Verifikasi batch oleh admin">
    `;

            document.body.appendChild(form);
            form.submit();
        }

        // Auto-refresh setiap 30 detik untuk update real-time
        setInterval(function() {
            if (document.querySelector('.document-check:checked') === null) {
                location.reload();
            }
        }, 30000);
    </script>
@endpush
