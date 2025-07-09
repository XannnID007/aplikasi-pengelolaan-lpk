@extends('layouts.admin')

@section('title', 'Jadwal Keberangkatan')
@section('page-title', 'Jadwal Keberangkatan')

@section('content')
    <!-- Statistik Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $statistik['total'] }}</div>
            <div class="text-sm text-gray-600">Total Jadwal</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $statistik['aktif'] }}</div>
            <div class="text-sm text-gray-600">Aktif</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $statistik['penuh'] }}</div>
            <div class="text-sm text-gray-600">Penuh</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-blue-500">{{ $statistik['selesai'] }}</div>
            <div class="text-sm text-gray-600">Selesai</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-gray-600">{{ $statistik['total_kapasitas'] }}</div>
            <div class="text-sm text-gray-600">Total Kapasitas</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $statistik['total_terjadwal'] }}</div>
            <div class="text-sm text-gray-600">Total Terjadwal</div>
        </div>
    </div>

    <!-- Filter dan Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-end gap-4">
            <!-- Filters -->
            <form method="GET" action="{{ route('admin.jadwal') }}" class="flex-1">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="penuh" {{ request('status') == 'penuh' ? 'selected' : '' }}>Penuh</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select name="bulan"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <select name="tahun"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Tahun</option>
                            @for ($year = date('Y'); $year <= date('Y') + 2; $year++)
                                <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
                                    {{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <input type="text" name="kategori"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Kategori pekerjaan..." value="{{ request('kategori') }}">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-1"></i>Cari
                        </button>
                        <a href="{{ route('admin.jadwal') }}"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>

            <!-- Action Button -->
            <div class="flex-shrink-0">
                <a href="{{ route('admin.jadwal.create') }}"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Buat Jadwal Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Daftar Jadwal -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-calendar-alt mr-2"></i>Daftar Jadwal Keberangkatan ({{ $jadwal->total() }})
            </h3>
        </div>
        <div class="p-6">
            @if ($jadwal->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($jadwal as $j)
                        <div class="border border-gray-200 rounded-xl hover:shadow-md transition-shadow duration-200">
                            <!-- Header dengan status -->
                            <div class="px-6 py-4 border-b border-gray-200">
                                <div class="flex justify-between items-start">
                                    <div>
                                        @switch($j->status)
                                            @case('aktif')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Aktif
                                                </span>
                                            @break

                                            @case('penuh')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Penuh
                                                </span>
                                            @break

                                            @case('selesai')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Selesai
                                                </span>
                                            @break

                                            @case('dibatalkan')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Dibatalkan
                                                </span>
                                            @break
                                        @endswitch
                                    </div>
                                    <div class="relative">
                                        <button class="p-2 text-gray-400 hover:text-gray-600 focus:outline-none"
                                            onclick="toggleDropdown({{ $j->id }})">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div id="dropdown-{{ $j->id }}"
                                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                            <a href="{{ route('admin.jadwal.show', $j->id) }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-eye mr-2"></i>Lihat Detail
                                            </a>
                                            <a href="{{ route('admin.jadwal.edit', $j->id) }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-edit mr-2"></i>Edit
                                            </a>
                                            @if ($j->status != 'selesai')
                                                <hr class="my-1">
                                                <button onclick="selesaikanJadwal({{ $j->id }})"
                                                    class="block w-full text-left px-4 py-2 text-sm text-yellow-600 hover:bg-gray-100">
                                                    <i class="fas fa-check mr-2"></i>Tandai Selesai
                                                </button>
                                            @endif
                                            @if ($j->jumlah_peserta == 0)
                                                <hr class="my-1">
                                                <button onclick="hapusJadwal({{ $j->id }}, '{{ $j->nama_batch }}')"
                                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                                    <i class="fas fa-trash mr-2"></i>Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Body -->
                            <div class="px-6 py-4">
                                <h4 class="text-lg font-semibold text-blue-600 mb-2">{{ $j->nama_batch }}</h4>
                                <p class="text-sm text-gray-600 mb-2">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $j->tujuan_kota }}
                                </p>
                                <p class="text-sm text-gray-600 mb-4">
                                    <i class="fas fa-briefcase mr-1"></i>{{ $j->kategori_pekerjaan }}
                                </p>

                                <!-- Tanggal Keberangkatan -->
                                <div class="mb-4">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-calendar text-blue-500 mr-2"></i>
                                        <div>
                                            <div class="font-semibold text-gray-900">
                                                {{ $j->tanggal_keberangkatan->format('d F Y') }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $j->tanggal_keberangkatan->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress Kapasitas -->
                                <div class="mb-4">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm text-gray-600">Kapasitas</span>
                                        <span
                                            class="text-sm font-semibold">{{ $j->jumlah_peserta }}/{{ $j->kapasitas_maksimal }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        @php
                                            $percentage =
                                                $j->kapasitas_maksimal > 0
                                                    ? ($j->jumlah_peserta / $j->kapasitas_maksimal) * 100
                                                    : 0;
                                        @endphp
                                        <div class="h-2 rounded-full 
                                            @if ($percentage >= 100) bg-red-500 
                                            @elseif($percentage >= 80) bg-yellow-500 
                                            @else bg-green-500 @endif"
                                            style="width: {{ $percentage }}%">
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">{{ number_format($percentage, 1) }}% terisi
                                    </div>
                                </div>

                                @if ($j->deskripsi)
                                    <p class="text-sm text-gray-600">{{ Str::limit($j->deskripsi, 80) }}</p>
                                @endif
                            </div>

                            <!-- Footer -->
                            <div class="px-6 py-4 bg-gray-50 rounded-b-xl">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.jadwal.show', $j->id) }}"
                                        class="flex-1 px-3 py-2 text-center text-sm border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </a>
                                    @if ($j->status == 'aktif')
                                        <a href="{{ route('admin.jadwal.edit', $j->id) }}"
                                            class="px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex justify-center">
                    {{ $jadwal->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada jadwal keberangkatan</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan membuat jadwal keberangkatan pertama.</p>
                    <a href="{{ route('admin.jadwal.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Buat Jadwal Baru
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleDropdown(id) {
            // Close all other dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                if (dropdown.id !== `dropdown-${id}`) {
                    dropdown.classList.add('hidden');
                }
            });

            // Toggle current dropdown
            const dropdown = document.getElementById(`dropdown-${id}`);
            dropdown.classList.toggle('hidden');
        }

        function selesaikanJadwal(id) {
            if (confirm('Tandai jadwal ini sebagai selesai? Semua peserta akan diubah statusnya menjadi "Berangkat".')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/jadwal/${id}/selesaikan`;
                form.innerHTML = `
                    @csrf
                    @method('PUT')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function hapusJadwal(id, nama) {
            if (confirm(`Hapus jadwal "${nama}"? Tindakan ini tidak dapat dibatalkan.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/jadwal/${id}`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[onclick^="toggleDropdown"]')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        });

        // Auto refresh setiap 60 detik
        setInterval(function() {
            if (!document.querySelector('[id^="dropdown-"]:not(.hidden)')) {
                location.reload();
            }
        }, 60000);
    </script>
@endpush
