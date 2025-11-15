<?php
// app/Models/BarangHabisPakai.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangHabisPakai extends Model
{
    use HasFactory;
    protected $table = 'barang_habis_pakai';

    protected $fillable = [
    'nama_barang',
    'kode_barang',
    'total_stok',
    'batas_minimum',
    'satuan',
    'tahun_perolehan',
];


    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_expired' => 'date',
        'harga_satuan' => 'decimal:2',
    ];

       public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class, 'barang_id')
            ->where('jenis_barang', 'habis');
    }
}