<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;

class UpdateProfileInformationForm extends \Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm
{
    /**
     * Prepare the component.
     */
    public function mount()
    {
        // Jalankan mount parent untuk mengisi state awal
        parent::mount();

        $user = Auth::user();

        // Isi state.name dari relasi terkait (admin/dosen/mahasiswa) jika ada
        $name = null;
        if ($user) {
            $name = optional($user->admin)->name
                ?? optional($user->dosen)->name
                ?? optional($user->mahasiswa)->name
                ?? null;
        }

        // Jika ditemukan nama dari relasi, set ke state
        if ($name) {
            $this->state['name'] = $name;
        }
    }
}
