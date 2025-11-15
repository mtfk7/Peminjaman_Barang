<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BarangHabisPakai;
use App\Models\BarangTidakHabisPakai;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'barang_id',
        'jenis_barang', // habis_pakai / tidak_habis_pakai
        'nama_peminjam',
        'jumlah',
        'satuan',
        'tanggal_pinjam',
        'status',
        'jam_pinjam',
        'jam_kembali',
        'kelas',
        'kondisi_kembali',
    ];

    // Relasi ke Barang Habis Pakai
    public function barangHabisPakai()
    {
        return $this->belongsTo(BarangHabisPakai::class, 'barang_id');
    }

    // Relasi ke Barang Tidak Habis Pakai
    public function barangTidakHabisPakai()
    {
        return $this->belongsTo(BarangTidakHabisPakai::class, 'barang_id');
    }

    // relasi ke peminjamandetail
//     public function details()
// {
//     return $this->hasMany(PeminjamanDetail::class);
// }


    /**
     * Event booting
     */
    protected static function booted()
    {
        // Saat membuat peminjaman, auto set tanggal & jam
        static::creating(function ($record) {
            if (!$record->tanggal_pinjam) {
                $record->tanggal_pinjam = now()->toDateString();
            }
            if (!$record->jam_pinjam) {
                $record->jam_pinjam = now()->format('H:i:s');
            }
        });

        // Saat update status → auto isi jam kembali
        static::updating(function ($record) {
            if ($record->isDirty('status') && $record->status === 'Dikembalikan') {
                if (!$record->jam_kembali) {
                    $record->jam_kembali = now()->format('H:i:s');
                }
            }
        });

        // ❌ Tidak ada logika stok lagi → semua sudah diatur di PeminjamanObserver
    }
}
