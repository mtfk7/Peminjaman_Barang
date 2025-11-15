<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BarangHabisPakai;
use App\Models\BarangTidakHabisPakai;

class BarangMasuk extends Model
{
    protected $fillable = [
        'jenis_barang',
        'barang_id',
        'jumlah_masuk',
        'tanggal_masuk',
        'keterangan',
        'satuan',
    ];

    // Relasi sesuai jenis barang
    public function barangHabisPakai()
    {
        return $this->belongsTo(BarangHabisPakai::class, 'barang_id');
    }

    public function barangTidakHabisPakai()
    {
        return $this->belongsTo(BarangTidakHabisPakai::class, 'barang_id');
    }

    // Accessor nama barang dinamis
    public function getNamaBarangAttribute()
    {
        if ($this->jenis_barang === 'habis' && $this->barangHabisPakai) {
            return $this->barangHabisPakai->nama_barang;
        }

        if ($this->jenis_barang === 'tidak_habis' && $this->barangTidakHabisPakai) {
            return $this->barangTidakHabisPakai->nama_barang;
        }

        return '-';
    }

    protected static function booted()
    {
        // static::created(function ($barangMasuk) {
        //     if ($barangMasuk->jenis_barang === 'habis') {
        //         $barang = BarangHabisPakai::find($barangMasuk->barang_id);
        //         if ($barang) {
        //             $barang->increment('total_stok', $barangMasuk->jumlah_masuk);
        //         }
        //     }

        //     if ($barangMasuk->jenis_barang === 'tidak_habis') {
        //         $barang = BarangTidakHabisPakai::find($barangMasuk->barang_id);
        //         if ($barang) {
        //             $barang->increment('stok_baik', $barangMasuk->jumlah_masuk);
        //             $barang->increment('total_stok', $barangMasuk->jumlah_masuk);
        //         }
        //     }
        // });

        static::deleted(function ($barangMasuk) {
            if ($barangMasuk->jenis_barang === 'habis') {
                $barang = BarangHabisPakai::find($barangMasuk->barang_id);
                if ($barang) {
                    $barang->decrement('total_stok', $barangMasuk->jumlah_masuk);
                }
            }

            if ($barangMasuk->jenis_barang === 'tidak_habis') {
                $barang = BarangTidakHabisPakai::find($barangMasuk->barang_id);
                if ($barang) {
                    $barang->decrement('stok_baik', $barangMasuk->jumlah_masuk);
                    $barang->decrement('total_stok', $barangMasuk->jumlah_masuk);
                }
            }
        });
    }
}
