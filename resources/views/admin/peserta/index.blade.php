@extends('layouts.admin')

@section('title', 'Kelola Peserta')
@section('page-title', 'Kelola Peserta')

@section('content')
    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="stat-number text-primary">{{ $statistik['total'] }}</div>
                    <div class="stat-label">Total Peserta</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="stat-number text-warning">{{ $statistik['pending'] }}</div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="stat-number text-success">{{ $statistik['terverifikasi'] }}</div>
                    <div class="stat-label">Terverifikasi</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="stat-number text-info">{{ $statistik['terjadwal'] }}</div>
                    <div class="stat-label">Terjadwal</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="stat-number text-primary">{{ $statistik['berangkat'] }}</div>
                    <div class="stat-label">Berangkat</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="stat-number text-danger">{{ $statistik['ditolak'] }}</div>
                    <div class="stat-label">Ditolak</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter dan Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.peserta') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Cari Peserta</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama atau email..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="terverifikasi" {{ request('status') == 'terverifikasi' ? 'selected' : '' }}>
                                Terverifikasi</option>
                            <option value="terjadwal" {{ request('status') == 'terjadwal' ? 'selected' : '' }}>Terjadwal
                            </option>
                            <option value="berangkat" {{ request('status') == 'berangkat' ? 'selected' : '' }}>Berangkat
                            </option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Level Bahasa</label>
                        <select name="level_bahasa" class="form-control">
                            <option value="">Semua Level</option>
                            <option value="N5" {{ request('level_bahasa') == 'N5' ? 'selected' : '' }}>N5</option>
                            <option value="N4" {{ request('level_bahasa') == 'N4' ? 'selected' : '' }}>N4</option>
                            <option value="N3" {{ request('level_bahasa') == 'N3' ? 'selected' : '' }}>N3</option>
                            <option value="N2" {{ request('level_bahasa') == 'N2' ? 'selected' : '' }}>N2</option>
                            <option value="N1" {{ request('level_bahasa') == 'N1' ? 'selected' : '' }}>N1</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Cari
                            </button>
                            <a href="{{ route('admin.peserta') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#exportModal">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar Peserta -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-users me-2"></i>Daftar Peserta ({{ $peserta->total() }})</h5>
        </div>
        <div class="card-body">
            @if ($peserta->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Peserta</th>
                                <th>Kontak</th>
                                <th>Pendidikan</th>
                                <th>Bahasa Jepang</th>
                                <th>Skor Prioritas</th>
                                <th>Status</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($peserta as $p)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-primary text-white rounded-circle me-3"
                                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                {{ strtoupper(substr($p->nama, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $p->nama }}</div>
                                                <small
                                                    class="text-muted">{{ $p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $p->email }}</div>
                                        <small class="text-muted">{{ $p->telepon }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $p->pendidikan_terakhir ?: '-' }}</div>
                                        <small class="text-muted">{{ $p->pekerjaan_diinginkan ?: '-' }}</small>
                                    </td>
                                    <td>
                                        @if ($p->level_bahasa_jepang)
                                            <span class="badge bg-info">{{ $p->level_bahasa_jepang }}</span>
                                            <div><small>Skor: {{ $p->skor_bahasa_jepang ?: '-' }}</small></div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary">{{ number_format($p->skor_prioritas, 1) }}</div>
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar" style="width: {{ min(100, $p->skor_prioritas) }}%">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @switch($p->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @break

                                            @case('terverifikasi')
                                                <span class="badge bg-success">Terverifikasi</span>
                                            @break

                                            @case('terjadwal')
                                                <span class="badge bg-info">Terjadwal</span>
                                            @break

                                            @case('berangkat')
                                                <span class="badge bg-primary">Berangkat</span>
                                            @break

                                            @case('ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td>{{ $p->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item"
                                                        href="{{ route('admin.peserta.show', $p->id) }}">
                                                        <i class="fas fa-eye me-2"></i>Lihat Detail
                                                    </a></li>
                                                <li><a class="dropdown-item"
                                                        href="{{ route('admin.peserta.edit', $p->id) }}">
                                                        <i class="fas fa-edit me-2"></i>Edit
                                                    </a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item text-danger" href="#"
                                                        onclick="confirmDelete('{{ $p->id }}', '{{ $p->nama }}')">
                                                        <i class="fas fa-trash me-2"></i>Hapus
                                                    </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $peserta->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">Tidak ada peserta ditemukan</h5>
                    <p class="text-muted">Coba ubah filter pencarian atau kriteria lainnya.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Export -->
    <div class="modal fade" id="exportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Data Peserta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.laporan.export', 'peserta-excel') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label">Format Export</label>
                            <select name="format" class="form-control" required>
                                <option value="excel">Excel (.xlsx)</option>
                                <option value="csv">CSV (.csv)</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Dari</label>
                                <input type="date" name="tanggal_dari" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Sampai</label>
                                <input type="date" name="tanggal_sampai" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="terverifikasi">Terverifikasi</option>
                                <option value="terjadwal">Terjadwal</option>
                                <option value="berangkat">Berangkat</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(id, nama) {
            if (confirm(`Apakah Anda yakin ingin menghapus peserta "${nama}"? Tindakan ini tidak dapat dibatalkan.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/peserta/${id}`;
                form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endpush
