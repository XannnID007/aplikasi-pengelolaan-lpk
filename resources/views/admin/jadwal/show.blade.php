@extends('layouts.admin')

@section('title', 'Detail Jadwal')
@section('page-title', 'Detail Jadwal - ' . $jadwal->nama_batch)

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('admin.jadwal') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Jadwal
            </a>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Jadwal Information -->
            <div class="xl:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Jadwal</h3>
                            @switch($jadwal->status)
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
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Batch</label>
                            <p class="text-lg font-semibold text-blue-600">{{ $jadwal->nama_batch }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Kota</label>
                                <p class="text-gray-900">{{ $jadwal->tujuan_kota }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Pekerjaan</label>
                                <p class="text-gray-900">{{ $jadwal->kategori_pekerjaan }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Keberangkatan</label>
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-blue-500 mr-2"></i>
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        {{ $jadwal->tanggal_keberangkatan->format('d F Y') }}</p>
                                    <p class="text-sm text-gray-500">{{ $jadwal->tanggal_keberangkatan->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kapasitas</label>
                            <div class="flex justify-between items-center mb-1">
                                <span
                                    class="text-sm text-gray-600">{{ $jadwal->jumlah_peserta }}/{{ $jadwal->kapasitas_maksimal }}</span>
                                <span
                                    class="text-sm font-semibold">{{ number_format(($jadwal->jumlah_peserta / $jadwal->kapasitas_maksimal) * 100, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                @php
                                    $percentage = ($jadwal->jumlah_peserta / $jadwal->kapasitas_maksimal) * 100;
                                @endphp
                                <div class="h-3 rounded-full 
                                    @if ($percentage >= 100) bg-red-500 
                                    @elseif($percentage >= 80) bg-yellow-500 
                                    @else bg-green-500 @endif"
                                    style="width: {{ $percentage }}%">
                                </div>
                            </div>
                        </div>

                        @if ($jadwal->deskripsi)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <p class="text-gray-900 text-sm">{{ $jadwal->deskripsi }}</p>
                            </div>
                        @endif

                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.jadwal.edit', $jadwal->id) }}"
                                    class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                @if ($jadwal->status !== 'selesai')
                                    <button onclick="selesaikanJadwal()"
                                        class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-check mr-1"></i>Selesai
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tambah Peserta -->
                @if ($pesertaTersedia->count() > 0 && $jadwal->masihTersedia())
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-user-plus mr-2"></i>Tambah Peserta
                            </h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('admin.jadwal.tambahPeserta', $jadwal->id) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilih Peserta (Maks: {{ $jadwal->sisaKapasitas() }})
                                    </label>
                                    <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg">
                                        @foreach ($pesertaTersedia->take(10) as $peserta)
                                            <label
                                                class="flex items-center p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                                <input type="checkbox" name="peserta_ids[]" value="{{ $peserta->id }}"
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                                <div class="flex-1">
                                                    <div class="font-medium text-gray-900">{{ $peserta->nama }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        Skor: {{ number_format($peserta->skor_prioritas, 1) }} |
                                                        {{ $peserta->pekerjaan_diinginkan ?: 'Tidak ditentukan' }}
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Tambah ke Jadwal
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Daftar Peserta -->
            <div class="xl:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-users mr-2"></i>Daftar Peserta ({{ $jadwal->jumlah_peserta }})
                        </h3>
                    </div>
                    <div class="p-6">
                        @if ($jadwal->peserta->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Peserta
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Skor Akhir
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Bahasa Jepang
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal Penempatan
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($jadwal->peserta->sortByDesc('pivot.skor_akhir') as $peserta)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div
                                                            class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-medium mr-3">
                                                            {{ strtoupper(substr($peserta->nama, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="font-medium text-gray-900">{{ $peserta->nama }}
                                                            </div>
                                                            <div class="text-sm text-gray-500">{{ $peserta->email }}</div>
                                                            <div class="text-sm text-gray-500">
                                                                {{ $peserta->pekerjaan_diinginkan ?: '-' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="text-lg font-bold text-blue-600">
                                                            {{ number_format($peserta->pivot->skor_akhir ?? 0, 1) }}
                                                        </div>
                                                        <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                            <div class="bg-blue-600 h-2 rounded-full"
                                                                style="width: {{ min(100, $peserta->pivot->skor_akhir ?? 0) }}%">
                                                            </div>
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
                                                    {{ $peserta->pivot->tanggal_penempatan ? \Carbon\Carbon::parse($peserta->pivot->tanggal_penempatan)->format('d/m/Y') : '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex gap-2">
                                                        <a href="{{ route('admin.peserta.show', $peserta->id) }}"
                                                            class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                                            <i class="fas fa-eye text-xs mr-1"></i>
                                                        </a>
                                                        @if ($jadwal->status !== 'selesai')
                                                            <button
                                                                onclick="hapusPeserta({{ $peserta->id }}, '{{ $peserta->nama }}')"
                                                                class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md text-sm text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                                                                <i class="fas fa-times text-xs"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Peserta</h3>
                                <p class="text-gray-500">Jadwal ini belum memiliki peserta yang terjadwal.</p>
                                @if ($pesertaTersedia->count() > 0 && $jadwal->masihTersedia())
                                    <p class="text-gray-500 mt-2">Gunakan form di samping untuk menambah peserta.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Selesaikan -->
    <div id="selesaikanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Selesaikan Jadwal</h3>
            </div>
            <div class="p-6">
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        <span class="text-sm text-blue-700">Tindakan ini akan mengubah status semua peserta menjadi
                            "Berangkat"</span>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">Apakah Anda yakin ingin menandai jadwal ini sebagai selesai?</p>

                <form action="{{ route('admin.jadwal.selesaikanJadwal', $jadwal->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeSelesaikanModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-check mr-1"></i>Ya, Selesaikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function selesaikanJadwal() {
            const modal = document.getElementById('selesaikanModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeSelesaikanModal() {
            const modal = document.getElementById('selesaikanModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function hapusPeserta(pesertaId, nama) {
            if (confirm(`Hapus "${nama}" dari jadwal ini? Peserta akan dikembalikan ke status terverifikasi.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/jadwal/{{ $jadwal->id }}/peserta/${pesertaId}`;
                form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modal when clicking outside
        document.getElementById('selesaikanModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSelesaikanModal();
            }
        });

        // Limit checkbox selection based on remaining capacity
        const checkboxes = document.querySelectorAll('input[name="peserta_ids[]"]');
        const maxSelection = {{ $jadwal->sisaKapasitas() }};

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedCount = document.querySelectorAll('input[name="peserta_ids[]"]:checked')
                .length;

                if (checkedCount >= maxSelection) {
                    checkboxes.forEach(cb => {
                        if (!cb.checked) {
                            cb.disabled = true;
                        }
                    });
                } else {
                    checkboxes.forEach(cb => {
                        cb.disabled = false;
                    });
                }
            });
        });
    </script>
@endpush
