<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswas';

    protected $fillable = [
        'nim',
        'user_id',
        'prodi_id',
        'name',
        'tahun_masuk',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }
}