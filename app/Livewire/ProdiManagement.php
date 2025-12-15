<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Prodi;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Admin;

class ProdiManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $searchUser = '';
    public $showModal = false;
    public $showDetail = false;
    public $isEditing = false;

    public $nama_prodi, $jurusan, $fakultas;
    public $selectedProdiId;

    public $userType = 'mahasiswa';
    public $userResults = [];
    public $selectedUsers = [];

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'nama_prodi' => 'required|string|max:255',
        'jurusan' => 'required|string|max:255',
        'fakultas' => 'required|string|max:255',
    ];

    // ========== Render ==========
    public function render()
    {
        $prodis = Prodi::where('nama_prodi', 'like', "%{$this->search}%")
            ->orWhere('jurusan', 'like', "%{$this->search}%")
            ->orWhere('fakultas', 'like', "%{$this->search}%")
            ->orderBy('nama_prodi')
            ->paginate(10);

        return view('livewire.prodi-management', compact('prodis'));
    }

    // ========== Tambah / Edit ==========
    public function showAddModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function editProdi($id)
    {
        $prodi = Prodi::findOrFail($id);
        $this->selectedProdiId = $prodi->id;
        $this->nama_prodi = $prodi->nama_prodi;
        $this->jurusan = $prodi->jurusan;
        $this->fakultas = $prodi->fakultas;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function saveProdi()
    {
        $this->validate();

        Prodi::create([
            'nama_prodi' => $this->nama_prodi,
            'jurusan' => $this->jurusan,
            'fakultas' => $this->fakultas,
        ]);

        $this->resetForm();
        $this->showModal = false;
        session()->flash('success', 'Prodi berhasil ditambahkan!');
    }

    public function updateProdi()
    {
        $this->validate();

        $prodi = Prodi::findOrFail($this->selectedProdiId);
        $prodi->update([
            'nama_prodi' => $this->nama_prodi,
            'jurusan' => $this->jurusan,
            'fakultas' => $this->fakultas,
        ]);

        $this->resetForm();
        $this->showModal = false;
        session()->flash('success', 'Prodi berhasil diperbarui!');
    }

    public function deleteProdi($id)
    {
        Prodi::findOrFail($id)->delete();
        session()->flash('success', 'Prodi berhasil dihapus!');
    }

    // ========== Detail / Assign ==========
    public function showDetail($id)
    {
        $this->selectedProdiId = $id;
        $this->searchUser = '';
        $this->userResults = [];
        $this->selectedUsers = [];
        $this->showDetail = true;
    }

    public function updatedSearchUser()
    {
        if (strlen($this->searchUser) < 2) {
            $this->userResults = [];
            return;
        }

        switch ($this->userType) {
            case 'mahasiswa':
                $this->userResults = Mahasiswa::with('user')
                    ->whereNull('prodi_id')
                    ->where(function ($query) {
                        $query->where('name', 'like', "%{$this->searchUser}%")
                            ->orWhere('nim', 'like', "%{$this->searchUser}%");
                    })->take(10)->get();
                break;

            case 'dosen':
                $this->userResults = Dosen::with('user')
                    ->whereNull('prodi_id')
                    ->where(function ($query) {
                        $query->where('name', 'like', "%{$this->searchUser}%")
                            ->orWhere('nip', 'like', "%{$this->searchUser}%");
                    })->take(10)->get();
                break;

            case 'admin':
                $this->userResults = Admin::with('user')
                    ->whereNull('prodi_id')
                    ->where('name', 'like', "%{$this->searchUser}%")
                    ->take(10)->get();
                break;
        }
    }

    public function assignUsersToProdi()
    {
        if (empty($this->selectedUsers)) return;

        switch ($this->userType) {
            case 'mahasiswa':
                Mahasiswa::whereIn('id', $this->selectedUsers)->update(['prodi_id' => $this->selectedProdiId]);
                break;
            case 'dosen':
                Dosen::whereIn('id', $this->selectedUsers)->update(['prodi_id' => $this->selectedProdiId]);
                break;
            case 'admin':
                Admin::whereIn('id', $this->selectedUsers)->update(['prodi_id' => $this->selectedProdiId]);
                break;
        }

        $this->reset(['userResults', 'selectedUsers', 'showDetail']);
        session()->flash('success', 'Pengguna berhasil ditambahkan ke prodi!');
    }

    // ========== Utilitas ==========
    private function resetForm()
    {
        $this->reset(['nama_prodi', 'jurusan', 'fakultas', 'selectedProdiId']);
    }
}
