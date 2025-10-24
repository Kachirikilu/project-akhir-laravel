<div class="mb-8">
    <div class="mt-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Manajemen Pengguna</h2>

        {{-- ========== PENCARIAN & TAB FILTER ========== --}}
        <div class="mb-6 p-4 bg-white rounded-lg shadow-md border border-gray-100">
            <div class="flex border-b mb-4">
                {{-- Tab Semua --}}
                <button id="mode-all" data-mode=""
                    class="tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 
                    {{ !isset($searchMode) || $searchMode == '' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }}">
                    <i class="fas fa-users mr-2"></i> Semua Pengguna (<span
                        id="count-all">{{ number_format($totalUsers) }}</span>)
                </button>
                {{-- Tab Admin --}}
                <button id="mode-admin" data-mode="admin"
                    class="tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 
                    {{ isset($searchMode) && $searchMode == 'admin' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }}">
                    <i class="fas fa-crown mr-2"></i> Admin (<span
                        id="count-admin">{{ number_format($totalAdmins) }}</span>)
                </button>
                {{-- Tab User Biasa --}}
                <button id="mode-user" data-mode="user"
                    class="tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 
                    {{ isset($searchMode) && $searchMode == 'user' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }}">
                    <i class="fas fa-user mr-2"></i> User Biasa (<span
                        id="count-user">{{ number_format($totalNormalUsers) }}</span>)
                </button>
            </div>

            <form id="search-form" class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-3">

                <input type="text" id="search-input" name="search" value="{{ $search ?? '' }}"
                    placeholder="Cari Nama, Email, atau ID Pengguna..."
                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">

                <input type="hidden" id="search-mode-input" name="mode" value="{{ $searchMode ?? '' }}">

                <div class="flex space-x-3 w-full sm:w-auto">
                    <button type="submit"
                        class="w-1/2 sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-150 shadow-md">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('dashboard') }}"
                        class="w-1/2 sm:w-auto flex items-center justify-center py-2 px-4 text-gray-600 hover:text-gray-900 bg-gray-100 rounded-lg transition duration-150 shadow-sm border border-gray-300">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- ========== HASIL TABEL PENGGUNA (AJAX CONTAINER) ========== --}}
        <div class="bg-white shadow-lg rounded-lg overflow-hidden" id="user-results-container">
            <div id="ajax-table-content">

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Pada
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{-- Loop untuk menampilkan data pengguna --}}
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50" data-user-id="{{ $user->id }}">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-center text-sm">
                                        @if ($user->admin)
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Admin</span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">User
                                                Biasa</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $user->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-center text-sm">
                                        {{-- Tombol Delete hanya muncul jika user adalah admin dan BUKAN dirinya sendiri --}}
                                        @if (Auth::user()?->admin && Auth::id() !== $user->id)
                                            {{-- Gunakan button biasa dengan data-user-id untuk di-handle oleh JS AJAX --}}
                                            <button type="button" data-user-id="{{ $user->id }}"
                                                class="delete-user-btn bg-red-500 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition">
                                                Hapus
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada pengguna
                                        ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination hanya di-render saat load awal --}}
                    <div class="p-4" id="pagination-links-container">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const IS_ADMIN = @json(Auth::check() && Auth::user()->admin);
    const CURRENT_USER_ID = @json(Auth::id());
    const AJAX_URL = '{{ route('users.ajaxSearch') }}';
    const DELETE_ROUTE = '{{ route('users.destroy', 'DUMMY_ID') }}'.replace('/DUMMY_ID', '');
    const CSRF_TOKEN = '{{ csrf_token() }}';
</script>


