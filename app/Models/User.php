<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Filament\Models\Contracts\HasName;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, Notifiable;
    use HasRoles;

    protected $fillable = [
        'nama_user',
        'username',
        'password',
        'role',
        'email',
        'no_telp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Nama yang akan ditampilkan di Filament (sidebar/header).
     */
     public function getFilamentName(): string 
    {
        return $this->nama_user
            ?? $this->username
            ?? $this->email
            ?? 'User';
    }

    /**
     * Email yang bisa ditampilkan di Filament (opsional).
     */
    public function getFilamentUserEmail(): string
    {
        return $this->email ?? '';
    }

    /**
     * Atur siapa saja yang bisa akses panel Filament.
     */
    public function canAccessPanel(Panel $panel): bool
{
    return in_array($this->role, [
        'admin',
        'Kepala',
        'Admin Persediaan Barang',
        'Admin Peminjaman Barang',
    ]);
}

}
