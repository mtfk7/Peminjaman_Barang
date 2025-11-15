<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangTidakHabisPakai extends Model
{
    use HasFactory;

    protected $table = 'barang_tidak_habis_pakai';

    protected $fillable = [
        'nama_barang',
        'kode_barang',
        'stok_baik',
        'stok_kurang_baik',
        'stok_tidak_baik',
        'satuan',
        'tahun_perolehan',
    ];

    // ✅ Hitung total stok otomatis
    protected static function booted()
    {
        static::saving(function ($barang) {
            $barang->total_stok =
                (int) ($barang->stok_baik ?? 0) +
                (int) ($barang->stok_kurang_baik ?? 0) +
                (int) ($barang->stok_tidak_baik ?? 0);
        });
    }

     public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class, 'barang_id')
            ->where('jenis_barang', 'tidak_habis');
    }
}