<script>

    function buildDeleteButton(user) {
        if (!IS_ADMIN || user.id === CURRENT_USER_ID) return '';

        return `
            <button type="button" data-user-id="${user.id}"
                class="delete-user-btn bg-red-500 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition">
                Hapus
            </button>
        `;
    }

    function buildTableRow(user) {
        const formattedDate = new Date(user.created_at).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });

        const roleSpan = user.admin ?
            '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Admin</span>' :
            '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">User Biasa</span>';

        const deleteButtonHtml = buildDeleteButton(user);

        return `
            <tr class="hover:bg-gray-50" data-user-id="${user.id}">
                <td class="px-6 py-4 text-sm font-medium text-gray-900">${user.id}</td>
                <td class="px-6 py-4 text-sm text-gray-700">${user.name}</td>
                <td class="px-6 py-4 text-sm text-gray-700">${user.email}</td>
                <td class="px-6 py-4 text-center text-sm">${roleSpan}</td>
                <td class="px-6 py-4 text-sm text-gray-500">${formattedDate}</td>
                <td class="px-6 py-4 text-center text-sm">${deleteButtonHtml}</td>
            </tr>
        `;
    }

    function buildPaginationLinks(paginationData) {
        if (!paginationData.links || paginationData.links.length <= 3) return '';

        let linksHtml = '';
        paginationData.links.forEach(link => {
            const urlObj = new URL(link.url || 'http://dummy.com');
            const pageNum = urlObj.searchParams.get('page') || (link.label.includes('«') || link.label.includes(
                '»') ? '' : link.label);

            const isActive = link.active ? 'bg-indigo-600 text-white' :
                'text-gray-700 bg-white hover:bg-gray-50';
            const isLabel = link.label.includes('«') || link.label.includes('»');

            if (link.url) {
                linksHtml += `
                    <a href="#" class="pagination-link px-3 py-1 text-sm border border-gray-300 rounded-lg ${isActive} transition" 
                       data-page="${pageNum || (link.active ? paginationData.current_page : '')}">
                        ${link.label.replace('&laquo; Previous', '«').replace('Next &raquo;', '»')}
                    </a>
                 `;
            } else {
                linksHtml += `
                    <span class="px-3 py-1 text-sm border border-gray-300 rounded-lg ${link.active ? 'bg-indigo-600 text-white' : 'text-gray-400 cursor-not-allowed'}">
                        ${link.label.replace('&laquo; Previous', '«').replace('Next &raquo;', '»')}
                    </span>
                `;
            }
        });

        return `<nav class="flex justify-center space-x-2">${linksHtml}</nav>`;
    }


    // --- FUNGSI UTAMA LOAD DATA ---
    function getSearchParams() {
        return {
            search: document.getElementById('search-input').value.trim(),
            mode: document.getElementById('search-mode-input').value,
        };
    }

    function loadUserTable(params) {
        const container = document.getElementById('ajax-table-content');
        const urlParams = new URLSearchParams(params).toString();

        // Tampilkan loading state
        container.innerHTML =
            '<div class="p-6 text-center text-indigo-500"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...</div>';

        fetch(AJAX_URL + '?' + urlParams, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error(`Server Error: ${response.status} ${response.statusText}`);
                return response.json();
            })
            .then(data => {
                let tableBodyHtml = '';

                if (data.users && data.users.length > 0) {
                    data.users.forEach(user => {
                        tableBodyHtml += buildTableRow(user);
                    });
                } else {
                    tableBodyHtml =
                        '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada pengguna ditemukan.</td></tr>';
                }

                // Merakit Kontainer Baru (struktur tabel sama dengan partial Blade)
                const newTableContent = `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Pada</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${tableBodyHtml}
                        </tbody>
                    </table>
                    
                    <div class="p-4" id="pagination-links-container">
                        ${buildPaginationLinks(data.pagination)}
                    </div>
                </div>
            `;

                container.innerHTML = newTableContent;

                document.querySelector('#count-all').textContent = data.counts.totalUsers.toLocaleString();
                document.querySelector('#count-admin').textContent = data.counts.totalAdmins.toLocaleString();
                document.querySelector('#count-user').textContent = data.counts.totalNormalUsers.toLocaleString();
                
                attachHandlers();
                history.pushState(null, '', `?${urlParams}`);
            })
            .catch(error => {
                console.error('Error fetching user data:', error);
                container.innerHTML =
                    '<div class="p-6 text-center text-red-500">Gagal memuat data pengguna. Cek console untuk detail.</div>';
            });
    }

    // --- HANDLER FUNGSI ---
    function handlePaginationClick(e) {
        e.preventDefault();
        const page = this.dataset.page;
        const currentParams = getSearchParams();

        loadUserTable({
            ...currentParams,
            page: page
        });
    }

    function handleDeleteClick() {
        const userId = this.dataset.userId;

        if (!IS_ADMIN) {
            alert('Akses Ditolak: Anda tidak memiliki izin untuk menghapus.');
            return;
        }

        if (userId == CURRENT_USER_ID) {
            alert('Anda tidak dapat menghapus akun Anda sendiri!');
            return;
        }

        if (!confirm(
                `Apakah Anda yakin ingin menghapus Pengguna ID ${userId} (${document.querySelector(`tr[data-user-id="${userId}"] td:nth-child(2)`).textContent})?`
                )) return;

        // Kirim permintaan DELETE menggunakan Fetch API
        fetch(DELETE_ROUTE + '/' + userId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    _method: 'DELETE'
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || response.statusText);
                    });
                }
                return response.json();
            })
            .then(data => {
                const row = document.querySelector(`tr[data-user-id="${userId}"]`);
                if (row) row.remove();

                alert(data.message);
                loadUserTable(getSearchParams());
            })
            .catch(error => {
                alert(`Gagal menghapus: ${error.message}`);
                console.error('Delete Error:', error);
            });
    }

    function attachHandlers() {
        // 1. Attach Pagination Handlers
        document.querySelectorAll('#pagination-links-container .pagination-link').forEach(link => {
            link.addEventListener('click', handlePaginationClick);
        });

        // 2. Attach Delete Handlers (Hanya jika admin)
        if (IS_ADMIN) {
            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', handleDeleteClick);
            });
        }
    }


    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-mode');
        const searchForm = document.getElementById('search-form');
        const searchInput = document.getElementById('search-input');
        const searchModeInput = document.getElementById('search-mode-input');

        const doneTypingInterval = 500;
        let typingTimer;

        // --- INITIATE PADA LOAD AWAL ---
        const initialParams = new URLSearchParams(window.location.search);
        if (initialParams.has('search') || initialParams.has('mode') || initialParams.has('page')) {
            loadUserTable(Object.fromEntries(initialParams.entries()));
        } else {
            attachHandlers();
        }

        // 1. Handler untuk Tab (Filter Mode)
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const newMode = this.getAttribute('data-mode');

                searchModeInput.value = newMode;
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-indigo-500', 'text-indigo-700');
                    btn.classList.add('border-transparent', 'text-gray-500',
                        'hover:text-indigo-700');
                });
                this.classList.add('border-indigo-500', 'text-indigo-700');
                this.classList.remove('border-transparent', 'text-gray-500',
                    'hover:text-indigo-700');

                const params = getSearchParams();
                delete params.page;
                loadUserTable(params);
            });
        });

        // 2. Handler untuk Form Submit (Tombol Cari)
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const params = getSearchParams();
            delete params.page;
            loadUserTable(params);
        });

        // 3. Handler untuk Input Teks (Cari saat mengetik)
        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                const params = getSearchParams();
                delete params.page;
                loadUserTable(params);
            }, doneTypingInterval);
        });
    });
</script>
