<?php

namespace App\Observers;

use App\Models\BarangMasuk;
use App\Models\BarangHabisPakai;
use App\Models\BarangTidakHabisPakai;

class BarangMasukObserver
{
    /**
     * Handle the BarangMasuk "created" event.
     */
    public function created(BarangMasuk $barangMasuk)
    {
        if ($barangMasuk->jenis_barang === 'habis') {
            // Tambahkan ke stok barang habis pakai
            $barang = BarangHabisPakai::find($barangMasuk->barang_id);
            if ($barang) {
                $barang->total_stok += $barangMasuk->jumlah_masuk;
                $barang->save();
            }
        } elseif ($barangMasuk->jenis_barang === 'tidak_habis') {
            // Tambahkan ke stok barang tidak habis pakai (stok_baik dan total_stok)
            $barang = BarangTidakHabisPakai::find($barangMasuk->barang_id);
            if ($barang) {
                $barang->stok_baik += $barangMasuk->jumlah_masuk;
                $barang->total_stok += $barangMasuk->jumlah_masuk;
                $barang->save();
            }
        }
    }

      public function updated(BarangMasuk $barangMasuk)
    {
        // Ambil nilai lama sebelum update
        $oldJumlah = $barangMasuk->getOriginal('jumlah_masuk');
        $newJumlah = $barangMasuk->jumlah_masuk;
        $selisih = $newJumlah - $oldJumlah;

        if ($barangMasuk->jenis_barang === 'habis') {
            $barang = BarangHabisPakai::find($barangMasuk->barang_id);
            if ($barang) {
                $barang->total_stok += $selisih; // bisa nambah atau ngurang
                $barang->save();
            }
        } elseif ($barangMasuk->jenis_barang === 'tidak_habis') {
            $barang = BarangTidakHabisPakai::find($barangMasuk->barang_id);
            if ($barang) {
                $barang->stok_baik += $selisih;
                $barang->total_stok += $selisih;
                $barang->save();
            }
        }
    }
}
