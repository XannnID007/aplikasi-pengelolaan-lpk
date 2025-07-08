@extends('layouts.peserta')

@section('title', 'Jadwal Keberangkatan')
@section('page-title', 'Jadwal Keberangkatan')

@section('content')
    @if ($jadwalPeserta)
        <!-- Jadwal Saya -->
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <h5><i class="fas fa-plane-departure me-2"></i>Jadwal Keberangkatan Saya</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <h4 class="text-primary">{{ $jadwalPeserta->nama_batch }}</h4>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                    <strong>Tujuan:</strong> {{ $jadwalPeserta->tujuan_kota }}
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-briefcase text-muted me-2"></i>
                                    <strong>Kategori:</strong> {{ $jadwalPeserta->kategori_pekerjaan }}
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-star text-muted me-2"></i>
                                    <strong>Skor Anda:</strong>
                                    <span class="badge bg-success">{{ $jadwalPeserta->pivot->skor_akhir ?? 'N/A' }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <i class="fas fa-users text-muted me-2"></i>
                                    <strong>Kapasitas:</strong>
                                    {{ $jadwalPeserta->jumlah_peserta }}/{{ $jadwalPeserta->kapasitas_maksimal }}
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-calendar-check text-muted me-2"></i>
                                    <strong>Dijadwalkan:</strong>
                                    {{ $jadwalPeserta->pivot->tanggal_penempatan->format('d F Y') }}
                                </p>
                            </div>
                        </div>

                        @if ($jadwalPeserta->deskripsi)
                            <p class="text-muted">{{ $jadwalPeserta->deskripsi }}</p>
                        @endif
                    </div>
                    <div class="col-lg-4 text-center">
                        <div class="countdown-container">
                            <h6 class="text-muted">Countdown Keberangkatan</h6>
                            <div class="h3 text-primary mb-2">{{ $jadwalPeserta->tanggal_keberangkatan->format('d F Y') }}
                            </div>
                            <div class="countdown text-success"
                                data-target="{{ $jadwalPeserta->tanggal_keberangkatan->format('Y-m-d') }}">
                                <div class="countdown-item">
                                    <span class="days">00</span>
                                    <small>Hari</small>
                                </div>
                            </div>

                            @if ($jadwalPeserta->tanggal_keberangkatan->diffInDays(now()) <= 30)
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Persiapan Keberangkatan!</strong><br>
                                    Mulai siapkan dokumen dan keperluan untuk keberangkatan.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Tidak Ada Jadwal -->
        <div class="card mb-4 border-warning">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 4rem;"></i>
                <h5 class="text-muted">Belum Ada Jadwal Keberangkatan</h5>
                <p class="text-muted">
                    @switch(auth()->user()->status)
                        @case('pending')
                            Lengkapi profil dan upload dokumen untuk mendapatkan verifikasi.
                        @break

                        @case('terverifikasi')
                            Anda sudah terverifikasi! Tunggu penjadwalan dari admin atau sistem otomatis.
                        @break

                        @case('ditolak')
                            Status Anda ditolak. Hubungi admin untuk informasi lebih lanjut.
                        @break

                        @default
                            Pastikan status pendaftaran Anda sudah terverifikasi.
                    @endswitch
                </p>

                @if (auth()->user()->status == 'pending')
                    <div class="mt-3">
                        <a href="{{ route('peserta.profil') }}" class="btn btn-primary me-2">
                            <i class="fas fa-user-edit me-1"></i>Lengkapi Profil
                        </a>
                        <a href="{{ route('peserta.dokumen') }}" class="btn btn-outline-primary">
                            <i class="fas fa-file-upload me-1"></i>Upload Dokumen
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Statistik Jadwal -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-primary">{{ $statistikJadwal['total_jadwal_tersedia'] }}</div>
                        <div class="stat-label">Jadwal Tersedia</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-info">{{ $statistikJadwal['total_kapasitas'] }}</div>
                        <div class="stat-label">Total Kapasitas</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-warning">{{ $statistikJadwal['total_terisi'] }}</div>
                        <div class="stat-label">Sudah Terisi</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-success">{{ $statistikJadwal['rata_rata_terisi'] }}%</div>
                        <div class="stat-label">Rata-rata Terisi</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($rekomendasiJadwal->count() > 0)
        <!-- Rekomendasi Jadwal -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-magic me-2"></i>Rekomendasi Jadwal untuk Anda</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($rekomendasiJadwal as $jadwal)
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card h-100 border-primary shadow-sm">
                                <div class="card-header bg-light border-primary">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-primary">Rekomendasi</span>
                                        <span class="badge bg-warning">Skor: {{ $jadwal->rekomendasi_score }}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title text-primary">{{ $jadwal->nama_batch }}</h6>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $jadwal->tujuan_kota }}
                                    </p>
                                    <p class="text-muted small mb-3">
                                        <i class="fas fa-briefcase me-1"></i>{{ $jadwal->kategori_pekerjaan }}
                                    </p>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">Keberangkatan</small>
                                            <small
                                                class="fw-bold">{{ $jadwal->tanggal_keberangkatan->format('d/m/Y') }}</small>
                                        </div>
                                        <small
                                            class="text-muted">{{ $jadwal->tanggal_keberangkatan->diffForHumans() }}</small>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">Kapasitas</small>
                                            <small
                                                class="fw-bold">{{ $jadwal->jumlah_peserta }}/{{ $jadwal->kapasitas_maksimal }}</small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            @php
                                                $percentage =
                                                    ($jadwal->jumlah_peserta / $jadwal->kapasitas_maksimal) * 100;
                                            @endphp
                                            <div class="progress-bar bg-success" style="width: {{ $percentage }}%">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted"><strong>Mengapa direkomendasikan:</strong></small>
                                        <p class="small text-muted">{{ $jadwal->alasan_rekomendasi }}</p>
                                    </div>
                                </div>
                                <div class="card-footer border-0 pt-0">
                                    <button class="btn btn-outline-primary btn-sm w-100"
                                        onclick="lihatDetailJadwal({{ $jadwal->id }})">
                                        <i class="fas fa-info-circle me-1"></i>Lihat Detail
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Semua Jadwal Tersedia -->
    @if ($jadwalTersedia->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-calendar-alt me-2"></i>Semua Jadwal Tersedia</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($jadwalTersedia as $jadwal)
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span
                                            class="badge 
                                    @if ($jadwal->masihTersedia()) bg-success
                                    @else bg-warning @endif">
                                            @if ($jadwal->masihTersedia())
                                                Tersedia
                                            @else
                                                Penuh
                                            @endif
                                        </span>
                                        <small class="text-muted">{{ $jadwal->sisaKapasitas() }} tempat tersisa</small>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">{{ $jadwal->nama_batch }}</h6>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $jadwal->tujuan_kota }}
                                    </p>
                                    <p class="text-muted small mb-3">
                                        <i class="fas fa-briefcase me-1"></i>{{ $jadwal->kategori_pekerjaan }}
                                    </p>

                                    <div class="mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar text-primary me-2"></i>
                                            <div>
                                                <div class="fw-bold">{{ $jadwal->tanggal_keberangkatan->format('d F Y') }}
                                                </div>
                                                <small
                                                    class="text-muted">{{ $jadwal->tanggal_keberangkatan->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">Kapasitas</small>
                                            <small
                                                class="fw-bold">{{ $jadwal->jumlah_peserta }}/{{ $jadwal->kapasitas_maksimal }}</small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            @php
                                                $percentage =
                                                    ($jadwal->jumlah_peserta / $jadwal->kapasitas_maksimal) * 100;
                                            @endphp
                                            <div class="progress-bar 
                                        @if ($percentage >= 90) bg-danger 
                                        @elseif($percentage >= 70) bg-warning 
                                        @else bg-success @endif"
                                                style="width: {{ $percentage }}%">
                                            </div>
                                        </div>
                                    </div>

                                    @if ($jadwal->deskripsi)
                                        <p class="text-muted small">{{ Str::limit($jadwal->deskripsi, 100) }}</p>
                                    @endif
                                </div>
                                <div class="card-footer border-0 pt-0">
                                    <button class="btn btn-outline-primary btn-sm w-100"
                                        onclick="lihatDetailJadwal({{ $jadwal->id }})">
                                        <i class="fas fa-info-circle me-1"></i>Lihat Detail
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Belum Ada Jadwal Tersedia</h5>
                <p class="text-muted">Admin belum membuat jadwal keberangkatan. Silakan cek kembali nanti.</p>
            </div>
        </div>
    @endif

    <!-- Modal Detail Jadwal -->
    <div class="modal fade" id="detailJadwalModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Jadwal Keberangkatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="modalContent">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Countdown timer
        function updateCountdown() {
            const countdownElements = document.querySelectorAll('.countdown');

            countdownElements.forEach(element => {
                const targetDate = new Date(element.dataset.target + 'T00:00:00');
                const now = new Date();
                const difference = targetDate - now;

                if (difference > 0) {
                    const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));

                    const daysElement = element.querySelector('.days');
                    if (daysElement) {
                        daysElement.textContent = days.toString().padStart(2, '0');
                    }

                    // Update with hours and minutes if needed
                    if (days === 0) {
                        element.innerHTML = `
                    <div class="countdown-item">
                        <span class="hours">${hours.toString().padStart(2, '0')}</span>
                        <small>Jam</small>
                    </div>
                    <div class="countdown-item">
                        <span class="minutes">${minutes.toString().padStart(2, '0')}</span>
                        <small>Menit</small>
                    </div>
                `;
                    }
                } else {
                    element.innerHTML = '<div class="text-success"><strong>Hari Keberangkatan!</strong></div>';
                }
            });
        }

        // Update countdown every minute
        setInterval(updateCountdown, 60000);
        updateCountdown(); // Initial call

        function lihatDetailJadwal(jadwalId) {
            const modal = new bootstrap.Modal(document.getElementById('detailJadwalModal'));
            const content = document.getElementById('modalContent');

            content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

            modal.show();

            // Load jadwal detail via AJAX
            fetch(`/peserta/jadwal/${jadwalId}/detail`)
                .then(response => response.text())
                .then(html => {
                    content.innerHTML = html;
                })
                .catch(error => {
                    content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Gagal memuat detail jadwal. Silakan coba lagi.
                </div>
            `;
                });
        }

        // Auto refresh untuk update real-time
        setInterval(function() {
            // Only refresh if no modals are open
            if (!document.querySelector('.modal.show')) {
                const currentUrl = window.location.href;
                if (!currentUrl.includes('#')) {
                    location.reload();
                }
            }
        }, 120000); // Refresh every 2 minutes
    </script>

    <style>
        .countdown {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .countdown-item {
            text-align: center;
        }

        .countdown-item span {
            display: block;
            font-size: 1.5rem;
            font-weight: bold;
            color: #059669;
        }

        .countdown-item small {
            display: block;
            color: #6b7280;
            font-size: 0.75rem;
        }

        .card:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }

        .bg-light-success {
            background-color: rgba(16, 185, 129, 0.1) !important;
        }

        .bg-light-warning {
            background-color: rgba(245, 158, 11, 0.1) !important;
        }

        .bg-light-danger {
            background-color: rgba(239, 68, 68, 0.1) !important;
        }
    </style>
@endpush
