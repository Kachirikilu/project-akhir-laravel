<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perjalanans', function (Blueprint $table) {
            $table->id();
            $table->string('id_perjalanan')->unique()->comment('ID unik sesi dari log NMF atau UUID');
            $table->string('nama_pengguna');
            $table->string('nama_tempat');
            $table->boolean('selesai')->default(false)->comment('Menandakan apakah perbaikan telah dilaksanakan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perjalanans');
    }
};
