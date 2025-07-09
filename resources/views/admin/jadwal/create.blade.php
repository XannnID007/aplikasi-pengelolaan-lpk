@extends('layouts.admin')

@section('title', 'Buat Jadwal Baru')
@section('page-title', 'Buat Jadwal Keberangkatan')

@section('content')
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Buat Jadwal Keberangkatan Baru</h3>
                    <p class="text-gray-600 mt-1">Isi formulir di bawah untuk membuat jadwal keberangkatan baru</p>
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-calendar-plus text-4xl text-blue-500"></i>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-lg font-medium text-gray-900">Informasi Jadwal</h4>
            </div>

            <form action="{{ route('admin.jadwal.store') }}" method="POST" class="p-6">
                @csrf

                <!-- Nama Batch -->
                <div class="mb-6">
                    <label for="nama_batch" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Batch <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama_batch" name="nama_batch" value="{{ old('nama_batch') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-blue-500 @error('nama_batch') border-red-300 @enderror"
                        placeholder="Contoh: Batch Tokyo-IT-01-2025" required>
                    @error('nama_batch')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Grid 2 kolom -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Tanggal Keberangkatan -->
                    <div>
                        <label for="tanggal_keberangkatan" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Keberangkatan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="tanggal_keberangkatan" name="tanggal_keberangkatan"
                            value="{{ old('tanggal_keberangkatan') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-blue-500 @error('tanggal_keberangkatan') border-red-300 @enderror"
                            required>
                        @error('tanggal_keberangkatan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kapasitas Maksimal -->
                    <div>
                        <label for="kapasitas_maksimal" class="block text-sm font-medium text-gray-700 mb-2">
                            Kapasitas Maksimal <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="kapasitas_maksimal" name="kapasitas_maksimal"
                            value="{{ old('kapasitas_maksimal') }}" min="1" max="100"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-blue-500 @error('kapasitas_maksimal') border-red-300 @enderror"
                            placeholder="Contoh: 30" required>
                        @error('kapasitas_maksimal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Maksimal 100 peserta per batch</p>
                    </div>
                </div>

                <!-- Grid 2 kolom -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Tujuan Kota -->
                    <div>
                        <label for="tujuan_kota" class="block text-sm font-medium text-gray-700 mb-2">
                            Tujuan Kota <span class="text-red-500">*</span>
                        </label>
                        <select id="tujuan_kota" name="tujuan_kota"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-blue-500 @error('tujuan_kota') border-red-300 @enderror"
                            required>
                            <option value="">Pilih Kota Tujuan</option>
                            @foreach ($kotaTujuan as $kota)
                                <option value="{{ $kota }}" {{ old('tujuan_kota') == $kota ? 'selected' : '' }}>
                                    {{ $kota }}
                                </option>
                            @endforeach
                        </select>
                        @error('tujuan_kota')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kategori Pekerjaan -->
                    <div>
                        <label for="kategori_pekerjaan" class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori Pekerjaan <span class="text-red-500">*</span>
                        </label>
                        <select id="kategori_pekerjaan" name="kategori_pekerjaan"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-blue-500 @error('kategori_pekerjaan') border-red-300 @enderror"
                            required>
                            <option value="">Pilih Kategori Pekerjaan</option>
                            @foreach ($kategoriPekerjaan as $kategori)
                                <option value="{{ $kategori }}"
                                    {{ old('kategori_pekerjaan') == $kategori ? 'selected' : '' }}>
                                    {{ $kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_pekerjaan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="mb-6">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="4"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-blue-500 @error('deskripsi') border-red-300 @enderror"
                        placeholder="Deskripsikan detail pekerjaan, persyaratan khusus, atau informasi tambahan...">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Maksimal 1000 karakter</p>
                </div>

                <!-- Info Box -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        </div>
                        <div class="ml-3">
                            <h5 class="text-sm font-medium text-blue-800">Informasi Penting</h5>
                            <ul class="mt-2 text-sm text-blue-700 list-disc list-inside space-y-1">
                                <li>Jadwal akan otomatis berstatus "Aktif" setelah dibuat</li>
                                <li>Peserta akan dapat melihat jadwal ini di halaman mereka</li>
                                <li>Nama batch harus unik dan tidak boleh sama dengan jadwal lain</li>
                                <li>Tanggal keberangkatan minimal H+1 dari hari ini</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.jadwal') }}"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto generate nama batch
        function generateNamaBatch() {
            const tujuan = document.getElementById('tujuan_kota').value;
            const kategori = document.getElementById('kategori_pekerjaan').value;
            const tanggal = document.getElementById('tanggal_keberangkatan').value;

            if (tujuan && kategori && tanggal) {
                const year = new Date(tanggal).getFullYear();
                const month = String(new Date(tanggal).getMonth() + 1).padStart(2, '0');

                // Singkatan kategori
                const kategoriMap = {
                    'Teknologi Informasi': 'IT',
                    'Perhotelan': 'HTL',
                    'Manufaktur': 'MFG',
                    'Administrasi': 'ADM',
                    'Otomotif': 'OTO',
                    'Konstruksi': 'KST',
                    'Pertanian': 'AGR',
                    'Perikanan': 'FSH',
                    'Kesehatan': 'MED',
                    'Pendidikan': 'EDU'
                };

                const singkatan = kategoriMap[kategori] || kategori.substring(0, 3).toUpperCase();
                const namaBatch = `Batch ${tujuan}-${singkatan}-${month}-${year}`;

                document.getElementById('nama_batch').value = namaBatch;
            }
        }

        // Event listeners untuk auto generate
        document.getElementById('tujuan_kota').addEventListener('change', generateNamaBatch);
        document.getElementById('kategori_pekerjaan').addEventListener('change', generateNamaBatch);
        document.getElementById('tanggal_keberangkatan').addEventListener('change', generateNamaBatch);

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            submitBtn.disabled = true;
        });

        // Character counter for description
        const deskripsi = document.getElementById('deskripsi');
        const maxLength = 1000;

        // Create counter element
        const counter = document.createElement('p');
        counter.className = 'mt-1 text-sm text-gray-500';
        counter.textContent = `0/${maxLength} karakter`;
        deskripsi.parentNode.insertBefore(counter, deskripsi.nextSibling.nextSibling);

        deskripsi.addEventListener('input', function() {
            const currentLength = this.value.length;
            counter.textContent = `${currentLength}/${maxLength} karakter`;

            if (currentLength > maxLength * 0.9) {
                counter.classList.add('text-red-500');
                counter.classList.remove('text-gray-500');
            } else {
                counter.classList.add('text-gray-500');
                counter.classList.remove('text-red-500');
            }
        });
    </script>
@endpush
