<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Authenticatable
{
    protected $table = 'mahasiswas';
    
    protected $fillable = [
        'nim',
        'nama_lengkap',
        'email',
        'no_telp',
        'password',
        'jurusan',
        'prodi',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Relasi ke peminjaman mahasiswa
    public function peminjamanMahasiswa(): HasMany
    {
        return $this->hasMany(PeminjamanMahasiswa::class, 'mahasiswa_id');
    }

    // Override username untuk login dengan NIM
    public function getAuthIdentifierName()
    {
        return 'nim';
    }
}
