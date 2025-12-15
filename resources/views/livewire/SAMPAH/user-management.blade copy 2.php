<div class="p-6 mb-6 bg-gray-50" x-data="{ show: @entangle('showModal') }">
    <h2 class="text-2xl font-bold mb-4 text-gray-700">Manajemen Pengguna</h2>

    {{-- 🔍 Pencarian dan Filter --}}
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <div class="ml-auto flex gap-2">
            <button wire:click="showAddModal('admin')" class="bg-red-500 text-white px-3 py-2 rounded-lg">+
                Admin</button>
            <button wire:click="showAddModal('dosen')" class="bg-green-500 text-white px-3 py-2 rounded-lg">+
                Dosen</button>
            <button wire:click="showAddModal('mahasiswa')" class="bg-blue-500 text-white px-3 py-2 rounded-lg">+
                Mahasiswa</button>
        </div>
    </div>

    {{-- ========== PENCARIAN & TAB FILTER (ADMIN, DOSEN, MAHASISWA) ========== --}}

    <div class="mb-6 p-4 bg-white rounded-lg shadow-md border border-gray-100">
        <div class="flex border-b mb-4 overflow-x-auto">
            {{-- Tab Semua --}}
            <button wire:click="filterBy('')"
                class="{{ isset($filter) && $filter == '' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2">
                <i class="fas fa-users mr-2"></i> Semua Pengguna (<span id="count-all">{{ $totalUsers }}</span>)
            </button>
            {{-- Tab Admin --}}
            <button wire:click="filterBy('admin')"
                class="{{ isset($filter) && $filter == 'admin' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2">
                <i class="fas fa-crown mr-2"></i> Admin (<span id="count-admin">{{ $totalAdmins }}</span>)
            </button>
            {{-- Tab Dosen --}}
            <button wire:click="filterBy('dosen')"
                class="{{ isset($filter) && $filter == 'dosen' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2">
                <i class="fas fa-chalkboard-teacher mr-2"></i> Dosen (<span id="count-dosen">{{ $totalDosens }}</span>)
            </button>
            {{-- Tab Mahasiswa --}}
            <button wire:click="filterBy('mahasiswa')"
                class="{{ isset($filter) && $filter == 'mahasiswa' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2">
                <i class="fas fa-user-graduate mr-2"></i> Mahasiswa (<span
                    id="count-mahasiswa">{{ $totalMahasiswas }}</span>)
            </button>
        </div>

        <div class="flex flex-col sm:flex-row items-start space-y-4 sm:space-y-0 sm:space-x-3">
            <input wire:model.live="search" type="text" name="search" value="{{ $search ?? '' }}"
                placeholder="Cari Nama, Email, atau ID Pengguna..."
                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">

            <div x-data="{ open: false, selectedName: @entangle('selectedProdiName').live }" class="relative w-full sm:w-auto min-w-[250px]">
                <input type="text" placeholder="Filter berdasarkan Program Studi..." x-model="selectedName"
                    wire:model.live="prodiSearchQuery" @focus="open = true" @click.outside="open = false"
                    @keydown.escape.window="open = false" @keydown.enter.prevent="open = false"
                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm"
                    :class="{ 'pr-10': selectedName }">

                @if (strlen($prodiSearchQuery) >= 0 && count($prodiSearchResults) > 0)
                    <div x-show="open" x-cloak
                        class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-60 overflow-y-auto">

                        @forelse ($prodiSearchResults as $prodi)
                            <div wire:key="prodi-{{ $prodi->id }}"
                                wire:click="selectProdiForFilter({{ $prodi->id }})" @click="open = false"
                                class="px-4 py-2 cursor-pointer hover:bg-indigo-50 text-gray-800 transition duration-150">
                                <span class="font-medium">{{ $prodi->nama_prodi }}</span>
                                <span class="text-xs text-gray-500">({{ $prodi->fakultas }}) - ID:
                                    {{ $prodi->id }}</span>
                            </div>
                        @empty
                            <div class="px-4 py-2 text-gray-500 italic">Tidak ada prodi ditemukan.</div>
                        @endforelse

                    </div>
                @endif

                {{-- Tombol Reset Filter (Hanya tampil jika prodi sudah dipilih) --}}
                @if ($selectedProdiId)
                    <button wire:click="resetProdiFilter" @click="selectedName = ''; open = false"
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-red-500 hover:text-red-700 p-1">
                        <i class="fas fa-times-circle"></i>
                    </button>
                @endif
            </div>

            {{-- Tombol Reset Filter Utama --}}
            <div class="flex space-x-3 w-full sm:w-auto">
                <button wire:click="resetFilters"
                    class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-150 shadow-md whitespace-nowrap">
                    <i class="fas fa-sync-alt mr-1"></i> Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- ========== HASIL TABEL PENGGUNA (AJAX CONTAINER) ========== --}}
    <div class="bg-white shadow-lg rounded-lg overflow-hidden" id="user-results-container">

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Role</th>
                        {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Pada</th> --}}
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody wire:loading.class="opacity-50" wire:target="search,filterBy"
                    class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        @php
                            $role = $user->admin
                                ? 'Admin'
                                : ($user->dosen
                                    ? 'Dosen'
                                    : ($user->mahasiswa
                                        ? 'Mahasiswa'
                                        : 'Tidak Diketahui'));

                            // Ambil detail user dari relasi sesuai role
                            $detail = $user->admin ?? ($user->dosen ?? $user->mahasiswa);
                        @endphp

                        <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50"
                            data-user-id="{{ $user->id }}">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $detail->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $detail->prodi->nama_prodi ?? '-' }}
                            </td>

                            {{-- Role --}}
                            <td class="px-6 py-4 text-center text-sm">
                                @switch($role)
                                    @case('Admin')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ $role }}
                                        </span>
                                    @break

                                    @case('Dosen')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $role }}
                                        </span>
                                    @break

                                    @case('Mahasiswa')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $role }}
                                        </span>
                                    @break

                                    @default
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $role }}
                                        </span>
                                @endswitch
                            </td>

                            {{-- <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $user->created_at->format('d M Y') }}
                                </td> --}}

                            <td class="px-6 py-4 text-center text-sm space-x-2">
                                @if (Auth::user()?->admin)
                                    <button wire:click="editUser({{ $user->id }})"
                                        class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded">
                                        Edit
                                    </button>

                                    @if (Auth::id() !== $user->id)
                                        {{-- TOMBOL DELETE BARU --}}
                                        <button wire:click="confirmDelete({{ $user->id }})"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                            Hapus
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada pengguna ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="p-4" id="pagination-links-container">
                    {{ $users->links() }}
                </div>

                {{-- Loading indicator --}}
                <div wire:loading.flex wire:target="search,filterBy" class="justify-center items-center py-4">
                    <div class="flex items-center space-x-2 text-gray-500">
                        <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Memuat data...</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 💬 Modal --}}
        @if ($showModal)
            <div x-data="{ show: @entangle('showModal') }">
                {{-- 💬 Modal --}}
                @if ($showModal)
                    <div x-show="show" x-transition.opacity.duration.200ms
                        class="fixed inset-0 bg-gray-900 bg-opacity-40 flex justify-center items-center z-50">
                        <div @click.outside="show = false"
                            class="bg-white rounded-lg p-6 w-full max-w-lg transform transition-all duration-200 ease-out scale-100">
                            <h3 class="text-lg font-semibold mb-4 text-gray-800">
                                {{ $isEditing ? 'Edit ' . ucfirst($roleType) : 'Tambah ' . ucfirst($roleType) }}
                            </h3>

                            <div class="space-y-4">
                                {{-- Email Input --}}
                                <div>
                                    <input wire:model.lazy="email" type="email" placeholder="Email"
                                        class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('email')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Password Input --}}
                                <div>
                                    <input wire:model.lazy="password" type="password"
                                        placeholder="Password (Kosongkan jika tidak ingin diubah)"
                                        class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('password')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Nama Input --}}
                                <div>
                                    <input wire:model.lazy="name" type="text" placeholder="Nama"
                                        class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('name')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if ($roleType === 'dosen')
                                    {{-- NIP Input (Dosen) --}}
                                    <div>
                                        <input wire:model.lazy="nip" type="text" placeholder="NIP"
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('nip')
                                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @elseif($roleType === 'mahasiswa')
                                    {{-- NIM Input (Mahasiswa) --}}
                                    <div>
                                        <input wire:model.lazy="nim" type="text" placeholder="NIM"
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('nim')
                                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Tahun Masuk Input (Mahasiswa) --}}
                                    <div>
                                        <input wire:model.lazy="tahun_masuk" type="number" placeholder="Tahun Masuk"
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('tahun_masuk')
                                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif

                                {{-- Autocomplete Prodi Search (Pengganti input prodi_id) --}}
                                <div class="relative" x-data="{
                                    // Buka dropdown jika ada hasil atau jika prodi_id belum terpilih dan input di-fokus
                                    open: false,
                                    prodiResultsCount: $wire.entangle('prodi_results').defer.map(r => r.length),
                                }" @focusin="open = true"
                                    @focusout="setTimeout(() => open = false, 200)" {{-- Memberi waktu klik sebelum tutup --}}>
                                    
                                    <!-- Input Pencarian Nama Prodi -->
                                    <input wire:model.live.debounce.300ms="prodi_name_search" type="text"
                                        placeholder="Cari Nama Program Studi"
                                        class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    
                                    <!-- Display selected Prodi ID for visual confirmation (optional) -->
                                    @if ($prodi_id)
                                        <p class="text-xs text-gray-500 mt-1">
                                            Prodi ID terpilih: <span
                                                class="font-medium text-indigo-600">{{ $prodi_id }}</span>
                                        </p>
                                    @endif

                                    <!-- Hidden Input untuk menampung ID yang sebenarnya (penting untuk validasi) -->
                                    <input wire:model.defer="prodi_id" type="hidden">

                                    <!-- Pesan Error untuk prodi_id -->
                                    @error('prodi_id')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                    @error('prodi_name_search')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror

                                    <!-- Dropdown Hasil Pencarian -->
                                    <div x-show="open && prodiResultsCount > 0" x-cloak @mousedown.prevent
                                        {{-- Mencegah blur saat klik, agar elemen dropdown tetap ada --}}
                                        class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                                        @forelse ($prodi_results as $prodi)
                                            <div wire:key="prodi-{{ $prodi['id'] }}"
                                                wire:click="selectProdi({{ $prodi['id'] }}, '{{ $prodi['nama_prodi'] }}')"
                                                @click="open = false"
                                                class="p-2 cursor-pointer hover:bg-indigo-600 hover:text-white transition duration-150 ease-in-out text-sm">
                                                <span class="font-medium">{{ $prodi['nama_prodi'] }}</span>
                                                <span class="text-xs text-gray-400 hover:text-white float-right">ID:
                                                    {{ $prodi['id'] }}</span>
                                            </div>
                                        @empty
                                            @if (strlen($prodi_name_search) > 0 && !$prodi_id)
                                                <p class="p-2 text-sm text-gray-500">Tidak ada Prodi yang ditemukan.</p>
                                            @endif
                                        @endforelse
                                    </div>

                                    <!-- Pesan jika tidak ada hasil setelah mengetik -->
                                    @if (strlen($prodi_name_search) >= 2 && empty($prodi_results) && !$prodi_id)
                                        <p x-show="!open" class="text-sm text-gray-500 mt-1">Tidak ada Prodi yang cocok.
                                        </p>
                                    @endif
                                </div>
                                {{-- End Autocomplete --}}
                            </div>

                            <div class="flex justify-end mt-5 space-x-2">
                                <button @click="show = false" type="button"
                                    class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded transition">Batal</button>
                                <button wire:click.prevent="{{ $isEditing ? 'updateUser' : 'saveUser' }}"
                                    class="bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded transition disabled:opacity-50"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="{{ $isEditing ? 'updateUser' : 'saveUser' }}">
                                        {{ $isEditing ? 'Perbarui' : 'Simpan' }}
                                    </span>
                                    <span wire:loading
                                        wire:target="{{ $isEditing ? 'updateUser' : 'saveUser' }}">Memproses...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif


            </div>
        @endif


        {{-- MODAL KONFIRMASI HAPUS BARU --}}
        @if ($showDeleteConfirmation)
            <div x-show="$wire.showDeleteConfirmation" x-transition.opacity.duration.200ms
                class="fixed inset-0 bg-gray-900 bg-opacity-40 flex justify-center items-center z-50">
                <div @click.outside="$wire.cancelDelete()"
                    class="bg-white rounded-lg p-6 w-full max-w-sm transform transition-all duration-200 ease-out scale-100">

                    {{-- Header --}}
                    <h3 class="text-xl font-bold mb-2 text-red-600">Konfirmasi Hapus</h3>

                    {{-- Body Pesan --}}
                    <p class="text-gray-700 mb-6">
                        Apakah Anda yakin ingin menghapus **{{ $userEmailToDelete }}**?
                        Tindakan ini tidak dapat dibatalkan.
                    </p>

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end space-x-3">
                        <button wire:click="cancelDelete"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition duration-150">
                            Batal
                        </button>
                        <button wire:click="deleteUser"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-150">
                            Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
