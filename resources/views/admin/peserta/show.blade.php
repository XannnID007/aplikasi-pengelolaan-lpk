@extends('layouts.admin')

@section('title', 'Detail Peserta')
@section('page-title', 'Detail Peserta - ' . $peserta->nama)

@section('content')
    <div class="row">
        <!-- Profil Peserta -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user me-2"></i>Profil Peserta</h5>
                </div>
                <div class="card-body text-center">
                    <div class="avatar bg-primary text-white rounded-circle mx-auto mb-3"
                        style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                        {{ strtoupper(substr($peserta->nama, 0, 1)) }}
                    </div>
                    <h4>{{ $peserta->nama }}</h4>
                    <p class="text-muted">{{ $peserta->email }}</p>

                    <!-- Status Badge -->
                    <div class="mb-3">
                        @switch($peserta->status)
                            @case('pending')
                                <span class="badge bg-warning fs-6">Pending Verifikasi</span>
                            @break

                            @case('terverifikasi')
                                <span class="badge bg-success fs-6">Terverifikasi</span>
                            @break

                            @case('terjadwal')
                                <span class="badge bg-info fs-6">Terjadwal</span>
                            @break

                            @case('berangkat')
                                <span class="badge bg-primary fs-6">Sudah Berangkat</span>
                            @break

                            @case('ditolak')
                                <span class="badge bg-danger fs-6">Ditolak</span>
                            @break
                        @endswitch
                    </div>

                    <!-- Skor Prioritas -->
                    <div class="mb-3">
                        <h6>Skor Prioritas</h6>
                        <div class="h3 text-primary">{{ number_format($peserta->skor_prioritas, 1) }}</div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ min(100, $peserta->skor_prioritas) }}%"></div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                            <i class="fas fa-edit me-1"></i>Update Status
                        </button>
                        <a href="{{ route('admin.peserta.edit', $peserta->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-user-edit me-1"></i>Edit Profil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Kelengkapan Profil -->
            <div class="card">
                <div class="card-body">
                    <h6><i class="fas fa-chart-pie me-2"></i>Kelengkapan Profil</h6>
                    <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar" style="width: {{ $kelengkapanProfil }}%"></div>
                    </div>
                    <small class="text-muted">{{ $kelengkapanProfil }}% lengkap</small>
                </div>
            </div>
        </div>

        <!-- Informasi Detail -->
        <div class="col-lg-8 mb-4">
            <!-- Data Pribadi -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-address-card me-2"></i>Data Pribadi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <p>{{ $peserta->nama }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <p>{{ $peserta->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nomor Telepon</label>
                            <p>{{ $peserta->telepon ?: '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Lahir</label>
                            <p>{{ $peserta->tanggal_lahir ? $peserta->tanggal_lahir->format('d F Y') : '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jenis Kelamin</label>
                            <p>{{ $peserta->jenis_kelamin == 'L' ? 'Laki-laki' : ($peserta->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Daftar</label>
                            <p>{{ $peserta->created_at->format('d F Y H:i') }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Alamat</label>
                            <p>{{ $peserta->alamat ?: '-' }}</p>
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
                            <label class="form-label fw-bold">Pendidikan Terakhir</label>
                            <p>{{ $peserta->pendidikan_terakhir ?: '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Pekerjaan yang Diinginkan</label>
                            <p>{{ $peserta->pekerjaan_diinginkan ?: '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Level Bahasa Jepang</label>
                            <p>
                                @if ($peserta->level_bahasa_jepang)
                                    <span class="badge bg-info">{{ $peserta->level_bahasa_jepang }}</span>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Skor Bahasa Jepang</label>
                            <p>{{ $peserta->skor_bahasa_jepang ?: '-' }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Pengalaman Kerja</label>
                            <p style="white-space: pre-line;">{{ $peserta->pengalaman_kerja ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Dokumen -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-file-alt me-2"></i>Status Dokumen</h5>
                </div>
                <div class="card-body">
                    @if ($dokumen->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Jenis Dokumen</th>
                                        <th>Status</th>
                                        <th>Tanggal Upload</th>
                                        <th>Verifikasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dokumen as $doc)
                                        <tr>
                                            <td>{{ \App\Models\Dokumen::jenisDokumenWajib()[$doc->jenis_dokumen] ?? $doc->jenis_dokumen }}
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
                                                    {{ $doc->tanggal_verifikasi->format('d/m/Y') }}
                                                    <br><small class="text-muted">oleh
                                                        {{ $doc->verifiedBy?->nama }}</small>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.dokumen.show', $doc->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-file-times text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">Belum ada dokumen yang diupload</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Jadwal Keberangkatan -->
            @if ($jadwalKeberangkatan)
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-calendar-check me-2"></i>Jadwal Keberangkatan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>{{ $jadwalKeberangkatan->nama_batch }}</h6>
                                <p class="text-muted">{{ $jadwalKeberangkatan->tujuan_kota }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Tanggal Keberangkatan</h6>
                                <p class="text-primary">{{ $jadwalKeberangkatan->tanggal_keberangkatan->format('d F Y') }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>Kategori Pekerjaan</h6>
                                <p>{{ $jadwalKeberangkatan->kategori_pekerjaan }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Skor Akhir</h6>
                                <p class="text-success">{{ $jadwalKeberangkatan->pivot->skor_akhir ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.jadwal.show', $jadwalKeberangkatan->id) }}"
                                class="btn btn-outline-primary">
                                <i class="fas fa-calendar-alt me-1"></i>Lihat Detail Jadwal
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Update Status -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status Peserta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.peserta.updateStatus', $peserta->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Status Baru</label>
                            <select name="status" class="form-control" required>
                                <option value="pending" {{ $peserta->status == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="terverifikasi" {{ $peserta->status == 'terverifikasi' ? 'selected' : '' }}>
                                    Terverifikasi</option>
                                <option value="terjadwal" {{ $peserta->status == 'terjadwal' ? 'selected' : '' }}>
                                    Terjadwal</option>
                                <option value="berangkat" {{ $peserta->status == 'berangkat' ? 'selected' : '' }}>
                                    Berangkat</option>
                                <option value="ditolak" {{ $peserta->status == 'ditolak' ? 'selected' : '' }}>Ditolak
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Alasan perubahan status..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
