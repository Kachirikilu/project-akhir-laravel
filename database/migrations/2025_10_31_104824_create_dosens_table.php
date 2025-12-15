<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosens', function (Blueprint $table) {
            $table->id();
            
            // Kolom unik dan utama untuk dosen
            $table->string('nip')->unique(); 

            // Foreign Key ke tabel users (relasi 1:1)
            $table->foreignId('user_id')
                  ->constrained() 
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->unique(); 

            // Foreign Key ke tabel prodis (sementara nullable)
            $table->foreignId('prodi_id')->nullable(); 
            
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosens');
    }
};