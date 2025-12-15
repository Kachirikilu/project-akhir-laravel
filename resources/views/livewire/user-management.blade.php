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
        <div class="flex border-b mb-4 justify-between items-end">

            <div class="flex space-x-4 overflow-x-auto">
                {{-- Tab Semua --}}
                <button wire:click="filterBy('')"
                    class="{{ isset($filter) && $filter == '' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                   tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
                    <i class="fas fa-users mr-2"></i> Semua Pengguna (<span id="count-all">{{ $totalUsers }}</span>)
                </button>
                {{-- Tab Admin --}}
                <button wire:click="filterBy('admin')"
                    class="{{ isset($filter) && $filter == 'admin' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                   tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
                    <i class="fas fa-crown mr-2"></i> Admin (<span id="count-admin">{{ $totalAdmins }}</span>)
                </button>
                {{-- Tab Dosen --}}
                <button wire:click="filterBy('dosen')"
                    class="{{ isset($filter) && $filter == 'dosen' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                   tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
                    <i class="fas fa-chalkboard-teacher mr-2"></i> Dosen (<span
                        id="count-dosen">{{ $totalDosens }}</span>)
                </button>
                {{-- Tab Mahasiswa --}}
                <button wire:click="filterBy('mahasiswa')"
                    class="{{ isset($filter) && $filter == 'mahasiswa' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                   tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
                    <i class="fas fa-user-graduate mr-2"></i> Mahasiswa (<span
                        id="count-mahasiswa">{{ $totalMahasiswas }}</span>)
                </button>
            </div>
            {{-- Kontrol Jumlah Data Per Halaman (Ditempatkan di kanan) --}}
            <div class="flex items-center ml-4 pb-4">
                <label class="text-sm font-medium text-gray-500 mr-2 whitespace-nowrap">Tampilkan:</label>
                <div x-data="{ open: false, selected: @entangle('perPage').live }" class="relative w-20 **z-20**" @click.away="open = false">
                    <button type="button" @click="open = !open"
                        class="flex items-center justify-between border border-gray-300 rounded-md shadow-sm 
                       py-1 px-2 text-sm w-full bg-white transition duration-150 hover:border-indigo-500">
                        <span x-text="selected">5</span>
                        <svg class="h-4 w-4 ml-1 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <ul x-show="open" x-transition:enter="transition ease-out duration-100" x-cloak
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute **z-10** mt-1 w-full rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                        role="menu" aria-orientation="vertical" tabindex="-1">
                        @foreach ([3, 5, 8, 10, 15, 25, 50, 100] as $option)
                            <li wire:key="perpage-{{ $option }}"
                                @click="selected = {{ $option }}; open = false"
                                class="text-gray-700 block px-3 py-1 text-sm cursor-pointer hover:bg-indigo-500 hover:text-white"
                                :class="{ 'bg-indigo-100 font-semibold text-indigo-700': selected == {{ $option }} }">
                                {{ $option }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <span class="text-sm font-medium text-gray-500 ml-2">baris</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-start space-y-4 sm:space-y-0 sm:space-x-3">
            <input wire:model.live="search" type="text" name="search" value="{{ $search ?? '' }}"
                placeholder="Cari Nama, Email, atau ID Pengguna..."
                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">

            <div x-data="{ open: false, selectedName: @entangle('selectedProdiName').live }" class="relative w-full sm:w-auto min-w-[250px]">
                <input type="text" placeholder="Filter berdasarkan Program Studi..." x-model="selectedName"
                    wire:model.live="prodiSearchQuery" @focus="open = true; $event.target.select()"
                    @click.outside="open = false" @keydown.escape.window="open = false"
                    @keydown.enter.prevent="open = false"
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
                @if ($users->hasPages())
                    <div class="p-4" id="pagination-links-container">
                        {{ $users->links() }}
                    </div>
                @endif

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

        {{-- 💬 Modal Pengguna (Livewire Component - Blade View) --}}
        {{-- @if ($showModal) --}}
            {{-- <div x-data="{ show: @entangle('showModal') }" x-cloak> --}}
            <div x-cloak>

                <div x-show="show"
                    class="fixed inset-0 bg-gray-900 bg-opacity-40 flex justify-center items-center z-50">

                    <div @click.outside="show = false"
                        class="bg-white rounded-lg w-full max-w-4xl lg:w-4/5 transform transition-all duration-200 ease-out scale-100 max-h-[90vh] flex flex-col">

                        {{-- 1. Header Modal (Tetap di Atas) --}}
                        <div class="p-6 pb-4 border-b">
                            <h3 class="text-xl font-semibold text-gray-800">
                                {{ $isEditing ? 'Edit ' . ucfirst($roleType) : 'Tambah ' . ucfirst($roleType) }}
                            </h3>
                        </div>

                        {{-- 2. Konten Formulir (Bisa di-Scroll) --}}
                        <div class="p-6 pb-flex-1 overflow-y-auto space-y-6">
                            <form wire:submit.prevent="{{ $isEditing ? 'updateUser' : 'saveUser' }}" id="userForm">

                                {{-- ****************************************************** --}}
                                {{-- 1. ACCOUNT INFORMATION (EMAIL & PASSWORD) --}}
                                {{-- ****************************************************** --}}
                                <div class="p-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">
                                    <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Account Information</h4>

                                    {{-- 📧 Email Input --}}
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email <span
                                                class="text-red-500">*</span></label>
                                        <input wire:model.lazy="email" type="email" id="email"
                                            placeholder="contoh@domain.com"
                                            class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('email')
                                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- 🔒 Password Input --}}
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700">Password
                                            @if (!$isEditing)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <input wire:model.lazy="password" type="password" id="password"
                                            placeholder="{{ $isEditing ? 'Kosongkan jika tidak ingin diubah' : 'Masukkan Password' }}"
                                            class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('password')
                                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- ****************************************************** --}}
                                {{-- 2. PERSONAL INFORMATION (SESUAI ROLE) --}}
                                {{-- ****************************************************** --}}
                                <div class="p-4 mt-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">
                                    <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Personal Information</h4>

                                    {{-- 👤 Nama Input --}}
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name
                                            <span class="text-red-500">*</span></label>
                                        <input wire:model.lazy="name" type="text" id="name"
                                            placeholder="Masukkan Nama Lengkap"
                                            class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('name')
                                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Input Khusus Berdasarkan Role Type --}}
                                    @if ($roleType === 'dosen')
                                        {{-- 🆔 NIP Input (Dosen) --}}
                                        <div>
                                            <label for="nip" class="block text-sm font-medium text-gray-700">Lecturer
                                                ID (NIP) <span class="text-red-500">*</span></label>
                                            <input wire:model.lazy="nip" type="text" id="nip"
                                                placeholder="Nomor Induk Pegawai (NIP)"
                                                class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                                            @error('nip')
                                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @elseif($roleType === 'mahasiswa')
                                        {{-- 🔢 NIM Input (Mahasiswa) --}}
                                        <div>
                                            <label for="nim" class="block text-sm font-medium text-gray-700">Student
                                                ID (NIM) <span class="text-red-500">*</span></label>
                                            <input wire:model.lazy="nim" type="text" id="nim"
                                                placeholder="Nomor Induk Mahasiswa (NIM)"
                                                class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                                            @error('nim')
                                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- 📅 Tahun Masuk Input (Mahasiswa) --}}
                                        <div>
                                            <label for="tahun_masuk" class="block text-sm font-medium text-gray-700">Entry
                                                Year <span class="text-red-500">*</span></label>
                                            <input wire:model.lazy="tahun_masuk" type="number" id="tahun_masuk"
                                                placeholder="Contoh: 2020"
                                                class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                                            @error('tahun_masuk')
                                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif

                                    {{-- 🎓 Autocomplete Prodi Search --}}
                                    <div class="relative" x-data="{
                                        open: false,
                                        {{-- prodiResultsCount: $wire.entangle('prodi_results').defer.map(r => r.length), --}}
                                    }" {{-- @focusin="open = true"
                                        @focusout="setTimeout(() => open = false, 200)" --}}>

                                        <label for="prodi_search" class="block text-sm font-medium text-gray-700">Program
                                            Studi <span class="text-red-500">*</span></label>

                                        {{-- KONTROL INPUT DAN TOMBOL RESET --}}
                                        <div class="relative mt-1">
                                            <input autocomplete="off" wire:model.live.debounce.300ms="prodi_name_search"
                                                type="text" @focus="open = true; $event.target.select()"
                                                @click.outside="open = false" @keydown.escape.window="open = false"
                                                @keydown.enter.prevent="open = false" id="prodi_search"
                                                placeholder="Cari Nama Program Studi"
                                                class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10">

                                            {{-- Tombol Reset (Hanya muncul jika ada teks atau prodi sudah dipilih) --}}
                                            @if ($prodi_id || strlen($prodi_name_search) > 0)
                                                <button wire:click.prevent="resetProdiInput" type="button"
                                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition duration-150"
                                                    title="Bersihkan Pilihan">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                        {{-- AKHIR KONTROL INPUT --}}
                                        @if ($prodi_id && $prodi_name_search)
                                            <p class="text-xs text-gray-500 mt-1">
                                                Prodi Terpilih: <span
                                                    class="font-medium text-indigo-600">{{ $prodi_name_search }} (ID:
                                                    {{ $prodi_id }})</span>
                                            </p>
                                        @endif

                                        <input wire:model.defer="prodi_id" type="hidden">

                                        @error('prodi_id')
                                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                        @enderror
                                        @error('prodi_name_search')
                                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                        @enderror

                                        {{-- <div x-show="open && prodiResultsCount >= 0" x-cloak @mousedown.prevent --}}
                                        <div x-show="open" x-cloak
                                            class="relative z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                                            @forelse ($prodi_results as $prodi)
                                                <div wire:key="prodi-{{ $prodi['id'] }}"
                                                    wire:click="selectProdi({{ $prodi['id'] }}, '{{ $prodi['nama_prodi'] }}')"
                                                    @click="open = false"
                                                    class="px-4 py-2 cursor-pointer hover:bg-indigo-50 text-gray-800 transition duration-150">
                                                    <span class="font-medium">{{ $prodi['nama_prodi'] }}</span>
                                                    <span
                                                        class="text-xs text-gray-500 hover:text-white float-right">{{ $prodi['fakultas'] }}
                                                        - ID:
                                                        {{ $prodi['id'] }}</span>
                                                </div>
                                            @empty
                                                @if (strlen($prodi_name_search) > 0 && !$prodi_id)
                                                    <p class="p-2 text-sm text-gray-500">Tidak ada Prodi yang ditemukan.
                                                    </p>
                                                @endif
                                            @endforelse
                                        </div>

                                        @if (strlen($prodi_name_search) >= 2 && empty($prodi_results) && !$prodi_id)
                                            <p x-show="!open" class="text-sm text-gray-500 mt-1">Tidak ada Prodi yang
                                                cocok.
                                            </p>
                                        @endif
                                    </div>
                                    {{-- End Autocomplete --}}
                                </div>

                                {{-- 3. Footer/Tombol --}}
                                <div
                                    class="p-4 mt-4 border-t bg-gray-50 rounded-b-lg gap-4">

                                    {{-- 💡 Bagian Kiri (Error & Tips) --}}
                                    <div class="flex-1 text-xs text-gray-600 space-y-3">

                                        {{-- ⚠️ 1. Error Validation (Paling Atas) --}}
                                        @if ($errors->any())
                                            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                                <h4 class="font-semibold text-red-700 mb-2">⚠️ Ada beberapa kesalahan:</h4>
                                                <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        {{-- 💡 2. Tips (Di bawah Error) --}}
                                        <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm">
                                            <span class="font-semibold text-gray-700 block mb-1">💡 Tips:</span>
                                            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                                <li>Kosongkan kolom **password** untuk mempertahankan password
                                                    lama (saat edit).</li>
                                                <li>Pastikan semua kolom **wajib diisi** dengan benar.</li>
                                                <li>Perubahan akan tersimpan segera setelah formulir dikirim.</li>
                                            </ul>
                                        </div>

                                                  {{-- 💾 3. Tombol Aksi (Di sebelah Kanan, diatur ke flex-col-reverse agar Batal di atas Simpan di HP, namun Flex-row di Desktop) --}}
                                    <div
                                        class="flex flex-col-reverse sm:flex-row sm:justify-end sm:items-start gap-2 w-full sm:w-auto mt-auto">

                                        {{-- Tombol submit --}}
                                        <button type="submit"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition w-full sm:w-auto disabled:opacity-50"
                                            wire:loading.attr="disabled">
                                            <span wire:loading.remove
                                                wire:target="{{ $isEditing ? 'updateUser' : 'saveUser' }}">
                                                {{ $isEditing ? 'Perbarui' : 'Simpan' }}
                                            </span>
                                            <span wire:loading wire:target="{{ $isEditing ? 'updateUser' : 'saveUser' }}">
                                                Memproses...
                                            </span>
                                        </button>

                                        {{-- Tombol Batal --}}
                                        <button @click.prevent="show = false" type="button"
                                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition w-full sm:w-auto">
                                            Batal
                                        </button>
                                    </div>
                                    </div>

                          

                                </div>
                            </form>
                        </div>


                    </div>
                </div>
            </div>
        {{-- @endif --}}


        {{-- MODAL KONFIRMASI HAPUS BARU --}}
        @if ($showDeleteConfirmation)
            <div x-show="$wire.showDeleteConfirmation" x-transition.opacity.duration.200ms x-cloak
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
