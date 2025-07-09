@extends('layouts.admin')

@section('title', 'Algoritma Penjadwalan')
@section('page-title', 'Algoritma Penjadwalan')

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-500 to-blue-600 rounded-xl shadow-lg p-6 text-white mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Algoritma Greedy Penjadwalan</h2>
                <p class="text-purple-100">Sistem otomatis untuk mengoptimalkan penjadwalan peserta berdasarkan skor
                    prioritas dan kesesuaian kategori</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-magic text-6xl text-white opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Statistik & Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-blue-600">{{ $pesertaTersedia->count() }}</div>
                    <div class="text-sm text-gray-600">Peserta Tersedia</div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-green-600">{{ $jadwalTersedia->count() }}</div>
                    <div class="text-sm text-gray-600">Jadwal Tersedia</div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-purple-600">{{ $analisisEfektivitas['efektivitas_penjadwalan'] }}%
                    </div>
                    <div class="text-sm text-gray-600">Efektivitas</div>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-orange-600">{{ $analisisEfektivitas['pemanfaatan_kapasitas'] }}%
                    </div>
                    <div class="text-sm text-gray-600">Pemanfaatan</div>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-percentage text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Control Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Algorithm Control -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-cogs mr-2"></i>Kontrol Algoritma
                    </h3>
                </div>
                <div class="p-6">
                    @if ($pesertaTersedia->count() > 0 && $jadwalTersedia->count() > 0)
                        <!-- Info Algorithm -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                </div>
                                <div class="ml-3">
                                    <h5 class="text-sm font-medium text-blue-800">Cara Kerja Algoritma Greedy</h5>
                                    <ul class="mt-2 text-sm text-blue-700 list-disc list-inside space-y-1">
                                        <li>Mengurutkan peserta berdasarkan skor prioritas (tertinggi ke terendah)</li>
                                        <li>Memprioritaskan kesesuaian kategori pekerjaan</li>
                                        <li>Mengoptimalkan pemanfaatan kapasitas jadwal</li>
                                        <li>Mempertimbangkan tanggal keberangkatan terdekat</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Run Algorithm -->
                        <form action="{{ route('admin.penjadwalan.generate') }}" method="POST" id="algorithmForm">
                            @csrf
                            <div class="mb-4">
                                <div class="flex items-center mb-3">
                                    <input type="checkbox" id="konfirmasi" name="konfirmasi" value="1"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" required>
                                    <label for="konfirmasi" class="ml-2 text-sm text-gray-700">
                                        Saya memahami bahwa algoritma akan mengubah penjadwalan peserta secara otomatis
                                    </label>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <button type="submit" id="runAlgorithmBtn"
                                    class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:from-purple-700 hover:to-blue-700 transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-magic mr-2"></i>Jalankan Algoritma
                                </button>

                                <button type="button" onclick="showResetModal()"
                                    class="px-6 py-3 border border-red-300 text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                    <i class="fas fa-undo mr-2"></i>Reset
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Tidak Dapat Menjalankan Algoritma</h4>
                            <p class="text-gray-600 mb-4">
                                @if ($pesertaTersedia->count() == 0)
                                    Tidak ada peserta terverifikasi yang belum terjadwal.
                                @endif
                                @if ($jadwalTersedia->count() == 0)
                                    Tidak ada jadwal keberangkatan yang tersedia.
                                @endif
                            </p>
                            <div class="flex justify-center gap-3">
                                @if ($jadwalTersedia->count() == 0)
                                    <a href="{{ route('admin.jadwal.create') }}"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-1"></i>Buat Jadwal
                                    </a>
                                @endif
                                <a href="{{ route('admin.peserta') }}"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-users mr-1"></i>Kelola Peserta
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chart-bar mr-2"></i>Statistik Cepat
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Terverifikasi</span>
                        <span class="font-semibold">{{ $analisisEfektivitas['total_peserta_terverifikasi'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Terjadwal</span>
                        <span class="font-semibold">{{ $analisisEfektivitas['total_peserta_terjadwal'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Kapasitas</span>
                        <span class="font-semibold">{{ $analisisEfektivitas['total_kapasitas'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Kapasitas Terisi</span>
                        <span class="font-semibold">{{ $analisisEfektivitas['total_terjadwalkan'] }}</span>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        <a href="{{ route('admin.penjadwalan.analisis') }}"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            <i class="fas fa-chart-line mr-1"></i>Lihat Analisis Lengkap
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simulasi Penjadwalan -->
    @if (isset($simulasiPenjadwalan) && count($simulasiPenjadwalan) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-eye mr-2"></i>Preview Hasil Algoritma
                </h3>
                <p class="text-sm text-gray-600 mt-1">Simulasi bagaimana algoritma akan menjadwalkan peserta</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach ($simulasiPenjadwalan as $simulasi)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $simulasi['jadwal']->nama_batch }}</h4>
                                    <p class="text-sm text-gray-600">{{ $simulasi['jadwal']->tujuan_kota }} •
                                        {{ $simulasi['jadwal']->kategori_pekerjaan }}</p>
                                </div>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if ($simulasi['dapat_diisi'] >= $simulasi['sisa_kapasitas']) bg-green-100 text-green-800 
                                    @elseif($simulasi['dapat_diisi'] > 0) bg-yellow-100 text-yellow-800 
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $simulasi['dapat_diisi'] }}/{{ $simulasi['sisa_kapasitas'] }}
                                </span>
                            </div>

                            @if ($simulasi['peserta_terpilih']->count() > 0)
                                <div class="space-y-2">
                                    <h5 class="text-sm font-medium text-gray-700">Peserta yang akan dijadwalkan:</h5>
                                    @foreach ($simulasi['peserta_terpilih']->take(3) as $peserta)
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-900">{{ $peserta->nama }}</span>
                                            <span class="text-blue-600 font-medium">{{ $peserta->skor_prioritas }}</span>
                                        </div>
                                    @endforeach
                                    @if ($simulasi['peserta_terpilih']->count() > 3)
                                        <p class="text-xs text-gray-500">... dan
                                            {{ $simulasi['peserta_terpilih']->count() - 3 }} peserta lainnya</p>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic">Tidak ada peserta yang sesuai</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Peserta Tersedia -->
    @if ($pesertaTersedia->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-users mr-2"></i>Peserta Tersedia untuk Dijadwalkan ({{ $pesertaTersedia->count() }})
                </h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Peserta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Skor Prioritas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bahasa Jepang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pekerjaan Diinginkan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pendaftaran</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($pesertaTersedia->take(10) as $peserta)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium mr-3">
                                                {{ strtoupper(substr($peserta->nama, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $peserta->nama }}</div>
                                                <div class="text-sm text-gray-500">{{ $peserta->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-lg font-bold text-blue-600">
                                                {{ number_format($peserta->skor_prioritas, 1) }}</div>
                                            <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full"
                                                    style="width: {{ min(100, $peserta->skor_prioritas) }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($peserta->level_bahasa_jepang)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $peserta->level_bahasa_jepang }}
                                            </span>
                                            @if ($peserta->skor_bahasa_jepang)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $peserta->skor_bahasa_jepang }}/100</div>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $peserta->pekerjaan_diinginkan ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $peserta->created_at->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($pesertaTersedia->count() > 10)
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-500">Menampilkan 10 dari {{ $pesertaTersedia->count() }} peserta</p>
                        <a href="{{ route('admin.peserta') }}?status=terverifikasi"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            Lihat Semua Peserta
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Modal Reset Confirmation -->
    <div id="resetModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Reset Penjadwalan</h3>
            </div>
            <div class="p-6">
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        <span class="text-sm text-red-700">Tindakan ini akan mengembalikan semua peserta yang terjadwal ke
                            status "terverifikasi"</span>
                    </div>
                </div>

                <form action="{{ route('admin.penjadwalan.reset') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="konfirmasi_reset" name="konfirmasi_reset" value="1"
                                class="rounded border-gray-300 text-red-600 focus:ring-red-500" required>
                            <label for="konfirmasi_reset" class="ml-2 text-sm text-gray-700">
                                Saya memahami konsekuensi dari reset ini
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeResetModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-undo mr-1"></i>Reset Penjadwalan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Algorithm form submission
        document.getElementById('algorithmForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('runAlgorithmBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menjalankan Algoritma...';
            submitBtn.disabled = true;
        });

        function showResetModal() {
            const modal = document.getElementById('resetModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeResetModal() {
            const modal = document.getElementById('resetModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('resetModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeResetModal();
            }
        });

        // Auto refresh setiap 2 menit
        setInterval(function() {
            if (!document.querySelector('.modal.show') && !document.getElementById('resetModal').classList.contains(
                    'flex')) {
                location.reload();
            }
        }, 120000);
    </script>
@endpush
