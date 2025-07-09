@extends('layouts.peserta')

@section('title', 'Dashboard Peserta')
@section('page-title', 'Dashboard Peserta')

@section('content')
    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-xl shadow-lg p-6 text-white mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Selamat Datang, {{ $user->nama }}!</h2>
                <p class="text-green-100">Selamat datang di portal LPK Jepang. Kelola pendaftaran Anda dan pantau status
                    keberangkatan di sini.</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-torii-gate text-6xl text-white opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-3xl font-bold text-blue-600">{{ number_format($skorPrioritas, 1) }}</div>
                    <div class="text-sm text-gray-600">Skor Prioritas</div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-star text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-lg font-bold">
                        @switch($user->status)
                            @case('pending')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @break

                            @case('terverifikasi')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    Terverifikasi
                                </span>
                            @break

                            @case('terjadwal')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    Terjadwal
                                </span>
                            @break

                            @case('berangkat')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    Berangkat
                                </span>
                            @break

                            @case('ditolak')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    Ditolak
                                </span>
                            @break
                        @endswitch
                    </div>
                    <div class="text-sm text-gray-600">Status Pendaftaran</div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-tasks mr-2"></i>Progress Pendaftaran
                    </h3>
                </div>
                <div class="p-6">
                    <!-- Step 1: Profil -->
                    <div class="flex items-center mb-6 pb-4 border-b border-gray-200">
                        <div class="mr-4">
                            @if ($profilLengkap >= 80)
                                <div
                                    class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center">
                                    <i class="fas fa-check"></i>
                                </div>
                            @else
                                <div
                                    class="w-10 h-10 bg-yellow-500 text-white rounded-full flex items-center justify-center">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 mb-1">Lengkapi Profil</h4>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                    style="width: {{ $profilLengkap }}%"></div>
                            </div>
                            <div class="text-sm text-gray-500">{{ $profilLengkap }}% selesai</div>
                        </div>
                        <div class="ml-4">
                            @if ($profilLengkap < 100)
                                <a href="{{ route('peserta.profil') }}"
                                    class="px-3 py-1 text-sm bg-blue-50 text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Step 2: Dokumen -->
                    <div class="flex items-center mb-6 pb-4 border-b border-gray-200">
                        <div class="mr-4">
                            @if ($dokumenDisetujui >= 5)
                                <div
                                    class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center">
                                    <i class="fas fa-check"></i>
                                </div>
                            @elseif($totalDokumen > 0)
                                <div
                                    class="w-10 h-10 bg-yellow-500 text-white rounded-full flex items-center justify-center">
                                    <i class="fas fa-file"></i>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-gray-400 text-white rounded-full flex items-center justify-center">
                                    <i class="fas fa-file"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 mb-1">Upload Dokumen</h4>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                    style="width: {{ ($dokumenDisetujui / 5) * 100 }}%"></div>
                            </div>
                            <div class="text-sm text-gray-500">{{ $dokumenDisetujui }} dari 5 dokumen disetujui</div>
                        </div>
                        <div class="ml-4">
                            <a href="{{ route('peserta.dokumen') }}"
                                class="px-3 py-1 text-sm bg-blue-50 text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="fas fa-upload mr-1"></i>Upload
                            </a>
                        </div>
                    </div>

                    <!-- Step 3: Verifikasi -->
                    <div class="flex items-center mb-6 pb-4 border-b border-gray-200">
                        <div class="mr-4">
                            @if ($user->status == 'terverifikasi' || $user->status == 'terjadwal' || $user->status == 'berangkat')
                                <div
                                    class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center">
                                    <i class="fas fa-check"></i>
                                </div>
                            @elseif($dokumenPending > 0)
                                <div
                                    class="w-10 h-10 bg-yellow-500 text-white rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock"></i>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-gray-400 text-white rounded-full flex items-center justify-center">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 mb-1">Verifikasi Admin</h4>
                            @if ($user->status == 'terverifikasi')
                                <div class="text-sm text-green-600">Status Anda telah terverifikasi</div>
                            @elseif($dokumenPending > 0)
                                <div class="text-sm text-yellow-600">{{ $dokumenPending }} dokumen menunggu verifikasi</div>
                            @else
                                <div class="text-sm text-gray-500">Menunggu upload dokumen lengkap</div>
                            @endif
                        </div>
                    </div>

                    <!-- Step 4: Jadwal -->
                    <div class="flex items-center">
                        <div class="mr-4">
                            @if ($jadwalKeberangkatan)
                                <div
                                    class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center">
                                    <i class="fas fa-check"></i>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-gray-400 text-white rounded-full flex items-center justify-center">
                                    <i class="fas fa-calendar"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 mb-1">Penjadwalan Keberangkatan</h4>
                            @if ($jadwalKeberangkatan)
                                <div class="text-sm text-green-600">Anda telah dijadwalkan untuk berangkat</div>
                            @else
                                <div class="text-sm text-gray-500">Menunggu penjadwalan dari admin</div>
                            @endif
                        </div>
                        @if ($jadwalKeberangkatan)
                            <div class="ml-4">
                                <a href="{{ route('peserta.jadwal') }}"
                                    class="px-3 py-1 text-sm bg-blue-50 text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-calendar mr-1"></i>Lihat
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Keberangkatan -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-plane-departure mr-2"></i>Jadwal Keberangkatan
                    </h3>
                </div>
                <div class="p-6">
                    @if ($jadwalKeberangkatan)
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <i class="fas fa-calendar-check text-blue-600 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-blue-600 mb-2">{{ $jadwalKeberangkatan->nama_batch }}
                            </h4>
                            <p class="text-gray-600 mb-2">{{ $jadwalKeberangkatan->tujuan_kota }}</p>
                            <div class="text-2xl font-bold text-gray-900 mb-2">
                                {{ $jadwalKeberangkatan->tanggal_keberangkatan->format('d F Y') }}</div>
                            <div class="text-sm text-gray-500 mb-4">
                                {{ $jadwalKeberangkatan->tanggal_keberangkatan->diffForHumans() }}</div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-2 gap-4 text-center">
                                    <div>
                                        <div class="text-sm text-gray-600 mb-1">Kategori</div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $jadwalKeberangkatan->kategori_pekerjaan }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600 mb-1">Skor Anda</div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $jadwalKeberangkatan->pivot->skor_akhir ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <i class="fas fa-calendar-times text-gray-300 text-4xl mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-600 mb-2">Belum Ada Jadwal</h4>
                            <p class="text-sm text-gray-500 mb-4">Pastikan profil dan dokumen sudah lengkap untuk
                                mendapatkan jadwal keberangkatan.</p>

                            @if ($user->status !== 'terverifikasi')
                                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="text-sm text-yellow-700">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Selesaikan verifikasi terlebih dahulu
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Status Dokumen -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-file-alt mr-2"></i>Status Dokumen
            </h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jenis Dokumen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Upload</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($dokumenWajib as $jenis => $label)
                            @php
                                $dokumen = $dokumenUser->get($jenis);
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-alt text-gray-400 mr-2"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ $label }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($dokumen)
                                        @switch($dokumen->status_verifikasi)
                                            @case('pending')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Menunggu Verifikasi
                                                </span>
                                            @break

                                            @case('disetujui')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Disetujui
                                                </span>
                                            @break

                                            @case('ditolak')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Ditolak
                                                </span>
                                            @break
                                        @endswitch
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Belum Upload
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($dokumen)
                                        {{ $dokumen->tanggal_upload->format('d/m/Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if (!$dokumen || $dokumen->status_verifikasi == 'ditolak')
                                        <a href="{{ route('peserta.dokumen') }}"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-upload mr-1"></i>Upload
                                        </a>
                                    @elseif($dokumen->status_verifikasi == 'disetujui')
                                        <span
                                            class="inline-flex items-center px-3 py-2 border border-green-300 text-sm leading-4 font-medium rounded-md text-green-700 bg-green-50">
                                            <i class="fas fa-check mr-1"></i>Selesai
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-2 border border-yellow-300 text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-50">
                                            <i class="fas fa-clock mr-1"></i>Menunggu
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($totalDokumen < 5)
                <div class="mt-6 text-center">
                    <a href="{{ route('peserta.dokumen') }}"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        <i class="fas fa-upload mr-2"></i>Upload Dokumen
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
