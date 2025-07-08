@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
    <div class="auth-header">
        <h2><i class="fas fa-torii-gate me-2"></i>LPK Jepang</h2>
        <p>Daftar sebagai calon peserta</p>
    </div>

    <div class="auth-body">
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Lengkap</label>
                <div class="icon-input">
                    <i class="fas fa-user"></i>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                        name="nama" value="{{ old('nama') }}" placeholder="Masukkan nama lengkap" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="icon-input">
                    <i class="fas fa-envelope"></i>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" placeholder="Masukkan email" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="icon-input">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password" placeholder="Kata sandi" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi</label>
                    <div class="icon-input">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                            placeholder="Konfirmasi" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="telepon" class="form-label">Nomor Telepon</label>
                <div class="icon-input">
                    <i class="fas fa-phone"></i>
                    <input type="tel" class="form-control @error('telepon') is-invalid @enderror" id="telepon"
                        name="telepon" value="{{ old('telepon') }}" placeholder="Masukkan nomor telepon" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <div class="icon-input">
                        <i class="fas fa-calendar"></i>
                        <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                            id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                    <select class="form-control @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin"
                        name="jenis_kelamin" required>
                        <option value="">Pilih</option>
                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="alamat" class="form-label">Alamat</label>
                <div class="icon-input">
                    <i class="fas fa-map-marker-alt"></i>
                    <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3"
                        placeholder="Masukkan alamat lengkap" required>{{ old('alamat') }}</textarea>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                </button>
            </div>
        </form>

        <div class="auth-links">
            <p class="mb-0 text-muted">Sudah punya akun?
                <a href="{{ route('login') }}">Masuk di sini</a>
            </p>
        </div>
    </div>
@endsection
