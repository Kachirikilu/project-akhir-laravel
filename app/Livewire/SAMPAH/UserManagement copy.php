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

class ManageUsers extends Component
{
    use WithPagination;
    protected $paginationTheme = 'tailwind';

    // ... (Properti Filter existing) ...
    public $search = '';
    public $filter = '';
    public $prodiSearchQuery = '';
    public $prodiSearchResults = [];
    public $selectedProdiId = null;
    public $selectedProdiName = '';

    // Properti Modal
    public $showModal = false;
    public $isEditing = false;
    public $roleType;

    // Properti Konfirmasi Hapus
    public $showDeleteConfirmation = false;
    public $userIdToDelete = null;
    public $userEmailToDelete = '';

    // Form fields
    public $userId;
    public $email;
    public $password;
    public $name;
    public $nip;
    public $nim;
    public $tahun_masuk;
    public $prodi_id; 

    // --- PROPERTI UNTUK AUTOCOMPLETE ---
    public $prodi_name_search = '';
    public $prodi_results = [];
    public $initial_prodi_recommendations = []; // BARU: Menampung 5 prodi awal/favorit/terbaru
    // ------------------------------------

    protected $updatesQueryString = ['search', 'filter', 'prodiSearchQuery'];

    protected $rules = [
        'email' => 'required|email|max:255',
        'name' => 'required|string|max:255',
        'password' => 'nullable|min:8',
    ];

    public function mount()
    {
        // Ambil 5 prodi teratas/paling relevan saat komponen dimuat pertama kali
        $this->initial_prodi_recommendations = Prodi::limit(5)
            ->get(['id', 'nama_prodi'])
            ->map(fn($p) => ['id' => $p->id, 'name' => $p->nama_prodi]) // Map untuk konsistensi
            ->toArray();
    }


    // Method untuk pencarian Prodi secara real-time di Modal (Autocomplete)
    public function updatedProdiNameSearch($value)
    {
        // Reset prodi_id saat user mulai mengetik pencarian baru
        $this->prodi_id = null;
        $this->resetErrorBag(['prodi_id']);

        $value = trim($value);

        if (strlen($value) >= 2) {
            $results = Prodi::where('nama_prodi', 'like', '%' . $value . '%')
                ->orWhere('id', 'like', '%' . $value . '%')
                ->limit(10)
                ->get(['id', 'nama_prodi']);

            // Mapping results agar Blade tetap menggunakan key 'name' untuk nama prodi
            $this->prodi_results = $results->map(function ($prodi) {
                return [
                    'id' => $prodi->id,
                    'name' => $prodi->nama_prodi,
                ];
            })->toArray();
        } elseif (empty($value)) {
            // Jika input kosong, tampilkan rekomendasi awal di Blade (tidak perlu query lagi)
            $this->prodi_results = [];
        } else {
            // Jika input < 2 karakter dan tidak kosong, jangan tampilkan apa-apa kecuali rekomendasi awal
            $this->prodi_results = [];
        }
    }

    // Method untuk memilih hasil pencarian dari Autocomplete
    public function selectProdi($prodiId, $prodiName)
    {
        $this->prodi_id = $prodiId;
        $this->prodi_name_search = $prodiName;
        $this->prodi_results = []; 
        $this->resetErrorBag(['prodi_id', 'prodi_name_search']);
    }

    // Dipanggil saat modal dibuka atau reset
    public function resetInput()
    {
        $this->reset([
            'userId', 'email', 'password', 'name', 'nip', 'nim', 'tahun_masuk', 
            'prodi_id', 'prodi_name_search', 'prodi_results', // 'initial_prodi_recommendations' tidak direset
            'roleType'
        ]);
        $this->resetErrorBag();
    }

    // ... (render(), saveUser(), editUser(), updateUser(), dan metode lainnya tetap sama) ...
    // Hapus saja fungsi render() dan ganti dengan:
    public function render()
    {
        // Anda perlu menambahkan logika render Anda di sini (disimpan dari kode sebelumnya)
        return view('livewire.user-management', [
            // Tambahkan data yang dibutuhkan oleh view jika ada
            'prodis' => Prodi::all(), // Contoh data lain
        ]);
    }

    // Pastikan metode CRUD lainnya (saveUser, updateUser, dll) ada di sini
    // ...
}
