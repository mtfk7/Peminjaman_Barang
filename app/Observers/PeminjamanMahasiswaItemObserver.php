<?php

namespace App\Observers;

use App\Models\PeminjamanMahasiswaItem;
use App\Models\BarangHabisPakai;
use App\Models\BarangTidakHabisPakai;

class PeminjamanMahasiswaItemObserver
{
    /**
     * Handle the PeminjamanMahasiswaItem "created" event.
     */
    public function created(PeminjamanMahasiswaItem $item): void
    {
        //
    }

    /**
     * Handle the PeminjamanMahasiswaItem "updated" event.
     */
    public function updated(PeminjamanMahasiswaItem $item): void
    {
        // Cek apakah status berubah
        $oldStatus = $item->getOriginal('status');
        $newStatus = $item->status;

        // Jika status berubah dari pending ke approved, kurangi stok
        if ($oldStatus === 'pending' && $newStatus === 'approved') {
            $this->kurangiStok($item);
            
            // Untuk barang habis pakai, langsung otomatis set ke dikembalikan dengan jam_kembali
            if ($item->jenis_barang === 'habis_pakai') {
                $item->status = 'dikembalikan';
                $item->jam_kembali = now()->format('H:i:s');
                $item->saveQuietly(); // Save tanpa trigger observer lagi untuk menghindari loop
            }
        }
        
        // Jika status berubah dari approved ke dikembalikan, tambah stok kembali dan set jam_kembali
        // Hanya untuk barang tidak habis pakai (barang habis pakai tidak perlu dikembalikan)
        if ($oldStatus === 'approved' && $newStatus === 'dikembalikan') {
            // Hanya tambah stok jika barang tidak habis pakai
            if ($item->jenis_barang === 'tidak_habis_pakai') {
                $this->tambahStok($item);
            }
            
            // Set jam_kembali otomatis jika belum ada
            if (empty($item->jam_kembali)) {
                $item->jam_kembali = now()->format('H:i:s');
                $item->saveQuietly(); // Save tanpa trigger observer lagi
            }
        }
        
        // Jika status berubah dari approved ke pending (rollback), tambah stok kembali
        // Hanya untuk barang tidak habis pakai
        if ($oldStatus === 'approved' && $newStatus === 'pending') {
            if ($item->jenis_barang === 'tidak_habis_pakai') {
                $this->tambahStok($item);
            }
        }
        
        // Jika status berubah dari dikembalikan ke approved (rollback), kurangi stok lagi
        // Hanya untuk barang tidak habis pakai
        if ($oldStatus === 'dikembalikan' && $newStatus === 'approved') {
            if ($item->jenis_barang === 'tidak_habis_pakai') {
                $this->kurangiStok($item);
            }
        }
    }

    /**
     * Kurangi stok barang saat peminjaman disetujui
     */
    private function kurangiStok(PeminjamanMahasiswaItem $item): void
    {
        if ($item->jenis_barang === 'habis_pakai') {
            $barang = BarangHabisPakai::find($item->barang_id);
            if ($barang && $barang->total_stok >= $item->jumlah) {
                $barang->total_stok -= $item->jumlah;
                $barang->save();
            }
        } else {
            $barang = BarangTidakHabisPakai::find($item->barang_id);
            if ($barang && $barang->total_stok >= $item->jumlah) {
                $barang->total_stok -= $item->jumlah;
                if ($barang->stok_baik >= $item->jumlah) {
                    $barang->stok_baik -= $item->jumlah;
                } else {
                    $barang->stok_rusak = max(0, $barang->stok_rusak - ($item->jumlah - $barang->stok_baik));
                    $barang->stok_baik = 0;
                }
                $barang->save();
            }
        }
    }

    /**
     * Tambah stok barang saat dikembalikan
     * Hanya untuk barang tidak habis pakai (barang habis pakai tidak perlu dikembalikan)
     */
    private function tambahStok(PeminjamanMahasiswaItem $item): void
    {
        // Barang habis pakai tidak perlu dikembalikan, stok tidak ditambah kembali
        if ($item->jenis_barang === 'habis_pakai') {
            return; // Tidak perlu menambah stok untuk barang habis pakai
        }
        
        // Hanya untuk barang tidak habis pakai
        $barang = BarangTidakHabisPakai::find($item->barang_id);
        if ($barang) {
            $barang->total_stok += $item->jumlah;
            // Tambahkan ke stok baik (asumsi barang dikembalikan dalam kondisi baik)
            $barang->stok_baik += $item->jumlah;
            $barang->save();
        }
    }

    /**
     * Handle the PeminjamanMahasiswaItem "deleted" event.
     */
    public function deleted(PeminjamanMahasiswaItem $item): void
    {
        //
    }

    /**
     * Handle the PeminjamanMahasiswaItem "restored" event.
     */
    public function restored(PeminjamanMahasiswaItem $item): void
    {
        //
    }

    /**
     * Handle the PeminjamanMahasiswaItem "force deleted" event.
     */
    public function forceDeleted(PeminjamanMahasiswaItem $item): void
    {
        //
    }
}
