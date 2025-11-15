<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PeminjamanMahasiswa extends Model
{
    protected $table = 'peminjaman_mahasiswa';
    
    protected $fillable = [
        'mahasiswa_id',
        'nama_mahasiswa',
        'nama_peminjam',
        'nim',
        'email',
        'no_telp',
        // Kolom barang sudah dipindah ke items (peminjaman_mahasiswa_items)
        // 'barang_id',
        // 'nama_barang',
        // 'jenis_barang',
        // 'jumlah',
        // 'satuan',
        'tanggal_pinjam',
        'tanggal_kembali',
        'jam_pinjam',
        'jam_kembali',
        'keperluan',
        'mata_kuliah',
        'kelas',
        'qr_code',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
        'jam_pinjam' => 'string',
        'jam_kembali' => 'string',
        'approved_at' => 'datetime',
    ];

    // Generate QR Code unik saat membuat peminjaman baru
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->qr_code)) {
                $model->qr_code = Str::uuid()->toString();
            }
        });
    }

    // Relasi ke Mahasiswa
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    // Relasi ke User yang meng-approve
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Relasi ke Items (multiple items dalam 1 peminjaman)
    public function items(): HasMany
    {
        return $this->hasMany(PeminjamanMahasiswaItem::class, 'peminjaman_mahasiswa_id');
    }

    // Helper untuk mendapatkan status keseluruhan (calculated dari items)
    public function getStatusKeseluruhanAttribute()
    {
        $items = $this->items;
        
        if ($items->isEmpty()) {
            return 'pending';
        }

        // Jika ada yang rejected, status keseluruhan = rejected
        if ($items->contains('status', 'rejected')) {
            return 'rejected';
        }

        // Jika semua pending, status = pending
        if ($items->every(fn($item) => $item->status === 'pending')) {
            return 'pending';
        }

        // Jika semua dikembalikan, status = dikembalikan
        // Perhatikan: barang habis pakai yang sudah approved otomatis menjadi dikembalikan oleh Observer
        // Tapi untuk safety, kita juga cek jika barang habis pakai sudah approved, dianggap dikembalikan
        $allReturned = $items->every(function($item) {
            // Barang habis pakai yang sudah approved dianggap dikembalikan (karena tidak perlu dikembalikan fisik)
            if ($item->jenis_barang === 'habis_pakai' && ($item->status === 'approved' || $item->status === 'dikembalikan')) {
                return true; // Dianggap sudah dikembalikan
            }
            // Barang tidak habis pakai harus status dikembalikan
            return $item->status === 'dikembalikan';
        });
        
        if ($allReturned) {
            return 'dikembalikan';
        }

        // Jika ada yang approved atau dikembalikan (campuran), status = approved (sebagian dikembalikan)
        return 'approved';
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
