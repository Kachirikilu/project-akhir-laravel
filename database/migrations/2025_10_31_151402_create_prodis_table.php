<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('prodis', function (Blueprint $table) {
            $table->id(); // kolom id auto increment (primary key)
            $table->string('nama_prodi'); // contoh: Teknik Elektro
            $table->string('jurusan'); // contoh: Teknik
            $table->string('fakultas'); // contoh: Fakultas Teknik
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('prodis');
    }
};
