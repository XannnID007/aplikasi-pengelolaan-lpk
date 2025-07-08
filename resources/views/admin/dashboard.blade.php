@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('content')
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Peserta -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Peserta</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalPeserta }}</p>
                </div>
            </div>
        </div>

        <!-- Terverifikasi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Terverifikasi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pesertaTerverifikasi }}</p>
                </div>
            </div>
        </div>

        <!-- Terjadwal -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Terjadwal</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pesertaTerjadwal }}</p>
                </div>
            </div>
        </div>

        <!-- Berangkat -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-plane-departure text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Sudah Berangkat</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pesertaBerangkat }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Quick Actions -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-bolt text-blue-600 mr-2"></i>
                    Aksi Cepat
                </h3>

                <!-- Pending Tasks -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600">Dokumen menunggu verifikasi</span>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $dokumenPending }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Jadwal aktif</span>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $jadwalAktif }}
                        </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="{{ route('admin.dokumen') }}"
                        class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-file-check mr-2"></i>
                        Verifikasi Dokumen
                    </a>

                    <a href="{{ route('admin.penjadwalan') }}"
                        class="w-full flex items-center justify-center px-4 py-2 border border-blue-200 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                        <i class="fas fa-magic mr-2"></i>
                        Jalankan Algoritma
                    </a>

                    <a href="{{ route('admin.jadwal.create') }}"
                        class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Buat Jadwal Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Chart & Recent Data -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Grafik Pendaftaran Bulanan
                </h3>
                <div class="h-64">
                    <canvas id="pendaftaranChart" class="w-full h-full"></canvas>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Peserta Terbaru -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-user-plus text-green-600 mr-2"></i>
                        Peserta Terbaru
                    </h3>

                    @if ($pesertaTerbaru->count() > 0)
                        <div class="space-y-3">
                            @foreach ($pesertaTerbaru as $peserta)
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-medium">
                                        {{ strtoupper(substr($peserta->nama, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $peserta->nama }}</p>
                                        <p class="text-xs text-gray-500">{{ $peserta->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @switch($peserta->status)
                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                    @case('terverifikasi') bg-green-100 text-green-800 @break
                                    @case('terjadwal') bg-blue-100 text-blue-800 @break
                                    @case('ditolak') bg-red-100 text-red-800 @break
                                @endswitch">
                                        {{ ucfirst($peserta->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Belum ada peserta terdaftar</p>
                    @endif
                </div>

                <!-- Jadwal Terdekat -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
                        Jadwal Terdekat
                    </h3>

                    @if ($jadwalTerdekat->count() > 0)
                        <div class="space-y-3">
                            @foreach ($jadwalTerdekat as $jadwal)
                                <div class="border-l-4 border-blue-500 pl-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $jadwal->nama_batch }}</p>
                                    <p class="text-xs text-gray-500">{{ $jadwal->tujuan_kota }} •
                                        {{ $jadwal->tanggal_keberangkatan->format('d/m/Y') }}</p>
                                    <div class="mt-1">
                                        <div class="flex items-center text-xs text-gray-500">
                                            <span>{{ $jadwal->jumlah_peserta }}/{{ $jadwal->kapasitas_maksimal }}</span>
                                            <div class="ml-2 flex-1 bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-blue-500 h-1.5 rounded-full"
                                                    style="width: {{ ($jadwal->jumlah_peserta / $jadwal->kapasitas_maksimal) * 100 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Belum ada jadwal</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        // Chart Pendaftaran Bulanan
        const ctx = document.getElementById('pendaftaranChart').getContext('2d');
        const pendaftaranData = @json($pendaftaranBulanan);

        const bulanNama = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const chartData = [];
        const chartLabels = [];

        for (let i = 1; i <= 12; i++) {
            const data = pendaftaranData.find(item => item.bulan === i);
            chartData.push(data ? data.jumlah : 0);
            chartLabels.push(bulanNama[i - 1]);
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Jumlah Pendaftar',
                    data: chartData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    }
                },
                elements: {
                    point: {
                        hoverRadius: 8
                    }
                }
            }
        });
    </script>
@endpush
