    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f9;
        }

        /* Gaya dasar untuk Pagination */
        .pagination-container .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
        }
    </style>

    <div class="container mx-auto p-4">

        {{-- ============================== STATISTIK RINGKASAN ============================== --}}
        <h2 class="text-2xl font-extrabold text-gray-900 mb-4 border-b pb-2">Dashboard Keluhan</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

            {{-- Total Keseluruhan --}}
            <div
                class="bg-indigo-100 p-6 rounded-xl shadow-lg border-l-4 border-indigo-600 flex justify-between items-start transition duration-300 hover:shadow-xl hover:scale-[1.01]">
                <div class="space-y-1">
                    <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider">Total Keseluruhan</p>
                    <p class="text-4xl font-extrabold text-indigo-900">{{ number_format($totalKeluhan) }}</p>
                </div>
                <i class="fas fa-list text-5xl text-indigo-400 opacity-30 mt-1 ml-4"></i>
            </div>

            {{-- Sudah Selesai --}}
            <div
                class="bg-green-100 p-6 rounded-xl shadow-lg border-l-4 border-green-600 flex justify-between items-start transition duration-300 hover:shadow-xl hover:scale-[1.01]">
                <div class="space-y-1">
                    <p class="text-sm font-medium text-green-600 uppercase tracking-wider">Selesai (Maintenance
                        Berhasil)</p>
                    <p class="text-4xl font-extrabold text-green-900">{{ number_format($keluhanSelesai) }}</p>
                </div>
                <i class="fas fa-check-circle text-5xl text-green-400 opacity-30 mt-1 ml-4"></i>
            </div>

            {{-- Sedang Diproses (BARU) --}}
            <div
                class="bg-blue-100 p-6 rounded-xl shadow-lg border-l-4 border-blue-600 flex justify-between items-start transition duration-300 hover:shadow-xl hover:scale-[1.01]">
                <div class="space-y-1">
                    <p class="text-sm font-medium text-blue-600 uppercase tracking-wider">Sedang Diproses (Dikerjakan)
                    </p>
                    <p class="text-4xl font-extrabold text-blue-900">{{ number_format($keluhanDiproses) }}</p>
                </div>
                <i class="fas fa-tools text-5xl text-blue-400 opacity-30 mt-1 ml-4"></i>
            </div>

            {{-- Belum Selesai --}}
            <div
                class="bg-yellow-100 p-6 rounded-xl shadow-lg border-l-4 border-yellow-600 flex justify-between items-start transition duration-300 hover:shadow-xl hover:scale-[1.01]">
                <div class="space-y-1">
                    <p class="text-sm font-medium text-yellow-600 uppercase tracking-wider">Belum Selesai (Perlu Tindak
                        Lanjut)</p>
                    <p class="text-4xl font-extrabold text-yellow-900">{{ number_format($keluhanBelumSelesai) }}</p>
                </div>
                <i class="fas fa-exclamation-triangle text-5xl text-yellow-400 opacity-30 mt-1 ml-4"></i>
            </div>
        </div>

        @if (Auth::check() && !$keluhanSayaBelumSelesaiList->isEmpty())
            <h3 class="text-xl font-bold text-yellow-700 mb-3">Komentar Saya (Belum)</h3>

            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Nama Pengguna</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Nama Tempat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Komentar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Tanggal</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{-- Ganti $keluhanBelumSelesaiList menjadi $keluhanSayaBelumSelesaiList --}}
                            @foreach ($keluhanSayaBelumSelesaiList as $keluh)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $keluh->id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $keluh->user->name ?? ($keluh->nama_pengguna ?? '-') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $keluh->nama_tempat ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @php
                                            $status = 'Belum';
                                            $class = 'bg-yellow-100 text-yellow-800';

                                            if ($keluh->perjalanan_id) {
                                                if ($keluh->perjalanan && $keluh->perjalanan->selesai) {
                                                    $status = 'Selesai';
                                                    $class = 'bg-green-100 text-green-800';
                                                } else {
                                                    $status = 'Diproses';
                                                    $class = 'bg-blue-100 text-blue-800';
                                                }
                                            }
                                        @endphp
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $class }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $keluh->komentar ? (strlen($keluh->komentar) > 25 ? substr($keluh->komentar, 0, 25) . '...' : $keluh->komentar) : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $keluh->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">

                                            <a href="{{ route('keluh_pengguna.show', $keluh->id) }}"
                                                class="flex-1 text-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 focus:ring focus:ring-indigo-300 transition duration-150">
                                                Lihat
                                            </a>

                                            @auth
                                                @if ($keluh->user_id == Auth::user()?->id || Auth::user()?->admin)
                                                    <form action="{{ route('keluh_pengguna.destroy', $keluh->id) }}"
                                                        method="POST" class="flex-1"
                                                        onsubmit="return confirm('Yakin ingin menghapus keluhan ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition duration-150">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            @endauth
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4 p-4 pagination-container">
                {{ $keluhanSayaBelumSelesaiList->links() }}
            </div>
        @endif

        @if (!$keluhanBelumSelesaiList->isEmpty() && Auth::user()?->admin)
        @endif

        {{-- ============================== PENCARIAN & TAB FILTER ============================== --}}
        <div class="mb-6 p-4 bg-white rounded-xl shadow-md border border-gray-100">
            <div class="flex flex-col sm:flex-row items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Filter Data Keluhan</h3>
                <a href="{{ route('keluh_pengguna.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition shadow-md mt-3 sm:mt-0">
                    <i class="fas fa-plus mr-1"></i> Tambah Keluhan
                </a>
            </div>

            <div class="flex border-b mb-4">
                <div class="flex border-b mb-4 overflow-x-auto"> {{-- Tambahkan overflow-x-auto agar tab tidak menumpuk di layar kecil --}}
                    <button id="mode-pending" data-mode="pending"
                        class="tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 border-indigo-500 text-indigo-700 whitespace-nowrap">
                        <i class="fas fa-hourglass-half mr-2"></i> Belum ({{ $keluhanBelumSelesai }})
                    </button>
                    <button id="mode-processing" data-mode="processing"
                        class="tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 border-transparent text-gray-500 hover:text-indigo-700 whitespace-nowrap">
                        <i class="fas fa-tools mr-2"></i> Sedang Diproses ({{ $keluhanDiproses }})
                    </button>
                    <button id="mode-complete" data-mode="complete"
                        class="tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 border-transparent text-gray-500 hover:text-indigo-700 whitespace-nowrap">
                        <i class="fas fa-check-double mr-2"></i> Sudah Selesai ({{ $keluhanSelesai }})
                    </button>
                </div>
            </div>

            <script>
                // Inisialisasi mode dan tab yang aktif saat halaman dimuat
                document.addEventListener('DOMContentLoaded', function() {
                    const pendingTab = document.getElementById('mode-pending');
                    if (pendingTab) {
                        pendingTab.click(); // Aktifkan tab Pending secara default
                    }
                });
            </script>

            <form id="search-form" class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-3">
                <input type="text" id="search-input" name="search"
                    placeholder="Cari Nama Pengguna, Tempat, atau Komentar dalam mode Belum..."
                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">
            </form>
        </div>

        {{-- ============================== KONTEN HASIL (TABBED & AJAX) ============================== --}}
        <div id="result-container">

            <div id="ajax-results" class="mb-6" style="display: none;">
            </div>

            <div id="default-results" class="space-y-6">

                {{-- 1. Tabel BELUM (Not Default) - KONTEN INLINE --}}
                <div id="table-pending" class="table-content block">
                    <h3 class="text-xl font-bold text-yellow-700 mb-3">Data Keluhan Belum</h3>

                    @if ($keluhanBelumSelesaiList->isEmpty())
                        <div class="bg-white shadow-lg rounded-lg overflow-hidden p-6 text-center text-gray-500">
                            Tidak ada keluhan yang perlu ditindaklanjuti.
                        </div>
                    @else
                        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Nama Pengguna</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Nama Tempat</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Status</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Komentar</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Tanggal</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($keluhanBelumSelesaiList as $keluh)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                    {{ $keluh->id }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-700">
                                                    {{ $keluh->nama_pengguna ?? '-' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-700">
                                                    {{ $keluh->nama_tempat ?? '-' }}</td>
                                                <td class="px-6 py-4 text-sm">
                                                    @php
                                                        $status = 'Belum';
                                                        $class = 'bg-yellow-100 text-yellow-800';

                                                        if ($keluh->perjalanan_id) {
                                                            if ($keluh->perjalanan && $keluh->perjalanan->selesai) {
                                                                $status = 'Selesai';
                                                                $class = 'bg-green-100 text-green-800';
                                                            } else {
                                                                $status = 'Diproses';
                                                                $class = 'bg-blue-100 text-blue-800';
                                                            }
                                                        }
                                                    @endphp
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $class }}">
                                                        {{ $status }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-700">
                                                    {{ $keluh->komentar ? (strlen($keluh->komentar) > 25 ? substr($keluh->komentar, 0, 50) . '...' : $keluh->komentar) : '-' }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    {{ $keluh->created_at->format('d M Y') }}</td>
                                                <td class="px-6 py-4 text-center">
                                                    <div class="flex items-center justify-center space-x-2">

                                                        <a href="{{ route('keluh_pengguna.show', $keluh->id) }}"
                                                            class="flex-1 text-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 focus:ring focus:ring-indigo-300 transition duration-150">
                                                            Lihat
                                                        </a>
                                                        @auth
                                                            @if ($keluh->user_id == Auth::user()?->id || Auth::user()?->admin)
                                                                <form
                                                                    action="{{ route('keluh_pengguna.destroy', $keluh->id) }}"
                                                                    method="POST" class="flex-1"
                                                                    onsubmit="return confirm('Yakin ingin menghapus keluhan ini?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition duration-150">
                                                                        Hapus
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endauth
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-4 p-4 pagination-container">
                            {{ $keluhanBelumSelesaiList->links() }}
                        </div>
                    @endif
                </div>

                {{-- 2. Tabel SEDANG DIPROSES (BARU & Default Active) - KONTEN INLINE --}}
                <div id="table-processing" class="table-content hidden">
                    <h3 class="text-xl font-bold text-blue-700 mb-3">Data Keluhan Sedang Diproses</h3>

                    @if ($keluhanDiprosesList->isEmpty())
                        <div class="bg-white shadow-lg rounded-lg overflow-hidden p-6 text-center text-gray-500">
                            Tidak ada keluhan yang sedang dalam proses maintenance.
                        </div>
                    @else
                        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                ID</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Nama Pengguna</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Nama Tempat</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Status</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Komentar</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Tanggal</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($keluhanDiprosesList as $keluh)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                    {{ $keluh->id }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-700">
                                                    {{ $keluh->nama_pengguna ?? '-' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-700">
                                                    {{ $keluh->nama_tempat ?? '-' }}</td>
                                                <td class="px-6 py-4 text-sm">
                                                    {{-- GUNAKAN LOGIKA STATUS BARU DI POIN C --}}
                                                    @php
                                                        $status = 'Diproses';
                                                        $class = 'bg-blue-100 text-blue-800';
                                                    @endphp
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $class }}">
                                                        {{ $status }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-700">
                                                    {{ $keluh->komentar ? (strlen($keluh->komentar) > 25 ? substr($keluh->komentar, 0, 50) . '...' : $keluh->komentar) : '-' }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    {{ $keluh->created_at->format('d M Y') }}</td>
                                                <td class="px-6 py-4 text-center">
                                                    <div class="flex items-center justify-center space-x-2">
                                                        <a href="{{ route('keluh_pengguna.show', $keluh->id) }}"
                                                            class="flex-1 text-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 focus:ring focus:ring-indigo-300 transition duration-150">
                                                            Lihat
                                                        </a>
                                                        <a href="{{ route('maintenance.show', $keluh->perjalanan_id) }}"
                                                            class="flex-1 text-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 focus:ring focus:ring-indigo-300 transition duration-150">
                                                            Peta
                                                        </a>

                                                        @auth
                                                            @if ($keluh->user_id == Auth::user()?->id || Auth::user()?->admin)
                                                                <form
                                                                    action="{{ route('keluh_pengguna.destroy', $keluh->id) }}"
                                                                    method="POST" class="flex-1"
                                                                    onsubmit="return confirm('Yakin ingin menghapus keluhan ini?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition duration-150">
                                                                        Hapus
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endauth
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-4 p-4 pagination-container">
                            {{ $keluhanDiprosesList->links() }}
                        </div>
                    @endif
                </div>

                {{-- 3. Tabel SUDAH SELESAI - KONTEN INLINE --}}
                <div id="table-complete" class="table-content hidden">
                    <h3 class="text-xl font-bold text-green-700 mb-3">Data Keluhan Sudah Selesai</h3>

                    @if ($keluhanSelesaiList->isEmpty())
                        <div class="bg-white shadow-lg rounded-lg overflow-hidden p-6 text-center text-gray-500">
                            Belum ada keluhan yang ditandai selesai.
                        </div>
                    @else
                        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                ID</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Nama Pengguna</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Nama Tempat</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Status</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Komentar</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Tanggal</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($keluhanSelesaiList as $keluh)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                    {{ $keluh->id }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-700">
                                                    {{ $keluh->nama_pengguna ?? '-' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-700">
                                                    {{ $keluh->nama_tempat ?? '-' }}</td>
                                                <td class="px-6 py-4 text-sm">
                                                    @php
                                                        $isSelesai = $keluh->perjalanan && $keluh->perjalanan->selesai;
                                                    @endphp
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isSelesai ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ $isSelesai ? 'Selesai' : 'Belum' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-700">
                                                    {{ $keluh->komentar ? (strlen($keluh->komentar) > 25 ? substr($keluh->komentar, 0, 50) . '...' : $keluh->komentar) : '-' }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    {{ $keluh->created_at->format('d M Y') }}</td>
                                                <td class="px-6 py-4 text-center">
                                                    <div class="flex items-center justify-center space-x-2">
                                                        <a href="{{ route('keluh_pengguna.show', $keluh->id) }}"
                                                            class="flex-1 text-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 focus:ring focus:ring-indigo-300 transition duration-150">
                                                            Lihat
                                                        </a>
                                                        <a href="{{ route('maintenance.show', $keluh->perjalanan_id) }}"
                                                            class="flex-1 text-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 focus:ring focus:ring-indigo-300 transition duration-150">
                                                            Peta
                                                        </a>
                                                        @auth
                                                            @if ($keluh->user_id == Auth::user()?->id || Auth::user()?->admin)
                                                                <form
                                                                    action="{{ route('keluh_pengguna.destroy', $keluh->id) }}"
                                                                    method="POST" class="flex-1"
                                                                    onsubmit="return confirm('Yakin ingin menghapus keluhan ini?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition duration-150">
                                                                        Hapus
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endauth
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-4 p-4 pagination-container">
                            {{ $keluhanSelesaiList->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- ============================== SCRIPT AJAX ============================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('search-input');
            const form = document.getElementById('search-form');

            // Kontainer yang dimanipulasi
            const ajaxResultsContainer = document.getElementById('ajax-results');
            const defaultResultsContainer = document.getElementById('default-results');
            const tabButtons = document.querySelectorAll('.tab-mode');
            const tableContents = document.querySelectorAll('.table-content');

            // TEMPLATE URLS (Digunakan di AJAX)
            // Asumsi bahwa variabel route() tersedia atau didefinisikan secara global/dilewatkan dari backend.
            const showUrlTemplate = '{{ route('keluh_pengguna.show', ':id') }}'.replace(':id', '');
            const destroyUrlTemplate = '{{ route('keluh_pengguna.destroy', ':id') }}'.replace(':id', '');
            const mapUrlTemplate = '{{ route('maintenance.show', ':id') }}'.replace(':id', '');
            const searchUrl = '{{ route('keluh_pengguna.search') }}';
            const csrfToken = '{{ csrf_token() }}';

            // State untuk mode pencarian: 'pending', 'processing', atau 'complete'
            let currentMode = 'pending';
            // Apakah user login? (untuk AJAX)
            // const isAdmin = {{ auth()->check() ? Auth::user()?->admin : null }};
            // const idUser = {{ auth()->check() ? Auth::user()?->id : null }};
            const isAdmin = {{ auth()->check() ? json_encode(Auth::user()?->admin ?? false) : 'false' }};
            const idUser = {{ auth()->check() ? json_encode(Auth::user()?->id) : 'null' }};

            // --- FUNGSI TABBING (MENGUBAH MODE) ---
            function switchMode(newMode) {
                // Validasi mode yang valid
                const validModes = ['pending', 'processing', 'complete'];
                if (!validModes.includes(newMode)) {
                    console.error('Mode tidak valid:', newMode);
                    return;
                }

                currentMode = newMode;
                input.value = ''; // Kosongkan input saat berganti mode
                ajaxResultsContainer.innerHTML = ''; // Hapus hasil AJAX
                ajaxResultsContainer.style.display = 'none';
                defaultResultsContainer.style.display = 'block';

                // Update tampilan tab
                tabButtons.forEach(btn => {
                    if (btn.dataset.mode === newMode) {
                        btn.classList.add('border-indigo-500', 'text-indigo-700');
                        btn.classList.remove('border-transparent', 'text-gray-500');
                    } else {
                        btn.classList.remove('border-indigo-500', 'text-indigo-700');
                        btn.classList.add('border-transparent', 'text-gray-500');
                    }
                });

                // Update konten tabel default
                tableContents.forEach(content => {
                    // Log untuk debugging
                    console.log('Checking table:', content.id, 'for mode:', newMode);
                    const shouldShow = content.id === `table-${newMode}`;
                    content.style.display = shouldShow ? 'block' : 'none';
                    if (shouldShow) {
                        console.log('Showing table:', content.id);
                    }
                });

                let placeholderText = 'Cari di daftar ';
                if (newMode === 'pending') {
                    placeholderText += 'Belum';
                } else if (newMode === 'processing') {
                    placeholderText += 'Sedang Diproses';
                } else if (newMode === 'complete') {
                    placeholderText += 'Sudah Selesai';
                }
                input.placeholder = placeholderText + '...';
            }

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    switchMode(button.dataset.mode);
                });
            });

            form.addEventListener('submit', e => e.preventDefault());

            input.addEventListener('input', async function() {
                const query = this.value.trim();
                const url = `${searchUrl}?search=${encodeURIComponent(query)}&mode=${currentMode}`;

                if (query.length === 0) {
                    ajaxResultsContainer.innerHTML = '';
                    ajaxResultsContainer.style.display = 'none';
                    defaultResultsContainer.style.display = 'block';
                    return;
                }

                defaultResultsContainer.style.display = 'none';

                try {
                    const response = await fetch(url);

                    if (!response.ok) {
                        console.error('Server Error:', response.status, response.statusText);
                        ajaxResultsContainer.innerHTML =
                            `<p class="p-4 text-red-500 italic mt-3 bg-red-100 rounded-lg shadow-md">Terjadi Error Server (${response.status}).</p>`;
                        ajaxResultsContainer.style.display = 'block';
                        return;
                    }

                    const data = await response.json();

                    ajaxResultsContainer.style.display = 'block';

                    if (data.length === 0) {
                        let modeName = '';
                        if (currentMode === 'pending') {
                            modeName = 'Belum';
                        } else if (currentMode === 'processing') {
                            modeName = 'Sedang Diproses';
                        } else if (currentMode === 'complete') {
                            modeName = 'Sudah Selesai';
                        }

                        ajaxResultsContainer.innerHTML = `
                     <div class="bg-white shadow-lg rounded-lg overflow-hidden p-6 text-center text-gray-500">
                          Tidak ada hasil keluhan ditemukan di mode ${currentMode === 'incomplete' ? 'Belum Selesai' : 'Sudah Selesai'}.
                     </div>`;
                        return;
                    }

                    let modeName = '';
                    if (currentMode === 'pending') {
                        modeName = 'Belum';
                    } else if (currentMode === 'processing') {
                        modeName = 'Sedang Diproses';
                    } else if (currentMode === 'complete') {
                        modeName = 'Sudah Selesai';
                    }

                    let html = `
            <h3 class="text-xl font-bold text-indigo-700 mb-3">Hasil Pencarian di Mode ${modeName}</h3>
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pengguna</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Tempat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Komentar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        `;

                    data.forEach(item => {
                        let statusText = 'Belum';
                        let statusClass = 'bg-yellow-100 text-yellow-800';

                        if (item.perjalanan_id !== null) {
                            if (item.perjalanan && item.perjalanan.selesai) {
                                statusText = 'Selesai';
                                statusClass = 'bg-green-100 text-green-800';
                            } else {
                                statusText = 'Diproses';
                                statusClass = 'bg-blue-100 text-blue-800';
                            }
                        }

                        const showUrl = showUrlTemplate + item.id;
                        const deleteUrl = destroyUrlTemplate + item.id;

                        const dateOptions = {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        };
                        const formattedDate = new Date(item.created_at).toLocaleString('id-ID',
                            dateOptions);
                        const shortKomentar = item.komentar ? (item.komentar.length > 25 ? item
                            .komentar.substring(0, 25) + '...' : item.komentar) : '-';

                        html += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${item.id}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">${item.nama_pengguna ?? '-'}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">${item.nama_tempat ?? '-'}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                            ${statusText}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">${shortKomentar}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${formattedDate}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="${showUrl}"
                                class="flex-1 text-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 focus:ring focus:ring-indigo-300 transition duration-150">
                                Lihat
                            </a>
                            ${item.perjalanan_id ? `
                                                <a href="${mapUrlTemplate + item.perjalanan_id}"
                                                    class="flex-1 text-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 focus:ring focus:ring-indigo-300 transition duration-150">
                                                    Peta
                                                </a>
                                                ` : ''}
                            ${isAdmin || item.user_id == !idUser ? `
                                                <form action="${deleteUrl}" method="POST" class="flex-1"
                                                    onsubmit="return confirm('Yakin ingin menghapus keluhan ini?');">
                                                    <input type="hidden" name="_token" value="${csrfToken}">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit"
                                                        class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition duration-150">
                                                        Hapus
                                                    </button>
                                                </form>
                                                ` : ''}
                        </div>
                    </td>
                </tr>
                `;
                    });

                    html += '</tbody></table></div></div>';
                    ajaxResultsContainer.innerHTML = html;

                } catch (e) {
                    console.error('Kesalahan Jaringan atau Parsing JSON:', e);
                    ajaxResultsContainer.innerHTML =
                        `<p class="p-4 text-red-500 italic mt-3 bg-red-100 rounded-lg shadow-md">Terjadi kesalahan. Pastikan koneksi dan respons server valid.</p>`;
                    ajaxResultsContainer.style.display = 'block';
                }
            });
        });
    </script>
