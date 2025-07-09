@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-title', 'Laporan & Analisis')

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-500 to-blue-600 rounded-xl shadow-lg p-6 text-white mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Laporan & Analisis</h2>
                <p class="text-purple-100">Dashboard komprehensif untuk monitoring dan evaluasi kinerja sistem</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-chart-line text-6xl text-white opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Ringkasan Laporan -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-blue-600">{{ $ringkasanLaporan['total_peserta'] }}</div>
                    <div class="text-sm text-gray-600">Total Peserta</div>
                    <div class="text-xs text-green-600 mt-1">
                        +{{ $ringkasanLaporan['peserta_aktif_bulan_ini'] }} bulan ini
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-green-600">{{ $ringkasanLaporan['total_keberangkatan'] }}</div>
                    <div class="text-sm text-gray-600">Total Keberangkatan</div>
                    <div class="text-xs text-green-600 mt-1">
                        +{{ $ringkasanLaporan['keberangkatan_bulan_ini'] }} bulan ini
                    </div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-plane-departure text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-purple-600">
                        {{ number_format($ringkasanLaporan['tingkat_verifikasi'], 1) }}%</div>
                    <div class="text-sm text-gray-600">Tingkat Verifikasi</div>
                    <div class="text-xs text-gray-500 mt-1">Peserta terverifikasi</div>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shield-alt text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-orange-600">
                        {{ number_format($ringkasanLaporan['efektivitas_penjadwalan'], 1) }}%</div>
                    <div class="text-sm text-gray-600">Efektivitas Penjadwalan</div>
                    <div class="text-xs text-gray-500 mt-1">Terverifikasi → Terjadwal</div>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Pendaftaran Bulanan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-chart-line mr-2"></i>Pendaftaran Bulanan
                </h3>
            </div>
            <div class="p-6">
                <div class="h-64">
                    <canvas id="pendaftaranChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Distribusi Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-chart-pie mr-2"></i>Distribusi Status Peserta
                </h3>
            </div>
            <div class="p-6">
                <div class="h-64">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('admin.laporan.peserta') }}"
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <h4 class="font-medium text-gray-900 mb-2">Laporan Peserta</h4>
                <p class="text-sm text-gray-500">Detail data peserta dan statistik</p>
            </div>
        </a>

        <a href="{{ route('admin.laporan.keberangkatan') }}"
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="text-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <i class="fas fa-plane-departure text-green-600"></i>
                </div>
                <h4 class="font-medium text-gray-900 mb-2">Laporan Keberangkatan</h4>
                <p class="text-sm text-gray-500">Analisis jadwal dan keberangkatan</p>
            </div>
        </a>

        <button onclick="showExportModal('peserta')"
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="text-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <i class="fas fa-download text-purple-600"></i>
                </div>
                <h4 class="font-medium text-gray-900 mb-2">Export Data</h4>
                <p class="text-sm text-gray-500">Download laporan dalam berbagai format</p>
            </div>
        </button>

        <a href="{{ route('admin.penjadwalan.analisis') }}"
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="text-center">
                <div class="w-12 h-12 bg-orange-100 rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <i class="fas fa-chart-bar text-orange-600"></i>
                </div>
                <h4 class="font-medium text-gray-900 mb-2">Analisis Algoritma</h4>
                <p class="text-sm text-gray-500">Evaluasi kinerja algoritma penjadwalan</p>
            </div>
        </a>
    </div>

    <!-- Keberangkatan Bulanan Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-chart-area mr-2"></i>Tren Keberangkatan Bulanan
            </h3>
        </div>
        <div class="p-6">
            <div class="h-64">
                <canvas id="keberangkatanChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Modal Export -->
    <div id="exportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Export Laporan</h3>
            </div>
            <form id="exportForm" method="GET">
                <div class="p-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Laporan</label>
                        <select name="type" id="exportType"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Pilih Jenis Laporan</option>
                            <option value="peserta-excel">Data Peserta (Excel)</option>
                            <option value="peserta-pdf">Data Peserta (PDF)</option>
                            <option value="keberangkatan-excel">Data Keberangkatan (Excel)</option>
                            <option value="keberangkatan-pdf">Data Keberangkatan (PDF)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dari</label>
                            <input type="date" name="tanggal_dari"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sampai</label>
                            <input type="date" name="tanggal_sampai"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter Status</label>
                        <select name="status"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="terverifikasi">Terverifikasi</option>
                            <option value="terjadwal">Terjadwal</option>
                            <option value="berangkat">Berangkat</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeExportModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-download mr-1"></i>Export
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        // Chart Pendaftaran Bulanan
        const pendaftaranCtx = document.getElementById('pendaftaranChart').getContext('2d');
        const pendaftaranData = @json($chartData['pendaftaran_bulanan']);

        const bulanNama = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const pendaftaranChartData = [];
        const pendaftaranLabels = [];

        for (let i = 1; i <= 12; i++) {
            const data = pendaftaranData.find(item => item.bulan === i);
            pendaftaranChartData.push(data ? data.jumlah : 0);
            pendaftaranLabels.push(bulanNama[i - 1]);
        }

        new Chart(pendaftaranCtx, {
            type: 'line',
            data: {
                labels: pendaftaranLabels,
                datasets: [{
                    label: 'Jumlah Pendaftar',
                    data: pendaftaranChartData,
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
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Chart Distribusi Status
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusData = @json($chartData['distribusi_status']);

        const statusLabels = statusData.map(item => {
            const labels = {
                'pending': 'Pending',
                'terverifikasi': 'Terverifikasi',
                'terjadwal': 'Terjadwal',
                'berangkat': 'Berangkat',
                'ditolak': 'Ditolak'
            };
            return labels[item.status] || item.status;
        });
        const statusValues = statusData.map(item => item.jumlah);

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: [
                        '#f59e0b', // pending - amber
                        '#10b981', // terverifikasi - emerald
                        '#3b82f6', // terjadwal - blue
                        '#8b5cf6', // berangkat - violet
                        '#ef4444' // ditolak - red
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Chart Keberangkatan Bulanan
        const keberangkatanCtx = document.getElementById('keberangkatanChart').getContext('2d');
        const keberangkatanData = @json($chartData['keberangkatan_bulanan']);

        const keberangkatanChartData = [];
        for (let i = 1; i <= 12; i++) {
            const data = keberangkatanData.find(item => item.bulan === i);
            keberangkatanChartData.push(data ? data.jumlah : 0);
        }

        new Chart(keberangkatanCtx, {
            type: 'bar',
            data: {
                labels: pendaftaranLabels,
                datasets: [{
                    label: 'Jumlah Keberangkatan',
                    data: keberangkatanChartData,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 4
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
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Export Modal Functions
        function showExportModal(type) {
            const modal = document.getElementById('exportModal');
            const form = document.getElementById('exportForm');

            form.action = '{{ route('admin.laporan.export', '') }}/' + type;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeExportModal() {
            const modal = document.getElementById('exportModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('exportModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeExportModal();
            }
        });
    </script>
@endpush
