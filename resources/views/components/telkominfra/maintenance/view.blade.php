@props([
    'perjalanans',
    'search' => '',
    'searchMode',
    'totalPerjalanan',
    'perjalananSelesai',
    'perjalananBelumSelesai',
])

<div class="bg-white shadow-xl sm:rounded-lg p-6">
    <h3 class="text-lg font-semibold mb-4">Daftar Sesi Drive Test</h3>

    @auth
        {{-- ========== FORM UPLOAD DATA BARU ========== --}}
        <div class="mb-6 border p-4 rounded-lg bg-gray-50">
            <h4 class="text-md font-bold mb-3">Unggah Data Perjalanan (NMF) Baru</h4>
            <form action="{{ route('perjalanan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <input type="hidden" name="id_perjalanan" value="">

                    <div>
                        <label for="nmf_file" class="block text-sm font-medium text-gray-700">
                            File Nemo (.nmf atau .txt log):
                        </label>
                        <input type="file" id="nmf_file" name="nmf_file[]" accept=".nmf,.txt" multiple required
                            class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white p-2">
                    </div>

                    <div>
                        <label for="nama_pengguna" class="block text-sm font-medium text-gray-700">Nama Pengguna:</label>
                        <input type="text" id="nama_pengguna" name="nama_pengguna" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border"
                            value="{{ Auth::user()->name ?? 'User Default' }}">
                    </div>

                    <div>
                        <label for="nama_tempat" class="block text-sm font-medium text-gray-700">Nama Tempat /
                            Lokasi:</label>
                        <input type="text" id="nama_tempat" name="nama_tempat" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border" value="Palembang">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status Pengujian:</label>
                        <select id="status" name="status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Before" selected>Before</option>
                            <option value="After">After</option>
                        </select>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <strong>Error!</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-300 transition ease-in-out duration-150 mt-2">
                    Unggah Data Baru
                </button>
            </form>
        </div>
    @endauth

    <div class="container mx-auto p-4">

        <div class="container mx-auto p-4">

            {{-- ============================== STATISTIK RINGKASAN PERJALANAN (BARU) ============================== --}}
            <h2 class="text-2xl font-extrabold text-gray-900 mb-4 border-b pb-2">Dashboard Data Maintenance</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

                {{-- Total Keseluruhan --}}
                <div
                    class="bg-indigo-100 p-6 rounded-xl shadow-lg border-l-4 border-indigo-600 flex justify-between items-start transition duration-300 hover:shadow-xl hover:scale-[1.01]">
                    <div class="space-y-1">
                        <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider">Total Perjalanan</p>
                        {{-- ASUMSI: $totalPerjalanan telah disiapkan di Controller --}}
                        <p class="text-4xl font-extrabold text-indigo-900">{{ number_format($totalPerjalanan ?? 0) }}
                        </p>
                    </div>
                    <i class="fas fa-route text-5xl text-indigo-400 opacity-30 mt-1 ml-4"></i>
                </div>

                {{-- Sudah Selesai --}}
                <div
                    class="bg-green-100 p-6 rounded-xl shadow-lg border-l-4 border-green-600 flex justify-between items-start transition duration-300 hover:shadow-xl hover:scale-[1.01]">
                    <div class="space-y-1">
                        <p class="text-sm font-medium text-green-600 uppercase tracking-wider">Sudah Selesai</p>
                        {{-- ASUMSI: $perjalananSelesai telah disiapkan di Controller --}}
                        <p class="text-4xl font-extrabold text-green-900">{{ number_format($perjalananSelesai ?? 0) }}
                        </p>
                    </div>
                    <i class="fas fa-check-circle text-5xl text-green-400 opacity-30 mt-1 ml-4"></i>
                </div>

                {{-- Belum Selesai --}}
                <div
                    class="bg-yellow-100 p-6 rounded-xl shadow-lg border-l-4 border-yellow-600 flex justify-between items-start transition duration-300 hover:shadow-xl hover:scale-[1.01]">
                    <div class="space-y-1">
                        <p class="text-sm font-medium text-yellow-600 uppercase tracking-wider">Belum Selesai</p>
                        {{-- ASUMSI: $perjalananBelumSelesai telah disiapkan di Controller --}}
                        <p class="text-4xl font-extrabold text-yellow-900">
                            {{ number_format($perjalananBelumSelesai ?? 0) }}</p>
                    </div>
                    <i class="fas fa-hourglass-half text-5xl text-yellow-400 opacity-30 mt-1 ml-4"></i>
                </div>
            </div>

            <hr class="my-6 border-gray-200">

            {{-- ========== PENCARIAN & TAB FILTER (BARU) ========== --}}
            <div class="mb-6 p-4 bg-white rounded-lg shadow-md border border-gray-100">
                <div class="flex border-b mb-4">
                    {{-- Tambahkan tab untuk filter status --}}
                    <button id="mode-all" data-mode=""
                        class="tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 {{ !isset($searchMode) || $searchMode == '' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }}">
                        <i class="fas fa-list mr-2"></i> Semua Data ({{ number_format($totalPerjalanan) }})
                    </button>
                    <button id="mode-incomplete" data-mode="0"
                        class="tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 {{ isset($searchMode) && $searchMode == '0' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }}">
                        <i class="fas fa-hourglass-half mr-2"></i> Belum Selesai
                        ({{ number_format($perjalananBelumSelesai) }})
                    </button>
                    <button id="mode-complete" data-mode="1"
                        class="tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 {{ isset($searchMode) && $searchMode == '1' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }}">
                        <i class="fas fa-check-double mr-2"></i> Sudah Selesai
                        ({{ number_format($perjalananSelesai) }})
                    </button>
                </div>

                <form id="search-form"
                    class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-3">
                    <input type="text" id="search-input" name="search" value="{{ $search ?? '' }}"
                        placeholder="Cari ID, ID Perjalanan, Lokasi, atau Pengguna..."
                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">
                    <input type="hidden" id="search-mode-input" name="mode" value="{{ $searchMode ?? '' }}">

                    <div class="flex space-x-3 w-full sm:w-auto">
                        <button type="submit"
                            class="w-1/2 sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-150 shadow-md">
                            <i class="fas fa-search"></i> Cari
                        </button>

                        <a href="{{ route('maintenance.index') }}"
                            class="w-1/2 sm:w-auto flex items-center justify-center py-2 px-4 text-gray-600 hover:text-gray-900 bg-gray-100 rounded-lg transition duration-150 shadow-sm border border-gray-300">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- ========== HASIL TABEL (AJAX / DEFAULT) ========== --}}
            <div id="result-container">

                {{-- 1. HASIL AJAX: Kontainer yang diisi JavaScript. Disembunyikan saat load. --}}
                <div id="ajax-results" style="display: none;">
                    {{-- Konten tabel hasil AJAX akan dimasukkan di sini oleh JavaScript --}}
                </div>

                {{-- 2. HASIL DEFAULT: Kontainer yang diisi Blade. Disembunyikan jika ada $search. --}}
                <div id="default-results" style="{{ $search ? 'display: none;' : 'display: block;' }}">
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                            Peta</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID
                                            Perjalanan</th>
                                        {{-- KOLOM BARU --}}
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                            Selesai</th>
                                        {{-- END KOLOM BARU --}}
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Lokasi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Pengguna</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Waktu Unggah</th>
                                        @auth
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Hapus</th>
                                        @endauth
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($perjalanans as $perjalanan)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ $perjalanan->id }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <button
                                                    onclick="window.location='{{ route('maintenance.show', $perjalanan->id) }}'"
                                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 focus:ring focus:ring-indigo-300">
                                                    Peta
                                                </button>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700">
                                                {{ $perjalanan->id_perjalanan }}</td>
                                            {{-- KOLOM BARU --}}
                                            <td class="px-6 py-4 text-center text-sm">
                                                @if ($perjalanan->selesai)
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ya</span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Tidak</span>
                                                @endif
                                            </td>
                                            {{-- END KOLOM BARU --}}
                                            <td class="px-6 py-4 text-sm text-gray-700">{{ $perjalanan->nama_tempat }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $perjalanan->nama_pengguna }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $perjalanan->created_at->format('d M Y H:i') }}</td>
                                            @auth
                                                <td class="px-6 py-4 text-center">
                                                    <form action="{{ route('perjalanan.destroy', $perjalanan->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </td>
                                            @endauth
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada
                                                data ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="p-4">
                                {{ $perjalanans->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<br>




<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('search-input');
        const form = document.getElementById('search-form');
        const searchModeInput = document.getElementById('search-mode-input');

        // Kontainer yang dimanipulasi
        const ajaxResultsContainer = document.getElementById('ajax-results');
        const defaultResultsContainer = document.getElementById('default-results');
        const tabButtons = document.querySelectorAll('.tab-mode');

        // State mode pencarian awal (diambil dari Blade jika ada)
        let currentMode = searchModeInput.value;

        // TEMPLATE URLS
        const showUrlTemplate = '{{ route('maintenance.show', 'DUMMY_ID') }}'.replace('DUMMY_ID', '');
        const destroyUrlTemplate = '{{ route('perjalanan.destroy', 'DUMMY_ID') }}'.replace('DUMMY_ID', '');
        const searchUrl = '{{ route('perjalanan.ajaxSearch') }}'; // Pastikan route ini benar
        const csrfToken = '{{ csrf_token() }}';

        // --- FUNGSI AJAX UTAMA (Dipanggil oleh input dan tab) ---
        async function runAjaxSearch(query, mode) {
            // Tampilkan Loading
            ajaxResultsContainer.innerHTML =
                `<div class="p-6 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...</div>`;
            defaultResultsContainer.style.display = 'none';
            ajaxResultsContainer.style.display = 'block';

            const url = `${searchUrl}?search=${encodeURIComponent(query)}&mode=${encodeURIComponent(mode)}`;

            try {
                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error(`Server Error: ${response.status} ${response.statusText}`);
                }

                const data = await response.json();

                if (data.length === 0) {
                    ajaxResultsContainer.innerHTML = `
                      <div class="bg-white shadow-lg rounded-lg overflow-hidden p-6 text-center text-gray-500">
                           Tidak ada hasil ditemukan untuk filter saat ini.
                      </div>`;
                    return;
                }

                // --- Mulai Buat HTML Tabel AJAX ---
                let html = `
            <h3 class="text-xl font-bold text-indigo-700 mb-3">Hasil Filter / Pencarian Cepat</h3>
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Peta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Perjalanan</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Selesai</th> 
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengguna</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Unggah</th>
                                @auth
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hapus</th>
                                @endauth
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        `;

                data.forEach(item => {
                    const showUrl = showUrlTemplate + item.id;
                    const deleteUrl = destroyUrlTemplate + item.id;
                    // Format tanggal sama seperti di Blade
                    const formattedDate = new Date(item.created_at).toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const statusBadge = item.selesai ?
                        '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ya</span>' :
                        '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Tidak</span>';

                    html += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${item.id}</td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="window.location='${showUrl}'"
                            class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 focus:ring focus:ring-indigo-300">
                            Peta
                        </button>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">${item.id_perjalanan ?? '-'}</td>
                    <td class="px-6 py-4 text-center text-sm">${statusBadge}</td> 
                    <td class="px-6 py-4 text-sm text-gray-700">${item.nama_tempat ?? '-'}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${item.nama_pengguna ?? '-'}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${formattedDate}</td>
                    @auth
                    <td class="px-6 py-4 text-center">
                        <form action="${deleteUrl}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                             <input type="hidden" name="_token" value="${csrfToken}">
                             <input type="hidden" name="_method" value="DELETE">
                             <button type="submit"
                                 class="bg-red-500 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition">
                                 Hapus
                             </button>
                           </form>
                    </td>
                    @endauth
                </tr>
                `;
                });

                html += '</tbody></table></div></div>';
                ajaxResultsContainer.innerHTML = html;

            } catch (e) {
                console.error('Kesalahan Jaringan atau Parsing JSON:', e);
                ajaxResultsContainer.innerHTML =
                    `<p class="text-red-500 italic mt-3">Terjadi kesalahan. Cek koneksi atau respons server.</p>`;
            }
        }

        // --- LOGIC TAB SWITCH (TANPA RELOAD) ---
        function switchMode(newMode) {
            currentMode = newMode;
            searchModeInput.value = newMode; // Update hidden input

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

            const query = input.value.trim();

            // Jika query kosong, tampilkan default results (yang sudah difilter oleh Blade)
            if (query.length === 0 && newMode === '{{ $searchMode ?? '' }}') {
                // Jika mode yang dipilih sama dengan mode default halaman (dan tidak ada search), 
                // kita tampilkan kembali hasil Blade default
                ajaxResultsContainer.innerHTML = '';
                ajaxResultsContainer.style.display = 'none';
                defaultResultsContainer.style.display = 'block';
                return;
            }

            // Jalankan AJAX search dengan mode dan query saat ini
            runAjaxSearch(query, newMode);
        }

        // Event Listener untuk tombol tab
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Pastikan tab berfungsi hanya jika ada perbedaan mode atau ada query
                switchMode(button.dataset.mode);
            });
        });

        // Mencegah form submit tradisional pada input dan menjalankan AJAX search saat mengetik
        form.addEventListener('submit', e => {
            // Jika form disubmit (klik tombol Cari), kita tetap lakukan full submit 
            // untuk menghandle pagination di server side
        });

        input.addEventListener('input', function() {
            const query = this.value.trim();
            const mode = currentMode;

            // KASUS 1: Query Kosong. Tampilkan hasil default yang sudah difilter mode.
            if (query.length === 0) {
                ajaxResultsContainer.innerHTML = '';
                ajaxResultsContainer.style.display = 'none';
                defaultResultsContainer.style.display = 'block';
                return;
            }

            // KASUS 2: Ada Query. Jalankan AJAX search.
            runAjaxSearch(query, mode);
        });
    });
</script>
