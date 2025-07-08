@extends('layouts.peserta')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
    <div class="row">
        <!-- Profile Summary -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar bg-primary text-white rounded-circle mx-auto mb-3"
                        style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem;">
                        {{ strtoupper(substr($user->nama, 0, 1)) }}
                    </div>
                    <h4>{{ $user->nama }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>

                    <!-- Status Badge -->
                    <div class="mb-3">
                        @switch($user->status)
                            @case('pending')
                                <span class="badge status-pending fs-6">Menunggu Verifikasi</span>
                            @break

                            @case('terverifikasi')
                                <span class="badge status-terverifikasi fs-6">Terverifikasi</span>
                            @break

                            @case('terjadwal')
                                <span class="badge status-terjadwal fs-6">Terjadwal</span>
                            @break

                            @case('berangkat')
                                <span class="badge status-terverifikasi fs-6">Sudah Berangkat</span>
                            @break

                            @case('ditolak')
                                <span class="badge status-ditolak fs-6">Ditolak</span>
                            @break
                        @endswitch
                    </div>

                    <!-- Kelengkapan Profil -->
                    <div class="mb-3">
                        <h6>Kelengkapan Profil</h6>
                        <div class="progress mb-2" style="height: 12px;">
                            <div class="progress-bar" style="width: {{ $kelengkapanProfil }}%"></div>
                        </div>
                        <small class="text-muted">{{ $kelengkapanProfil }}% lengkap</small>
                    </div>

                    <!-- Skor Prioritas -->
                    <div class="mb-4">
                        <h6>Skor Prioritas</h6>
                        <div class="h3 text-primary">{{ number_format($user->skor_prioritas, 1) }}</div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="width: {{ min(100, $user->skor_prioritas) }}%"></div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="d-grid">
                        <a href="{{ route('peserta.profil.edit') }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="col-lg-8 mb-4">
            <!-- Progress Alert -->
            @if ($kelengkapanProfil < 100)
                <div class="alert alert-warning mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-3 fa-2x"></i>
                        <div>
                            <h6 class="alert-heading mb-1">Profil Belum Lengkap</h6>
                            <p class="mb-2">Lengkapi profil Anda untuk meningkatkan skor prioritas dan peluang mendapat
                                jadwal keberangkatan.</p>
                            @if (count($fieldKosong) > 0)
                                <small><strong>Yang belum diisi:</strong> {{ implode(', ', $fieldKosong) }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Data Pribadi -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-user me-2"></i>Data Pribadi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Nama Lengkap</label>
                            <p class="mb-0">{{ $user->nama ?: '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Email</label>
                            <p class="mb-0">{{ $user->email ?: '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Nomor Telepon</label>
                            <p class="mb-0">{{ $user->telepon ?: '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Tanggal Lahir</label>
                            <p class="mb-0">
                                @if ($user->tanggal_lahir)
                                    {{ $user->tanggal_lahir->format('d F Y') }}
                                    <small class="text-muted">({{ $user->tanggal_lahir->age }} tahun)</small>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Jenis Kelamin</label>
                            <p class="mb-0">
                                @if ($user->jenis_kelamin)
                                    {{ $user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Bergabung Sejak</label>
                            <p class="mb-0">{{ $user->created_at->format('d F Y') }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted">Alamat</label>
                            <p class="mb-0" style="white-space: pre-line;">{{ $user->alamat ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Pendidikan & Keahlian -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-graduation-cap me-2"></i>Pendidikan & Keahlian</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Pendidikan Terakhir</label>
                            <p class="mb-0">{{ $user->pendidikan_terakhir ?: '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Pekerjaan yang Diinginkan</label>
                            <p class="mb-0">{{ $user->pekerjaan_diinginkan ?: '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Level Bahasa Jepang</label>
                            <p class="mb-0">
                                @if ($user->level_bahasa_jepang)
                                    <span class="badge bg-info">{{ $user->level_bahasa_jepang }}</span>
                                    @switch($user->level_bahasa_jepang)
                                        @case('N5')
                                            <small class="text-muted d-block">Pemula</small>
                                        @break

                                        @case('N4')
                                            <small class="text-muted d-block">Dasar</small>
                                        @break

                                        @case('N3')
                                            <small class="text-muted d-block">Menengah Bawah</small>
                                        @break

                                        @case('N2')
                                            <small class="text-muted d-block">Menengah Atas</small>
                                        @break

                                        @case('N1')
                                            <small class="text-muted d-block">Mahir</small>
                                        @break
                                    @endswitch
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Skor Bahasa Jepang</label>
                            <p class="mb-0">
                                @if ($user->skor_bahasa_jepang)
                                    <span class="h5 text-primary">{{ $user->skor_bahasa_jepang }}</span>
                                    <small class="text-muted">/100</small>
                                    <div class="progress mt-1" style="height: 6px;">
                                        <div class="progress-bar" style="width: {{ $user->skor_bahasa_jepang }}%"></div>
                                    </div>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted">Pengalaman Kerja</label>
                            <p class="mb-0" style="white-space: pre-line;">{{ $user->pengalaman_kerja ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tips Meningkatkan Skor -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-lightbulb me-2"></i>Tips Meningkatkan Skor Prioritas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Kelengkapan Data (40%)</h6>
                            <ul class="list-unstyled">
                                <li class="mb-1">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Lengkapi semua field profil
                                </li>
                                <li class="mb-1">
                                    <i class="fas fa-file-upload text-info me-2"></i>
                                    Upload semua dokumen wajib
                                </li>
                                <li class="mb-1">
                                    <i class="fas fa-shield-alt text-warning me-2"></i>
                                    Pastikan dokumen diverifikasi
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Kemampuan Bahasa (30%)</h6>
                            <ul class="list-unstyled">
                                <li class="mb-1">
                                    <i class="fas fa-certificate text-info me-2"></i>
                                    Tingkatkan level bahasa Jepang
                                </li>
                                <li class="mb-1">
                                    <i class="fas fa-star text-warning me-2"></i>
                                    Raih skor tes tinggi (>80)
                                </li>
                            </ul>

                            <h6 class="text-primary mt-3">Pengalaman Kerja (20%)</h6>
                            <ul class="list-unstyled">
                                <li class="mb-1">
                                    <i class="fas fa-briefcase text-success me-2"></i>
                                    Deskripsikan pengalaman kerja detail
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Skor Anda saat ini: {{ number_format($user->skor_prioritas, 1) }}</strong><br>
                        Skor akan otomatis diperbarui setiap kali Anda melengkapi profil atau dokumen diverifikasi.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
