





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
            <div x-show="show" x-transition.opacity.duration.200ms
                class="fixed inset-0 bg-gray-900 bg-opacity-40 flex justify-center items-center z-50">
                <div @click.outside="show = false"
                    class="bg-white rounded-lg p-6 w-full max-w-lg transform transition-all duration-200 ease-out scale-100">
                    <h3 class="text-lg font-semibold mb-4">
                        {{ $isEditing ? 'Edit ' . ucfirst($roleType) : 'Tambah ' . ucfirst($roleType) }}
                    </h3>

                    <div class="space-y-3">
                        <input wire:model.lazy="email" type="email" placeholder="Email"
                            class="w-full border rounded-lg px-3 py-2">
                        <input wire:model.lazy="password" type="password" placeholder="Password"
                            class="w-full border rounded-lg px-3 py-2">
                        <input wire:model.lazy="name" type="text" placeholder="Nama"
                            class="w-full border rounded-lg px-3 py-2">

                        @if ($roleType === 'dosen')
                            <input wire:model.lazy="nip" type="text" placeholder="NIP"
                                class="w-full border rounded-lg px-3 py-2">
                        @elseif($roleType === 'mahasiswa')
                            <input wire:model.lazy="nim" type="text" placeholder="NIM"
                                class="w-full border rounded-lg px-3 py-2">
                            <input wire:model.lazy="tahun_masuk" type="number" placeholder="Tahun Masuk"
                                class="w-full border rounded-lg px-3 py-2">
                        @endif
                        <input wire:model.lazy="prodi_id" type="text" placeholder="Program Studi"
                            class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div class="flex justify-end mt-5 space-x-2">
                        <button @click="show = false" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                        <button wire:click="{{ $isEditing ? 'updateUser' : 'saveUser' }}"
                            class="bg-indigo-600 text-white px-4 py-2 rounded">
                            {{ $isEditing ? 'Perbarui' : 'Simpan' }}
                        </button>
                    </div>
                </div>
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