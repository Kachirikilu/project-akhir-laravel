<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Illuminate\Support\Facades\DB; // <-- Import DB untuk Transaction

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        // Gunakan Transaction untuk memastikan konsistensi antara tabel users dan admins
        DB::transaction(function () use ($user, $input) {
            // 1. Update Profile Photo
            if (isset($input['photo'])) {
                $user->updateProfilePhoto($input['photo']);
            }

            // 2. Update User Record
            if ($input['email'] !== $user->email &&
                $user instanceof MustVerifyEmail) {
                $this->updateVerifiedUser($user, $input);
            } else {
                $user->forceFill([
                    // jangan isi 'name' di tabel users karena kolom name dipindahkan ke tabel terkait
                    'email' => $input['email'],
                ])->save();
            }

            // Update nama pada model terkait jika ada (admin / dosen / mahasiswa)
            if (isset($input['name'])) {
                if ($user->admin) {
                    $user->admin->forceFill([
                        'name' => $input['name'],
                    ])->save();
                }

                if ($user->dosen) {
                    $user->dosen->forceFill([
                        'name' => $input['name'],
                    ])->save();
                }

                if ($user->mahasiswa) {
                    $user->mahasiswa->forceFill([
                        'name' => $input['name'],
                    ])->save();
                }
            }
        });
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        // Jangan coba simpan 'name' ke tabel users (kolom dihapus), hanya update email dan reset verifikasi
        $user->forceFill([
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();

        // Perbarui nama pada relasi jika ada
        if (isset($input['name'])) {
            if ($user->admin) {
                $user->admin->forceFill(['name' => $input['name']])->save();
            }
            if ($user->dosen) {
                $user->dosen->forceFill(['name' => $input['name']])->save();
            }
            if ($user->mahasiswa) {
                $user->mahasiswa->forceFill(['name' => $input['name']])->save();
            }
        }
    }
}
