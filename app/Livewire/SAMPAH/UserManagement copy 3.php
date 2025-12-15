<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserManagement extends Component
{
    use WithPagination;
    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $filter = '';
    public $showModal = false;
    public $isEditing = false;
    public $roleType;

    // Properti Konfirmasi Hapus
    public $showDeleteConfirmation = false;
    public $userIdToDelete = null;
    public $userEmailToDelete = '';

    // Form fields
    public $userId, $email, $password, $name, $nip, $nim, $tahun_masuk, $prodi_id;

    // Properti Filter Prodi (Autocomplete) - Untuk Filter Utama
    public $prodiSearchQuery = '';
    public $prodiSearchResults = [];
    public $selectedProdiId = null;
    public $selectedProdiName = '';

    // --- PROPERTI UNTUK AUTOCOMPLETE MODAL ---
    public $prodi_name_search = '';
    public $prodi_results = [];
    // ----------------------------------------

    protected $updatesQueryString = ['search', 'filter', 'prodiSearchQuery'];

    // Aturan validasi dasar (akan di override/modifikasi di save/update)
    protected $rules = [
        'email' => 'required|email',
        'password' => 'nullable|min:8', // Diubah menjadi min 8 untuk konsistensi
        'name' => 'required|string|max:255',
        'nip' => 'nullable|string|max:50',
        'nim' => 'nullable|string|max:50',
        'tahun_masuk' => 'nullable|integer',
        'prodi_id' => 'required|exists:prodis,id',
    ];

    public function render()
    {
        // 1. MEMBANGUN BASE QUERY (Filter Search dan Prodi)
        $query = User::query()
            ->with(['admin', 'dosen', 'mahasiswa'])
            ->where(function ($q) {
                // Logika Pencarian Teks
                $q->where('email', 'like', "%{$this->search}%")
                    ->orWhereHas('admin', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('dosen', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('dosen', fn($q) => $q->where('nip', 'like', "%{$this->search}%"))
                    ->orWhereHas('mahasiswa', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('mahasiswa', fn($q) => $q->where('nim', 'like', "%{$this->search}%"))
                    ->orWhereHas('mahasiswa', fn($q) => $q->where('tahun_masuk', 'like', "%{$this->search}%"))
                    ->orWhereHas('admin.prodi', fn($q) => $q->where('nama_prodi', 'like', "%{$this->search}%"))
                    ->orWhereHas('dosen.prodi', fn($q) => $q->where('nama_prodi', 'like', "%{$this->search}%"))
                    ->orWhereHas('mahasiswa.prodi', fn($q) => $q->where('nama_prodi', 'like', "%{$this->search}%"));
            });

        // Logika Filter Prodi
        $query->when($this->selectedProdiId, function ($q) {
            $q->where(function ($subQ) {
                $subQ->whereHas('admin', fn($rel) => $rel->where('prodi_id', $this->selectedProdiId))
                    ->orWhereHas('dosen', fn($rel) => $rel->where('prodi_id', $this->selectedProdiId))
                    ->orWhereHas('mahasiswa', fn($rel) => $rel->where('prodi_id', $this->selectedProdiId));
            });
        });

        // 2. KLONING QUERY UNTUK PENGHITUNGAN TOTAL (Count Query Base)
        $countQueryBase = clone $query;

        // 3. APLIKASIKAN FILTER ROLE KE QUERY UTAMA (Untuk Paginasi)
        if ($this->filter === 'admin') {
            $query->whereHas('admin');
        } elseif ($this->filter === 'dosen') {
            $query->whereHas('dosen');
        } elseif ($this->filter === 'mahasiswa') {
            $query->whereHas('mahasiswa');
        }

        // 4. HITUNG TOTAL PENGGUNA BERDASARKAN FILTER TEKS & PRODI
        $totalUsers = $countQueryBase->count();
        $totalAdmins = (clone $countQueryBase)->whereHas('admin')->count();
        $totalDosens = (clone $countQueryBase)->whereHas('dosen')->count();
        $totalMahasiswas = (clone $countQueryBase)->whereHas('mahasiswa')->count();
        
        // 5. LOGIKA AUTOCOMPLETE PRODI (UNTUK FILTER UTAMA, BUKAN MODAL)
        if (strlen($this->prodiSearchQuery) > 1) {
            $this->prodiSearchResults = Prodi::where('nama_prodi', 'like', '%' . $this->prodiSearchQuery . '%')
                                             ->orWhere('id', $this->prodiSearchQuery) 
                                             ->limit(10)
                                             ->get();
        } elseif (empty($this->prodiSearchQuery)) {
            $this->prodiSearchResults = Prodi::limit(5)->get();
        } else {
            $this->prodiSearchResults = [];
        }


        // 6. LOGIKA AUTOCOMPLETE PRODI UNTUK MODAL (Telah disederhanakan)
        // Logika pengisian awal prodi_results dipindahkan ke updatedProdiNameSearch dan showAddModal/editUser

        return view('livewire.user-management', [
            'users' => $query->paginate(8),
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'totalDosens' => $totalDosens,
            'totalMahasiswas' => $totalMahasiswas,
        ]);
    }

    // --- METODE FILTER (TETAP SAMA) ---
    public function filterBy($role)
    {
        $this->filter = $role;
        $this->resetPage();
    }

    public function selectProdiForFilter($prodiId)
    {
        $prodi = Prodi::find($prodiId);
        if ($prodi) {
            $this->selectedProdiId = $prodiId;
            $this->selectedProdiName = $prodi->nama_prodi;
            $this->prodiSearchQuery = '';
            $this->resetPage();
        }
    }
    

    public function resetProdiFilter()
    {
        $this->selectedProdiId = null;
        $this->selectedProdiName = '';
        $this->prodiSearchQuery = '';
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filter', 'selectedProdiId', 'selectedProdiName', 'prodiSearchQuery']);
        $this->resetPage();
    }
    
    public function loadInitialProdiResults()
    {
        if (empty($this->prodi_name_search) && !$this->prodi_id) {
            $this->prodi_results = Prodi::orderBy('nama_prodi')->limit(5)->get(['id', 'nama_prodi'])->toArray();
        }
    }
        // --- PERBAIKAN UTAMA: updatedProdiNameSearch DENGAN EXACT MATCH ---
    public function updatedProdiNameSearch($value)
    {
        // 1. Atur prodi_id menjadi null saat mulai mengetik pencarian baru
        $this->prodi_id = null;
        $this->resetErrorBag(['prodi_id', 'prodi_name_search']);

        if (strlen($value) === 0) {
            $this->prodi_results = [];
        }

        if (strlen($value) > 0) {
            // Pencarian LIKE untuk mengisi dropdown
            $results = Prodi::where('nama_prodi', 'like', '%' . $value . '%')
                ->orWhere('id', 'like', '%' . $value . '%') 
                ->limit(10)
                ->get(['id', 'nama_prodi']);

            $this->prodi_results = $results->toArray();

            // 2. LOGIKA EXACT MATCH (Case-Insensitive)
            $exactMatch = $results->filter(function ($prodi) use ($value) {
                // Bandingkan secara case-insensitive
                return strtolower($prodi->nama_prodi) === strtolower($value);
            })->first();
            
            if ($exactMatch) {
                // Jika ada match persis, set ID dan tutup dropdown
                $this->prodi_id = $exactMatch->id;
                $this->prodi_name_search = $exactMatch->nama_prodi; // Isi nama prodi yang persis dari DB
                $this->prodi_results = []; // Sembunyikan dropdown
            }
            
        } else {
            // Jika input dikosongkan, tampilkan 5 hasil awal lagi
            $this->prodi_results = Prodi::orderBy('nama_prodi')->limit(5)->get(['id', 'nama_prodi'])->toArray();
        }
 
    }

    // Method untuk memilih hasil pencarian
    public function selectProdi($prodiId, $prodiName)
    {
        $this->prodi_id = $prodiId;
        $this->prodi_name_search = $prodiName; // Isi input dengan nama prodi yang dipilih
        $this->prodi_results = []; // Tutup dropdown
        $this->resetErrorBag(['prodi_id', 'prodi_name_search']); // Hapus error jika ada
    }
    public function resetProdiInput()
    {
        $this->prodi_id = null;
        $this->prodi_name_search = '';
        $this->prodi_results = Prodi::orderBy('nama_prodi')->limit(5)->get(['id', 'nama_prodi'])->toArray(); // Tampilkan 5 hasil awal lagi
        $this->resetErrorBag(['prodi_id', 'prodi_name_search']);
    }

    public function showAddModal($role)
    {
        $this->resetInput();
        $this->roleType = $role;
        $this->showModal = true;
        $this->isEditing = false;

        // Panggil updatedProdiNameSearch dengan nilai kosong untuk mengisi 5 hasil awal
        $this->updatedProdiNameSearch(''); 
    }

    public function resetInput()
    {
        $this->reset([
            'userId', 'email', 'password', 'name', 'nip', 'nim', 'tahun_masuk', 
            'prodi_id', 'prodi_name_search', 'prodi_results', 
            'roleType'
        ]);
        $this->resetErrorBag(); 
    }

    public function saveUser()
    {
        $rules = $this->rules;
        $rules['email'] .= '|unique:users,email';
        $rules['password'] = 'required|min:8';

        if ($this->roleType === 'dosen') {
            $rules['nip'] = 'required|unique:dosens,nip';
        } elseif ($this->roleType === 'mahasiswa') {
            $rules['nim'] = 'required|unique:mahasiswas,nim';
            $rules['tahun_masuk'] = 'required|integer|min:1900|max:' . date('Y');
        }

        // Validasi utama: Pastikan prodi_id sudah terisi (baik dari select atau exact match)
        $rules['prodi_id'] = 'required|exists:prodis,id';
        
        // --- VALIDASI TAMBAHAN UNTUK UI/UX AUTOCOMPLETE ---
        if (!$this->prodi_id || empty($this->prodi_name_search)) {
            // Ini akan menimpa pesan error default prodi_id
            $this->addError('prodi_name_search', 'Program Studi harus dipilih dari daftar yang tersedia atau diketik dengan nama yang benar.');
            $this->prodi_id = null; // Pastikan ID kosong jika validasi gagal
            return; 
        }
        // -------------------------------------------------
        
        $this->validate($rules);
        
        // Logika penyimpanan data
        $user = User::create([
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        if ($this->roleType === 'admin') {
            Admin::create(['user_id' => $user->id, 'name' => $this->name, 'prodi_id' => $this->prodi_id]);
        } elseif ($this->roleType === 'dosen') {
            Dosen::create(['user_id' => $user->id, 'nip' => $this->nip, 'name' => $this->name, 'prodi_id' => $this->prodi_id]);
        } elseif ($this->roleType === 'mahasiswa') {
            Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $this->nim,
                'tahun_masuk' => $this->tahun_masuk,
                'name' => $this->name,
                'prodi_id' => $this->prodi_id,
            ]);
        }

        $this->showModal = false;
        $this->dispatch('toast', message: '✅ Pengguna berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        if (!Auth::user()->admin) {
            $this->dispatch('toast', message: '❌ Hanya admin yang dapat mengedit pengguna.');
            return;
        }

        $this->resetInput();
        $this->isEditing = true;
        $this->showModal = true;

        $user = User::with(['admin', 'dosen', 'mahasiswa'])->findOrFail($id);
        $this->userId = $user->id;
        $this->email = $user->email;
        $this->prodi_id = $user->admin->prodi_id ?? $user->dosen->prodi_id ?? $user->mahasiswa->prodi_id ?? null; 

        // --- BARU: Ambil Nama Prodi untuk ditampilkan di input autocomplete ---
        if ($this->prodi_id) {
            $prodi = Prodi::find($this->prodi_id);
            // Tetapkan nama prodi untuk input autocomplete
            $this->prodi_name_search = $prodi ? $prodi->nama_prodi : '';
        } else {
            $this->prodi_name_search = '';
        }
        $this->prodi_results = []; // Pastikan dropdown tertutup saat edit
        // ---------------------------------------------------------------------

        if ($user->admin) {
            $this->roleType = 'admin';
            $this->name = $user->admin->name;
        } elseif ($user->dosen) {
            $this->roleType = 'dosen';
            $this->name = $user->dosen->name;
            $this->nip = $user->dosen->nip;
        } elseif ($user->mahasiswa) {
            $this->roleType = 'mahasiswa';
            $this->name = $user->mahasiswa->name;
            $this->nim = $user->mahasiswa->nim;
            $this->tahun_masuk = $user->mahasiswa->tahun_masuk;
        }
    }

    public function updateUser()
    {
        $rules = [
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'name' => 'required|string|max:255',
            'password' => 'nullable|min:8', 
            'prodi_id' => 'required|exists:prodis,id', 
        ];

        if ($this->roleType === 'dosen') {
            $rules['nip'] = ['required', Rule::unique('dosens', 'nip')->ignore($this->userId, 'user_id')]; 
        } elseif ($this->roleType === 'mahasiswa') {
            $rules['nim'] = ['required', Rule::unique('mahasiswas', 'nim')->ignore($this->userId, 'user_id')]; 
            $rules['tahun_masuk'] = 'required|integer|min:1900|max:' . date('Y');
        }

        // --- VALIDASI TAMBAHAN UNTUK UI/UX AUTOCOMPLETE ---
        if (!$this->prodi_id || empty($this->prodi_name_search)) {
            $this->addError('prodi_name_search', 'Program Studi harus dipilih dari daftar yang tersedia atau diketik dengan nama yang benar.');
            $this->prodi_id = null; 
            return; 
        }
        // -------------------------------------------------

        $this->validate($rules);

        // Logika update data
        $user = User::findOrFail($this->userId);
        $user->update(['email' => $this->email]);

        if ($this->password) {
            $user->update(['password' => Hash::make($this->password)]);
        }

        if ($this->roleType === 'admin') {
            $user->admin->update(['name' => $this->name, 'prodi_id' => $this->prodi_id]);
        } elseif ($this->roleType === 'dosen') {
            $user->dosen->update(['name' => $this->name, 'nip' => $this->nip, 'prodi_id' => $this->prodi_id]);
        } elseif ($this->roleType === 'mahasiswa') {
            $user->mahasiswa->update([
                'name' => $this->name,
                'nim' => $this->nim,
                'tahun_masuk' => $this->tahun_masuk,
                'prodi_id' => $this->prodi_id,
            ]);
        }

        $this->showModal = false;
        $this->dispatch('toast', message: '✅ Data pengguna berhasil diperbarui.');
    }
    
    // --- METODE HAPUS (TETAP SAMA) ---
    public function confirmDelete($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            session()->flash('error', 'Pengguna tidak ditemukan.');
            return;
        }

        // Cek apakah user yang akan dihapus adalah user yang sedang login
        if (Auth::id() === $user->id) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        $this->userIdToDelete = $userId;
        $this->userEmailToDelete = $user->email;
        $this->showDeleteConfirmation = true;
    }

    /**
     * Mengeksekusi penghapusan pengguna
     */
    public function deleteUser()
    {
        if (!$this->userIdToDelete) {
            $this->cancelDelete();
            return;
        }

        try {
            $user = User::findOrFail($this->userIdToDelete);
            $user->admin()->delete();
            $user->dosen()->delete();
            $user->mahasiswa()->delete();
            $user->delete();

            session()->flash('success', 'Pengguna dan data terkait berhasil dihapus.');
            
            $this->reset(['showDeleteConfirmation', 'userIdToDelete', 'userEmailToDelete']);
            $this->resetPage(); 
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus pengguna. ' . $e->getMessage());
        }
    }

    public function cancelDelete()
    {
        $this->reset(['showDeleteConfirmation', 'userIdToDelete', 'userEmailToDelete']);
    }
}