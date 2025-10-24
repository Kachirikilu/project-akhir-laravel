<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keluh_penggunas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perjalanan_id')
                  ->nullable()
                  ->constrained('perjalanans')
                  ->nullOnDelete()
                  ->comment('Relasi ke perjalanan yang terkait');
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->string('nama_pengguna')->comment('Nama pengguna yang mengajukan keluhan');
            $table->string('nama_tempat')->comment('Tempat spesifik dari keluhan pengguna');
            $table->text('komentar')->nullable()->comment('Isi keluhan pengguna');
            $table->string('foto')->nullable()->comment('Foto atau screenshot bukti keluhan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keluh_penggunas');
    }
};
