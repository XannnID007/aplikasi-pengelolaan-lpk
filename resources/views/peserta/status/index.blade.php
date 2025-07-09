@extends('layouts.peserta')

@section('title', 'Status Pendaftaran')
@section('page-title', 'Status Pendaftaran')

@section('content')
    <!-- Progress Overview -->
    <div class="bg-gradient-to-r from-blue-500 to-green-600 rounded-xl shadow-lg p-6 text-white mb-8">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <h3 class="text-xl font-bold mb-2">Tracking Status Pendaftaran Anda</h3>
                <p class="text-blue-100">Pantau progres pendaftaran dan persiapan keberangkatan ke Jepang</p>
            </div>
            <div class="ml-6 text-center">
                <div class="text-3xl font-bold mb-1">{{ $progressDetail['verifikasi']['skor_prioritas'] ?? 0 }}</div>
                <div class="text-sm text-blue-100">Skor Prioritas</div>
            </div>
        </div>
    </div>

    <!-- Timeline Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-route mr-2"></i>Timeline Pendaftaran
            </h3>
        </div>
        <div class="p-6">
            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute left-8 top-0 h-full w-0.5 bg-gray-200"></div>

                @foreach ($timelineStatus as $step)
                    <div class="relative flex items-start mb-8 last:mb-0">
                        <!-- Timeline Point -->
                        <div class="flex-shrink-0 relative">
                            <div
                                class="w-16 h-16 rounded-full flex items-center justify-center text-white font-medium z-10
                                @if ($step['status'] == 'completed') bg-green-500
                                @elseif($step['status'] == 'in_progress') bg-blue-500
                                @else bg-gray-400 @endif">
                                <i class="{{ $step['icon'] }} text-lg"></i>
                            </div>
                            @if ($step['status'] == 'completed')
                                <div
                                    class="absolute -top-1 -right-1 w-6 h-6 bg-green-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                            @elseif($step['status'] == 'in_progress')
                                <div
                                    class="absolute -top-1 -right-1 w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center">
                                    <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                </div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="ml-6 flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-lg font-semibold text-gray-900">{{ $step['title'] }}</h4>
                                @if ($step['date'])
                                    <span class="text-sm text-gray-500">{{ $step['date']->format('d/m/Y') }}</span>
                                @endif
                            </div>
                            <p class="text-gray-600 mb-3">{{ $step['description'] }}</p>

                            @if ($step['status'] == 'completed')
                                <div
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Selesai
                                </div>
                            @elseif($step['status'] == 'in_progress')
                                <div
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-spinner fa-spin mr-1"></i>Sedang Berlangsung
                                </div>
                            @else
                                <div
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-clock mr-1"></i>Menunggu
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Progress Detail Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Profil -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="font-semibold text-gray-900">
                    <i class="fas fa-user mr-2"></i>Kelengkapan Profil
                </h4>
            </div>
            <div class="p-6">
                <div class="text-center mb-4">
                    <div class="text-3xl font-bold text-{{ $progressDetail['profil']['color'] }}-600">
                        {{ $progressDetail['profil']['persentase'] }}%
                    </div>
                    <div class="text-sm text-gray-600">{{ $progressDetail['profil']['status'] }}</div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-{{ $progressDetail['profil']['color'] }}-500 h-3 rounded-full transition-all duration-500"
                        style="width: {{ $progressDetail['profil']['persentase'] }}%"></div>
                </div>
                @if ($progressDetail['profil']['persentase'] < 100)
                    <div class="mt-4">
                        <a href="{{ route('peserta.profil.edit') }}"
                            class="w-full inline-flex justify-center items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-edit mr-1"></i>Lengkapi
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Dokumen -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="font-semibold text-gray-900">
                    <i class="fas fa-file-alt mr-2"></i>Status Dokumen
                </h4>
            </div>
            <div class="p-6">
                <div class="text-center mb-4">
                    <div class="text-3xl font-bold text-{{ $progressDetail['dokumen']['color'] }}-600">
                        {{ $progressDetail['dokumen']['disetujui'] }}/{{ $progressDetail['dokumen']['total'] }}
                    </div>
                    <div class="text-sm text-gray-600">{{ $progressDetail['dokumen']['status'] }}</div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-{{ $progressDetail['dokumen']['color'] }}-500 h-3 rounded-full transition-all duration-500"
                        style="width: {{ $progressDetail['dokumen']['persentase'] }}%"></div>
                </div>
                @if ($progressDetail['dokumen']['persentase'] < 100)
                    <div class="mt-4">
                        <a href="{{ route('peserta.dokumen') }}"
                            class="w-full inline-flex justify-center items-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-upload mr-1"></i>Upload
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Verifikasi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="font-semibold text-gray-900">
                    <i class="fas fa-shield-alt mr-2"></i>Status Verifikasi
                </h4>
            </div>
            <div class="p-6">
                <div class="text-center mb-4">
                    <div
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $progressDetail['verifikasi']['color'] }}-100 text-{{ $progressDetail['verifikasi']['color'] }}-800">
                        {{ $progressDetail['verifikasi']['label'] }}
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600 mb-1">
                        {{ number_format($progressDetail['verifikasi']['skor_prioritas'], 1) }}
                    </div>
                    <div class="text-sm text-gray-600">Skor Prioritas</div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-500"
                        style="width: {{ min(100, $progressDetail['verifikasi']['skor_prioritas']) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Next Steps -->
    @if ($nextSteps->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-tasks mr-2"></i>Langkah Selanjutnya
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach ($nextSteps as $step)
                        <div
                            class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 mr-4">
                                <div
                                    class="w-10 h-10 
                                    @if ($step['priority'] == 'high') bg-red-100 text-red-600
                                    @elseif($step['priority'] == 'medium') bg-yellow-100 text-yellow-600
                                    @else bg-blue-100 text-blue-600 @endif
                                    rounded-lg flex items-center justify-center">
                                    <i class="{{ $step['icon'] }}"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 mb-1">{{ $step['title'] }}</h4>
                                <p class="text-sm text-gray-600 mb-3">{{ $step['description'] }}</p>
                                <a href="{{ $step['url'] }}"
                                    class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                    {{ $step['action'] }}
                                    <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Estimasi Waktu -->
    @if (!empty($estimasiProses))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-clock mr-2"></i>Estimasi Waktu Proses
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($estimasiProses as $key => $value)
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-sm text-blue-600 font-medium mb-1">
                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                            </div>
                            <div class="text-lg font-semibold text-blue-800">{{ $value }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Riwayat Aktivitas -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-history mr-2"></i>Riwayat Aktivitas
            </h3>
        </div>
        <div class="p-6">
            @if ($riwayatAktivitas->count() > 0)
                <div class="space-y-4">
                    @foreach ($riwayatAktivitas->take(10) as $aktivitas)
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4">
                                <div
                                    class="w-8 h-8 bg-{{ $aktivitas['color'] }}-100 text-{{ $aktivitas['color'] }}-600 rounded-full flex items-center justify-center">
                                    <i class="{{ $aktivitas['icon'] }} text-xs"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $aktivitas['aktivitas'] }}</p>
                                        <p class="text-sm text-gray-600">{{ $aktivitas['keterangan'] }}</p>
                                    </div>
                                    <div class="text-sm text-gray-500 ml-4">
                                        {{ $aktivitas['tanggal']->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($riwayatAktivitas->count() > 10)
                    <div class="mt-6 text-center">
                        <button onclick="loadMoreActivities()"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fas fa-chevron-down mr-2"></i>Lihat Aktivitas Lainnya
                        </button>
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <i class="fas fa-history text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500">Belum ada aktivitas yang tercatat</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function loadMoreActivities() {
            // Implementation for loading more activities via AJAX
            // This is a placeholder for future enhancement
            alert('Fitur ini akan segera tersedia');
        }

        // Auto refresh setiap 2 menit untuk update status real-time
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                location.reload();
            }
        }, 120000);

        // Smooth scrolling untuk navigation
        function scrollToSection(sectionId) {
            document.getElementById(sectionId).scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Progress animation on page load
        document.addEventListener('DOMContentLoaded', function() {
            const progressBars = document.querySelectorAll('[style*="width:"]');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 500);
            });
        });
    </script>

    <style>
        .timeline-step {
            position: relative;
        }

        .timeline-step:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 31px;
            top: 64px;
            width: 2px;
            height: calc(100% - 32px);
            background: #e5e7eb;
        }

        .timeline-step.completed:not(:last-child)::after {
            background: #10b981;
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }
    </style>
@endpush
