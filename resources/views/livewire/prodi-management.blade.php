<div class="p-6 bg-gray-50 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-700">Manajemen Program Studi</h2>

    {{-- 🔍 Pencarian --}}
    <div class="flex mb-4 space-x-3">
        <input wire:model.live="search" type="text" placeholder="Cari Prodi / Jurusan / Fakultas..."
            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">
        <button wire:click="showAddModal"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg">
            + Tambah
        </button>
    </div>

    {{-- 📋 Daftar Prodi --}}
    <table class="w-full border border-gray-300 bg-white rounded-lg shadow-sm">
        <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="py-2 px-3">Nama Prodi</th>
                <th class="py-2 px-3">Jurusan</th>
                <th class="py-2 px-3">Fakultas</th>
                <th class="py-2 px-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prodis as $prodi)
                <tr class="border-t">
                    <td class="py-2 px-3">{{ $prodi->nama_prodi }}</td>
                    <td class="py-2 px-3">{{ $prodi->jurusan }}</td>
                    <td class="py-2 px-3">{{ $prodi->fakultas }}</td>
                    <td class="py-2 px-3 text-center space-x-2">
                        <button wire:click="showDetail({{ $prodi->id }})"
                            class="text-blue-600 hover:underline">Detail</button>
                        <button wire:click="editProdi({{ $prodi->id }})"
                            class="text-yellow-600 hover:underline">Edit</button>
                        <button wire:click="deleteProdi({{ $prodi->id }})"
                            class="text-red-600 hover:underline">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $prodis->links() }}</div>

    {{-- 🟢 Modal Tambah/Edit --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                <h3 class="text-xl font-semibold mb-4">
                    {{ $isEditing ? 'Edit Prodi' : 'Tambah Prodi' }}
                </h3>

                <div class="space-y-3">
                    <input wire:model="nama_prodi" type="text" placeholder="Nama Prodi"
                        class="w-full border rounded-lg p-2">
                    <input wire:model="jurusan" type="text" placeholder="Jurusan"
                        class="w-full border rounded-lg p-2">
                    <input wire:model="fakultas" type="text" placeholder="Fakultas"
                        class="w-full border rounded-lg p-2">
                </div>

                <div class="flex justify-end mt-4 space-x-2">
                    <button wire:click="$set('showModal', false)"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-lg">Batal</button>
                    <button wire:click="{{ $isEditing ? 'updateProdi' : 'saveProdi' }}"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- 🟣 Detail Modal (Assign User ke Prodi) --}}
    @if ($showDetail)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-3xl p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-bold mb-4">Tambah Pengguna ke Prodi</h3>

                <div class="flex space-x-3 mb-3">
                    <select wire:model="userType" class="border rounded-lg p-2">
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen</option>
                        <option value="admin">Admin</option>
                    </select>

                    <input wire:model.live="searchUser" type="text" placeholder="Cari Nama, NIM, NIP, atau Email..."
                        class="flex-grow border rounded-lg p-2">
                </div>

                <div class="max-h-60 overflow-y-auto border rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 text-left">Nama</th>
                                <th class="p-2 text-left">Email</th>
                                <th class="p-2 text-center">Pilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($userResults as $user)
                                <tr class="border-t">
                                    <td class="p-2">{{ $user->name }}</td>
                                    {{-- PERBAIKAN: Menggunakan Nullsafe Operator (?->) untuk mencegah error jika relasi 'user' bernilai null --}}
                                    <td class="p-2">{{ $user->user?->email ?? '-' }}</td>
                                    <td class="text-center">
                                        <input type="checkbox" wire:model="selectedUsers" value="{{ $user->id }}">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-gray-500 py-3">Tidak ada hasil</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end mt-4 space-x-2">
                    <button wire:click="$set('showDetail', false)"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-lg">Tutup</button>
                    <button wire:click="assignUsersToProdi"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg">
                        Tambahkan
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
