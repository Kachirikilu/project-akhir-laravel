<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perjalanan extends Model
{
    use HasFactory;

    protected $table = 'perjalanans';

    protected $fillable = [
        'id_perjalanan',
        'nama_pengguna',
        'nama_tempat',
        'selesai',
    ];

    public function perjalananData(): HasMany
    {
        return $this->hasMany(PerjalananData::class, 'perjalanan_id');
    }

    public function pengukuranSinyal(): HasMany
    {
        return $this->hasMany(PengukuranSinyal::class, 'perjalanan_id');
    }

    public function keluhPenggunas(): HasMany
    {
        return $this->hasMany(KeluhPengguna::class, 'perjalanan_id');
    }
}
