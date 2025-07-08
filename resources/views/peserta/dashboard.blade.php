@extends('layouts.peserta')

@section('title', 'Dashboard Peserta')
@section('page-title', 'Dashboard Peserta')

@section('content')
    <!-- Welcome Card -->
    <div class="welcome-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2>Selamat Datang, {{ $user->nama }}!</h2>
                <p>Selamat datang di portal LPK Jepang. Kelola pendaftaran Anda dan pantau status keberangkatan di sini.</p>
            </div>
            <div class="col-md-4 text-end">
                <i class="fas fa-torii-gate" style="font-size: 4rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>

    <!-- Status Overview -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number">{{ number_format($skorPrioritas, 1) }}</div>
                        <div class="stat-label">Skor Prioritas</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number">
                            @switch($user->status)
                                @case('pending')
                                    <span class="badge status-pending">Pending</span>
                                @break

                                @case('terverifikasi')
                                    <span class="badge status-terverifikasi">Terverifikasi</span>
                                @break

                                @case('terjadwal')
                                    <span class="badge status-terjadwal">Terjadwal</span>
                                @break

                                @case('berangkat')
                                    <span class="badge status-terverifikasi">Berangkat</span>
                                @break

                                @case('ditolak')
                                    <span class="badge status-ditolak">Ditolak</span>
                                @break
                            @endswitch
                        </div>
                        <div class="stat-label">Status Pendaftaran</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Section -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-tasks me-2"></i>Progress Pendaftaran</h5>
                </div>
                <div class="card-body">
                    <!-- Step 1: Profil -->
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                        <div class="me-3">
                            @if ($profilLengkap >= 80)
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-check"></i>
                                </div>
                            @else
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Lengkapi Profil</h6>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar" style="width: {{ $profilLengkap }}%"></div>
                            </div>
                            <small class="text-muted">{{ $profilLengkap }}% selesai</small>
                        </div>
                        <div>
                            @if ($profilLengkap < 100)
                                <a href="{{ route('peserta.profil') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Step 2: Dokumen -->
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                        <div class="me-3">
                            @if ($dokumenDisetujui >= 5)
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-check"></i>
                                </div>
                            @elseif($totalDokumen > 0)
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-file"></i>
                                </div>
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-file"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Upload Dokumen</h6>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar" style="width: {{ ($dokumenDisetujui / 5) * 100 }}%"></div>
                            </div>
                            <small class="text-muted">{{ $dokumenDisetujui }} dari 5 dokumen disetujui</small>
                        </div>
                        <div>
                            <a href="{{ route('peserta.dokumen') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-upload me-1"></i>Upload
                            </a>
                        </div>
                    </div>

                    <!-- Step 3: Verifikasi -->
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                        <div class="me-3">
                            @if ($user->status == 'terverifikasi' || $user->status == 'terjadwal' || $user->status == 'berangkat')
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-check"></i>
                                </div>
                            @elseif($dokumenPending > 0)
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-clock"></i>
                                </div>
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Verifikasi Admin</h6>
                            @if ($user->status == 'terverifikasi')
                                <small class="text-success">Status Anda telah terverifikasi</small>
                            @elseif($dokumenPending > 0)
                                <small class="text-warning">{{ $dokumenPending }} dokumen menunggu verifikasi</small>
                            @else
                                <small class="text-muted">Menunggu upload dokumen lengkap</small>
                            @endif
                        </div>
                    </div>

                    <!-- Step 4: Jadwal -->
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            @if ($jadwalKeberangkatan)
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-check"></i>
                                </div>
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-calendar"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Penjadwalan Keberangkatan</h6>
                            @if ($jadwalKeberangkatan)
                                <small class="text-success">Anda telah dijadwalkan untuk berangkat</small>
                            @else
                                <small class="text-muted">Menunggu penjadwalan dari admin</small>
                            @endif
                        </div>
                        @if ($jadwalKeberangkatan)
                            <div>
                                <a href="{{ route('peserta.jadwal') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-calendar me-1"></i>Lihat
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Keberangkatan -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-plane-departure me-2"></i>Jadwal Keberangkatan</h5>
                </div>
                <div class="card-body">
                    @if ($jadwalKeberangkatan)
                        <div class="text-center">
                            <div class="bg-primary text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <h5 class="text-primary">{{ $jadwalKeberangkatan->nama_batch }}</h5>
                            <p class="text-muted mb-2">{{ $jadwalKeberangkatan->tujuan_kota }}</p>
                            <h4 class="text-dark">{{ $jadwalKeberangkatan->tanggal_keberangkatan->format('d F Y') }}</h4>
                            <small
                                class="text-muted">{{ $jadwalKeberangkatan->tanggal_keberangkatan->diffForHumans() }}</small>

                            <div class="mt-3 pt-3 border-top">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h6 class="text-muted mb-1">Kategori</h6>
                                        <small>{{ $jadwalKeberangkatan->kategori_pekerjaan }}</small>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-muted mb-1">Skor Anda</h6>
                                        <small>{{ $jadwalKeberangkatan->pivot->skor_akhir ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                            <h6 class="text-muted">Belum Ada Jadwal</h6>
                            <p class="text-muted small">Pastikan profil dan dokumen sudah lengkap untuk mendapatkan jadwal
                                keberangkatan.</p>

                            @if ($user->status !== 'terverifikasi')
                                <div class="mt-3">
                                    <small class="text-warning">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Selesaikan verifikasi terlebih dahulu
                                    </small>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Status Dokumen -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-file-alt me-2"></i>Status Dokumen</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Jenis Dokumen</th>
                                    <th>Status</th>
                                    <th>Tanggal Upload</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dokumenWajib as $jenis => $label)
                                    @php
                                        $dokumen = $dokumenUser->get($jenis);
                                    @endphp
                                    <tr>
                                        <td>
                                            <i class="fas fa-file-alt me-2 text-muted"></i>
                                            {{ $label }}
                                        </td>
                                        <td>
                                            @if ($dokumen)
                                                @switch($dokumen->status_verifikasi)
                                                    @case('pending')
                                                        <span class="badge bg-warning">Menunggu Verifikasi</span>
                                                    @break

                                                    @case('disetujui')
                                                        <span class="badge bg-success">Disetujui</span>
                                                    @break

                                                    @case('ditolak')
                                                        <span class="badge bg-danger">Ditolak</span>
                                                    @break
                                                @endswitch
                                            @else
                                                <span class="badge bg-secondary">Belum Upload</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($dokumen)
                                                {{ $dokumen->tanggal_upload->format('d/m/Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if (!$dokumen || $dokumen->status_verifikasi == 'ditolak')
                                                <a href="{{ route('peserta.dokumen') }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-upload me-1"></i>Upload
                                                </a>
                                            @elseif($dokumen->status_verifikasi == 'disetujui')
                                                <button class="btn btn-sm btn-success" disabled>
                                                    <i class="fas fa-check me-1"></i>Selesai
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-warning" disabled>
                                                    <i class="fas fa-clock me-1"></i>Menunggu
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($totalDokumen < 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('peserta.dokumen') }}" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Upload Dokumen
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
