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
        'user_id',
        'nama_pengguna',
        'nama_tempat',
        'komentar',
        'foto',
    ];

    /**
     * Relasi ke tabel perjalanan (many-to-one).
     */
    public function user()
    {
        return $this->belongsTo(User::class); 
    }

    public function perjalanan()
    {
        return $this->belongsTo(Perjalanan::class); 
    }
}
