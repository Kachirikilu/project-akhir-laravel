<div class="p-6 mt-6 bg-gray-50" x-data="{ show: @entangle('showModal') }">
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
                            <div wire:key="prodi-{{ $prodi->id }}" wire:click="selectProdi({{ $prodi->id }})"
                                @click="open = false"
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
        
    </div>
</div>