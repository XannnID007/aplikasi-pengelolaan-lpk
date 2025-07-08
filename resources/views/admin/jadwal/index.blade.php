@extends('layouts.admin')

@section('title', 'Jadwal Keberangkatan')
@section('page-title', 'Jadwal Keberangkatan')

@section('content')
    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="stat-number text-primary">{{ $statistik['total'] }}</div>
                    <div class="stat-label">Total Jadwal</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="stat-number text-success">{{ $statistik['aktif'] }}</div>
                    <div class="stat-label">Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="stat-number text-warning">{{ $statistik['penuh'] }}</div>
                    <div class="stat-label">Penuh</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="stat-number text-info">{{ $statistik['selesai'] }}</div>
                    <div class="stat-label">Selesai</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="stat-number text-secondary">{{ $statistik['total_kapasitas'] }}</div>
                    <div class="stat-label">Total Kapasitas</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="stat-number text-primary">{{ $statistik['total_terjadwal'] }}</div>
                    <div class="stat-label">Total Terjadwal</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter dan Actions -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <form method="GET" action="{{ route('admin.jadwal') }}">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif
                                    </option>
                                    <option value="penuh" {{ request('status') == 'penuh' ? 'selected' : '' }}>Penuh
                                    </option>
                                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai
                                    </option>
                                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>
                                        Dibatalkan</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Bulan</label>
                                <select name="bulan" class="form-control">
                                    <option value="">Semua Bulan</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}"
                                            {{ request('bulan') == $i ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Tahun</label>
                                <select name="tahun" class="form-control">
                                    <option value="">Semua Tahun</option>
                                    @for ($year = date('Y'); $year <= date('Y') + 2; $year++)
                                        <option value="{{ $year }}"
                                            {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Kategori</label>
                                <input type="text" name="kategori" class="form-control"
                                    placeholder="Kategori pekerjaan..." value="{{ request('kategori') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <a href="{{ route('admin.jadwal') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 text-end">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <a href="{{ route('admin.jadwal.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Buat Jadwal Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Jadwal -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-calendar-alt me-2"></i>Daftar Jadwal Keberangkatan ({{ $jadwal->total() }})</h5>
        </div>
        <div class="card-body">
            @if ($jadwal->count() > 0)
                <div class="row">
                    @foreach ($jadwal as $j)
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <!-- Header dengan status -->
                                <div class="card-header border-0 pb-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            @switch($j->status)
                                                @case('aktif')
                                                    <span class="badge bg-success">Aktif</span>
                                                @break

                                                @case('penuh')
                                                    <span class="badge bg-warning">Penuh</span>
                                                @break

                                                @case('selesai')
                                                    <span class="badge bg-info">Selesai</span>
                                                @break

                                                @case('dibatalkan')
                                                    <span class="badge bg-danger">Dibatalkan</span>
                                                @break
                                            @endswitch
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item"
                                                        href="{{ route('admin.jadwal.show', $j->id) }}">
                                                        <i class="fas fa-eye me-2"></i>Lihat Detail
                                                    </a></li>
                                                <li><a class="dropdown-item"
                                                        href="{{ route('admin.jadwal.edit', $j->id) }}">
                                                        <i class="fas fa-edit me-2"></i>Edit
                                                    </a></li>
                                                @if ($j->status != 'selesai')
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li><a class="dropdown-item text-warning" href="#"
                                                            onclick="selesaikanJadwal({{ $j->id }})">
                                                            <i class="fas fa-check me-2"></i>Tandai Selesai
                                                        </a></li>
                                                @endif
                                                @if ($j->jumlah_peserta == 0)
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li><a class="dropdown-item text-danger" href="#"
                                                            onclick="hapusJadwal({{ $j->id }}, '{{ $j->nama_batch }}')">
                                                            <i class="fas fa-trash me-2"></i>Hapus
                                                        </a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Body -->
                                <div class="card-body">
                                    <h6 class="card-title text-primary">{{ $j->nama_batch }}</h6>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $j->tujuan_kota }}
                                    </p>
                                    <p class="text-muted small mb-3">
                                        <i class="fas fa-briefcase me-1"></i>{{ $j->kategori_pekerjaan }}
                                    </p>

                                    <!-- Tanggal Keberangkatan -->
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar text-primary me-2"></i>
                                            <div>
                                                <div class="fw-bold">{{ $j->tanggal_keberangkatan->format('d F Y') }}
                                                </div>
                                                <small
                                                    class="text-muted">{{ $j->tanggal_keberangkatan->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Progress Kapasitas -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">Kapasitas</small>
                                            <small
                                                class="fw-bold">{{ $j->jumlah_peserta }}/{{ $j->kapasitas_maksimal }}</small>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            @php
                                                $percentage =
                                                    $j->kapasitas_maksimal > 0
                                                        ? ($j->jumlah_peserta / $j->kapasitas_maksimal) * 100
                                                        : 0;
                                            @endphp
                                            <div class="progress-bar 
                                        @if ($percentage >= 100) bg-danger 
                                        @elseif($percentage >= 80) bg-warning 
                                        @else bg-success @endif"
                                                style="width: {{ $percentage }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ number_format($percentage, 1) }}% terisi</small>
                                    </div>

                                    @if ($j->deskripsi)
                                        <p class="text-muted small">{{ Str::limit($j->deskripsi, 80) }}</p>
                                    @endif
                                </div>

                                <!-- Footer -->
                                <div class="card-footer border-0 pt-0">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.jadwal.show', $j->id) }}"
                                            class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </a>
                                        @if ($j->status == 'aktif')
                                            <a href="{{ route('admin.jadwal.edit', $j->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $jadwal->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">Belum ada jadwal keberangkatan</h5>
                    <p class="text-muted">Mulai dengan membuat jadwal keberangkatan pertama.</p>
                    <a href="{{ route('admin.jadwal.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Buat Jadwal Baru
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function selesaikanJadwal(id) {
            if (confirm('Tandai jadwal ini sebagai selesai? Semua peserta akan diubah statusnya menjadi "Berangkat".')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/jadwal/${id}/selesaikan`;
                form.innerHTML = `
            @csrf
            @method('PUT')
        `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function hapusJadwal(id, nama) {
            if (confirm(`Hapus jadwal "${nama}"? Tindakan ini tidak dapat dibatalkan.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/jadwal/${id}`;
                form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Auto refresh setiap 60 detik
        setInterval(function() {
            // Only refresh if no modals are open
            if (!document.querySelector('.modal.show')) {
                location.reload();
            }
        }, 60000);
    </script>
@endpush
