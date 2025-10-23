<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeluhPengguna extends Model
{
    use HasFactory;

    protected $table = 'keluh_penggunas';

    /**
     * Kolom yang bisa diisi secara massal.
     */
    protected $fillable = [
        'perjalanan_id',
        'nama_pengguna',
        'nama_tempat',
        'komentar',
        'foto',
    ];

    /**
     * Relasi ke tabel perjalanan (many-to-one).
     */
    public function perjalanan(): BelongsTo
    {
        return $this->belongsTo(Perjalanan::class, 'perjalanan_id');
    }
}
