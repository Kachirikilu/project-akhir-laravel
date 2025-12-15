<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Admin; // <-- IMPORT Model Admin
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB; // <-- Tambahkan untuk Transaction

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $adminKey = env('ADMIN_KEY', 'nrgKnSD$ZJP9sUh');

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'admin_key' => ['nullable', 'string'], 
        ])->after(function ($validator) use ($input, $adminKey) {
            if (isset($input['admin_key']) && $input['admin_key'] !== $adminKey) {
                $validator->errors()->add(
                    'admin_key',
                    'Kunci admin tidak valid.'
                );
            }
        })->validate();

        $isAdmin = (isset($input['admin_key']) && $input['admin_key'] === $adminKey);

        // **CATATAN PENTING:** // Anda harus menambahkan kolom 'is_admin' (boolean) ke tabel users 
        // sebelum menjalankan kode ini, jika belum.
        
        return DB::transaction(function () use ($input, $isAdmin) {
            // 1. Buat User utama (tetap simpan nama di sini sesuai standar Fortify/Jetstream)
            $user = User::create([
                // 'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'is_admin' => $isAdmin, 
            ]);

            // 2. Jika user adalah Admin, buat entri di tabel 'admins'
            if ($isAdmin) {
                Admin::create([
                    'user_id' => $user->id,
                    'name' => $input['name'], // Menyalin nama ke tabel admin
                    'prodi_id' => null, // Sesuai permintaan (nullable)
                ]);
            }

            return $user;
        });
    }
}
