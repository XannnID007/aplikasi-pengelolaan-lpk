@extends('layouts.peserta')

@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Edit Profil Anda</h3>
                    <p class="text-gray-600 mt-1">Lengkapi data profil untuk meningkatkan skor prioritas Anda</p>
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-user-edit text-4xl text-green-500"></i>
                </div>
            </div>
        </div>

        <!-- Progress Alert -->
        @php
            $kelengkapan = 0;
            $fields = [
                'nama',
                'email',
                'telepon',
                'tanggal_lahir',
                'alamat',
                'jenis_kelamin',
                'pendidikan_terakhir',
                'level_bahasa_jepang',
                'skor_bahasa_jepang',
                'pengalaman_kerja',
                'pekerjaan_diinginkan',
            ];
            foreach ($fields as $field) {
                if (!empty($user->$field)) {
                    $kelengkapan++;
                }
            }
            $kelengkapanPersen = round(($kelengkapan / count($fields)) * 100);
        @endphp

        @if ($kelengkapanPersen < 100)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <h4 class="text-sm font-medium text-yellow-800">Profil {{ $kelengkapanPersen }}% Lengkap</h4>
                        <p class="text-sm text-yellow-700 mt-1">Lengkapi semua field untuk mendapatkan skor prioritas
                            maksimal dan peluang terbaik mendapat jadwal keberangkatan.</p>
                        <div class="w-full bg-yellow-200 rounded-full h-2 mt-2">
                            <div class="bg-yellow-500 h-2 rounded-full transition-all duration-300"
                                style="width: {{ $kelengkapanPersen }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <form action="{{ route('peserta.profil.update') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Data Pribadi -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-user mr-2"></i>Data Pribadi
                    </h4>
                </div>

                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama -->
                        <div>
                            <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500 @error('nama') border-red-300 @enderror"
                                required>
                            @error('nama')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500 @error('email') border-red-300 @enderror"
                                required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Telepon -->
                        <div>
                            <label for="telepon" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="telepon" name="telepon" value="{{ old('telepon', $user->telepon) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500 @error('telepon') border-red-300 @enderror"
                                required>
                            @error('telepon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Lahir <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', $user->tanggal_lahir ? $user->tanggal_lahir->format('Y-m-d') : '') }}"
                                max="{{ date('Y-m-d', strtotime('-17 years')) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500 @error('tanggal_lahir') border-red-300 @enderror"
                                required>
                            @error('tanggal_lahir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jenis Kelamin -->
                        <div>
                            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Kelamin <span class="text-red-500">*</span>
                            </label>
                            <select id="jenis_kelamin" name="jenis_kelamin"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500 @error('jenis_kelamin') border-red-300 @enderror"
                                required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L"
                                    {{ old('jenis_kelamin', $user->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki
                                </option>
                                <option value="P"
                                    {{ old('jenis_kelamin', $user->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan
                                </option>
                            </select>
                            @error('jenis_kelamin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alamat -->
                        <div class="md:col-span-2">
                            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat Lengkap <span class="text-red-500">*</span>
                            </label>
                            <textarea id="alamat" name="alamat" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500 @error('alamat') border-red-300 @enderror"
                                placeholder="Masukkan alamat lengkap..." required>{{ old('alamat', $user->alamat) }}</textarea>
                            @error('alamat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Data Pendidikan & Keahlian -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-graduation-cap mr-2"></i>Pendidikan & Keahlian
                    </h4>
                </div>

                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Pendidikan Terakhir -->
                        <div>
                            <label for="pendidikan_terakhir" class="block text-sm font-medium text-gray-700 mb-2">
                                Pendidikan Terakhir <span class="text-red-500">*</span>
                            </label>
                            <select id="pendidikan_terakhir" name="pendidikan_terakhir"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500 @error('pendidikan_terakhir') border-red-300 @enderror"
                                required>
                                <option value="">Pilih Pendidikan Terakhir</option>
                                @foreach ($pendidikanOptions as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('pendidikan_terakhir', $user->pendidikan_terakhir) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pendidikan_terakhir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pekerjaan yang Diinginkan -->
                        <div>
                            <label for="pekerjaan_diinginkan" class="block text-sm font-medium text-gray-700 mb-2">
                                Pekerjaan yang Diinginkan
                            </label>
                            <select id="pekerjaan_diinginkan" name="pekerjaan_diinginkan"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500 @error('pekerjaan_diinginkan') border-red-300 @enderror">
                                <option value="">Pilih Pekerjaan yang Diinginkan</option>
                                @foreach ($pekerjaanOptions as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('pekerjaan_diinginkan', $user->pekerjaan_diinginkan) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pekerjaan_diinginkan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Level Bahasa Jepang -->
                        <div>
                            <label for="level_bahasa_jepang" class="block text-sm font-medium text-gray-700 mb-2">
                                Level Bahasa Jepang
                            </label>
                            <select id="level_bahasa_jepang" name="level_bahasa_jepang"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500 @error('level_bahasa_jepang') border-red-300 @enderror">
                                <option value="">Pilih Level Bahasa Jepang</option>
                                @foreach ($levelBahasaJepang as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('level_bahasa_jepang', $user->level_bahasa_jepang) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('level_bahasa_jepang')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Skor Bahasa Jepang -->
                        <div>
                            <label for="skor_bahasa_jepang" class="block text-sm font-medium text-gray-700 mb-2">
                                Skor Bahasa Jepang
                            </label>
                            <input type="number" id="skor_bahasa_jepang" name="skor_bahasa_jepang"
                                value="{{ old('skor_bahasa_jepang', $user->skor_bahasa_jepang) }}" min="0"
                                max="100"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500 @error('skor_bahasa_jepang') border-red-300 @enderror"
                                placeholder="0-100">
                            @error('skor_bahasa_jepang')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Kosongkan jika belum ada skor</p>
                        </div>

                        <!-- Pengalaman Kerja -->
                        <div class="md:col-span-2">
                            <label for="pengalaman_kerja" class="block text-sm font-medium text-gray-700 mb-2">
                                Pengalaman Kerja
                            </label>
                            <textarea id="pengalaman_kerja" name="pengalaman_kerja" rows="4"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500 @error('pengalaman_kerja') border-red-300 @enderror"
                                placeholder="Deskripsikan pengalaman kerja Anda sebelumnya...">{{ old('pengalaman_kerja', $user->pengalaman_kerja) }}</textarea>
                            @error('pengalaman_kerja')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Semakin detail pengalaman kerja, semakin tinggi skor
                                prioritas Anda</p>
                        </div>
                    </div>
                </div>

                <!-- Keamanan -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-lock mr-2"></i>Keamanan
                    </h4>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Password Baru -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password Baru
                            </label>
                            <input type="password" id="password" name="password"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500 @error('password') border-red-300 @enderror"
                                placeholder="Kosongkan jika tidak ingin mengubah">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password Baru
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500"
                                placeholder="Ulangi password baru">
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="px-6 py-4 bg-gray-50 flex justify-between items-center">
                    <a href="{{ route('peserta.profil') }}"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Validasi level bahasa dan skor
        const levelBahasa = document.getElementById('level_bahasa_jepang');
        const skorBahasa = document.getElementById('skor_bahasa_jepang');

        levelBahasa.addEventListener('change', function() {
            if (this.value && !skorBahasa.value) {
                skorBahasa.focus();
                skorBahasa.parentNode.querySelector('.text-gray-500').textContent =
                    'Wajib diisi jika level bahasa dipilih';
                skorBahasa.parentNode.querySelector('.text-gray-500').classList.add('text-yellow-600');
            }
        });

        // Form submission loading
        document.querySelector('form').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            submitBtn.disabled = true;
        });

        // Real-time kelengkapan profil
        const requiredFields = ['nama', 'email', 'telepon', 'tanggal_lahir', 'alamat', 'jenis_kelamin',
            'pendidikan_terakhir'
        ];
        const optionalFields = ['level_bahasa_jepang', 'skor_bahasa_jepang', 'pengalaman_kerja', 'pekerjaan_diinginkan'];

        function updateKelengkapan() {
            let completed = 0;
            const totalFields = requiredFields.length + optionalFields.length;

            [...requiredFields, ...optionalFields].forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field && field.value.trim() !== '') {
                    completed++;
                }
            });

            const percentage = Math.round((completed / totalFields) * 100);

            // Update progress bar if exists
            const progressBar = document.querySelector('.bg-yellow-500');
            if (progressBar) {
                progressBar.style.width = percentage + '%';
            }
        }

        // Add event listeners to all fields
        [...requiredFields, ...optionalFields].forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field) {
                field.addEventListener('input', updateKelengkapan);
                field.addEventListener('change', updateKelengkapan);
            }
        });
    </script>
@endpush
