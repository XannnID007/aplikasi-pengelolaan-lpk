@extends('layouts.admin')

@section('title', 'Verifikasi Dokumen')
@section('page-title', 'Verifikasi Dokumen')

@section('content')
    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-blue-600">{{ $statistik['total'] }}</div>
                    <div class="text-sm text-gray-600">Total Dokumen</div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $statistik['pending'] }}</div>
                    <div class="text-sm text-gray-600">Menunggu Verifikasi</div>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-green-600">{{ $statistik['disetujui'] }}</div>
                    <div class="text-sm text-gray-600">Disetujui</div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-2xl font-bold text-red-600">{{ $statistik['ditolak'] }}</div>
                    <div class="text-sm text-gray-600">Ditolak</div>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter dan Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('admin.dokumen') }}">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Peserta</label>
                    <input type="text" name="search"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Nama peserta..." value="{{ request('search') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui
                        </option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Dokumen</label>
                    <select name="jenis"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Jenis</option>
                        @foreach ($jenisDokumen as $key => $label)
                            <option value="{{ $key }}" {{ request('jenis') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-1"></i>Cari
                    </button>
                    <a href="{{ route('admin.dokumen') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-1"></i>Reset
                    </a>
                </div>
                <div class="flex items-end">
                    <button type="button"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                        onclick="openBatchModal()">
                        <i class="fas fa-check-double mr-1"></i>Batch
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Daftar Dokumen -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-file-alt mr-2"></i>Daftar Dokumen ({{ $dokumen->total() }})
            </h3>
        </div>
        <div class="p-6">
            @if ($dokumen->count() > 0)
                <!-- Bulk Actions -->
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 mr-2">
                        <label for="selectAll" class="text-sm text-gray-700">Pilih Semua</label>
                    </div>
                    <div id="bulkActions" class="hidden flex gap-2">
                        <button
                            class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors"
                            onclick="bulkApprove()">
                            <i class="fas fa-check mr-1"></i>Setujui Terpilih
                        </button>
                        <button
                            class="px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors"
                            onclick="bulkReject()">
                            <i class="fas fa-times mr-1"></i>Tolak Terpilih
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-12 px-6 py-3 text-left">
                                    <input type="checkbox" class="rounded border-gray-300" id="masterCheck">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Peserta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jenis Dokumen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    File</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Upload</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Verifikasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($dokumen as $doc)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" class="rounded border-gray-300 document-check"
                                            value="{{ $doc->id }}">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium mr-3">
                                                {{ strtoupper(substr($doc->user->nama, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $doc->user->nama }}</div>
                                                <div class="text-sm text-gray-500">{{ $doc->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $jenisDokumen[$doc->jenis_dokumen] ?? $doc->jenis_dokumen }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $doc->nama_file }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ number_format(\Illuminate\Support\Facades\Storage::disk('public')->size($doc->file_path) / 1024, 1) }}
                                            KB
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @switch($doc->status_verifikasi)
                                            @case('pending')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Menunggu
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
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $doc->tanggal_upload->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($doc->tanggal_verifikasi)
                                            <div class="text-sm text-gray-900">
                                                {{ $doc->tanggal_verifikasi->format('d/m/Y') }}</div>
                                            <div class="text-sm text-gray-500">{{ $doc->verifiedBy?->nama }}</div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.dokumen.show', $doc->id) }}"
                                                class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                                <i class="fas fa-eye text-xs mr-1"></i>
                                            </a>

                                            @if ($doc->status_verifikasi == 'pending')
                                                <button
                                                    class="inline-flex items-center px-3 py-1 border border-green-300 rounded-md text-sm text-green-700 bg-green-50 hover:bg-green-100 transition-colors"
                                                    onclick="verifikasiDokumen({{ $doc->id }}, 'disetujui')">
                                                    <i class="fas fa-check text-xs"></i>
                                                </button>
                                                <button
                                                    class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md text-sm text-red-700 bg-red-50 hover:bg-red-100 transition-colors"
                                                    onclick="verifikasiDokumen({{ $doc->id }}, 'ditolak')">
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

                <!-- Pagination -->
                <div class="mt-6 flex justify-center">
                    {{ $dokumen->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-file-times text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada dokumen ditemukan</h3>
                    <p class="text-gray-500">Coba ubah filter pencarian atau kriteria lainnya.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Verifikasi -->
    <div id="verifikasiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Verifikasi Dokumen</h3>
            </div>
            <form id="verifikasiForm" method="POST">
                @csrf
                @method('PUT')
                <div class="px-6 py-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Verifikasi</label>
                        <select name="status_verifikasi" id="statusVerifikasi"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="disetujui">Disetujui</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea name="catatan" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            rows="3" placeholder="Berikan catatan verifikasi..."></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeVerifikasiModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Simpan Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Batch Verifikasi -->
    <div id="batchModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Verifikasi Batch</h3>
            </div>
            <form action="{{ route('admin.dokumen.batchVerifikasi') }}" method="POST">
                @csrf
                <div class="px-6 py-4">
                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            <span class="text-sm text-blue-700">Verifikasi semua dokumen yang dipilih dengan status yang
                                sama.</span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Verifikasi</label>
                        <select name="status_verifikasi"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="disetujui">Setujui Semua</option>
                            <option value="ditolak">Tolak Semua</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea name="catatan" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            rows="3" placeholder="Catatan untuk semua dokumen..."></textarea>
                    </div>
                    <input type="hidden" name="dokumen_ids" id="selectedDokumenIds">
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeBatchModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Proses Batch
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Checkbox functionality
        document.getElementById('masterCheck').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.document-check');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBulkActions();
        });

        document.querySelectorAll('.document-check').forEach(checkbox => {
            checkbox.addEventListener('change', toggleBulkActions);
        });

        function toggleBulkActions() {
            const checkedBoxes = document.querySelectorAll('.document-check:checked');
            const bulkActions = document.getElementById('bulkActions');

            if (checkedBoxes.length > 0) {
                bulkActions.classList.remove('hidden');
            } else {
                bulkActions.classList.add('hidden');
            }
        }

        function verifikasiDokumen(id, status) {
            const form = document.getElementById('verifikasiForm');
            const statusSelect = document.getElementById('statusVerifikasi');
            const modal = document.getElementById('verifikasiModal');

            form.action = `/admin/dokumen/${id}/verifikasi`;
            statusSelect.value = status;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeVerifikasiModal() {
            const modal = document.getElementById('verifikasiModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openBatchModal() {
            const checkedBoxes = document.querySelectorAll('.document-check:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);

            if (ids.length === 0) {
                alert('Pilih dokumen yang akan diverifikasi');
                return;
            }

            document.getElementById('selectedDokumenIds').value = ids.join(',');
            const modal = document.getElementById('batchModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeBatchModal() {
            const modal = document.getElementById('batchModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function bulkApprove() {
            const checkedBoxes = document.querySelectorAll('.document-check:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);

            if (ids.length === 0) {
                alert('Pilih dokumen yang akan disetujui');
                return;
            }

            if (confirm(`Setujui ${ids.length} dokumen yang dipilih?`)) {
                processBatch(ids, 'disetujui');
            }
        }

        function bulkReject() {
            const checkedBoxes = document.querySelectorAll('.document-check:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);

            if (ids.length === 0) {
                alert('Pilih dokumen yang akan ditolak');
                return;
            }

            openBatchModal();
        }

        function processBatch(ids, status) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.dokumen.batchVerifikasi') }}';

            form.innerHTML = `
                @csrf
                <input type="hidden" name="dokumen_ids" value="${ids.join(',')}">
                <input type="hidden" name="status_verifikasi" value="${status}">
                <input type="hidden" name="catatan" value="Verifikasi batch oleh admin">
            `;

            document.body.appendChild(form);
            form.submit();
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            const verifikasiModal = document.getElementById('verifikasiModal');
            const batchModal = document.getElementById('batchModal');

            if (e.target === verifikasiModal) {
                closeVerifikasiModal();
            }
            if (e.target === batchModal) {
                closeBatchModal();
            }
        });

        // Auto-refresh setiap 30 detik untuk update real-time
        setInterval(function() {
            if (document.querySelector('.document-check:checked') === null) {
                location.reload();
            }
        }, 30000);
    </script>
@endpush
