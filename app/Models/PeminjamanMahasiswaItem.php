<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeminjamanMahasiswaItem extends Model
{
    protected $table = 'peminjaman_mahasiswa_items';
    
    protected $fillable = [
        'peminjaman_mahasiswa_id',
        'barang_id',
        'nama_barang',
        'jenis_barang',
        'jumlah',
        'satuan',
        'status',
        'jam_kembali',
    ];

    protected $casts = [
        'jam_kembali' => 'string',
    ];

    // Relasi ke PeminjamanMahasiswa (header)
    public function peminjamanMahasiswa(): BelongsTo
    {
        return $this->belongsTo(PeminjamanMahasiswa::class, 'peminjaman_mahasiswa_id');
    }

    // Helper untuk status badge
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'dikembalikan' => 'info',
            default => 'secondary'
        };
    }

    // Helper untuk status label
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Approval',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'dikembalikan' => 'Sudah Dikembalikan',
            default => $this->status
        };
    }
}
