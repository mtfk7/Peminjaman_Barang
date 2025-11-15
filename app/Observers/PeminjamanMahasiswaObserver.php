<?php

namespace App\Observers;

use App\Models\PeminjamanMahasiswa;
use App\Models\BarangHabisPakai;
use App\Models\BarangTidakHabisPakai;

class PeminjamanMahasiswaObserver
{
    /**
     * Handle the PeminjamanMahasiswa "created" event.
     */
    public function created(PeminjamanMahasiswa $peminjamanMahasiswa): void
    {
        //
    }

    /**
     * Handle the PeminjamanMahasiswa "updated" event.
     */
    public function updated(PeminjamanMahasiswa $peminjamanMahasiswa): void
    {
        // Cek apakah status berubah
        $oldStatus = $peminjamanMahasiswa->getOriginal('status');
        $newStatus = $peminjamanMahasiswa->status;

        // Jika status berubah dari pending ke approved, kurangi stok
        if ($oldStatus === 'pending' && $newStatus === 'approved') {
            $this->kurangiStok($peminjamanMahasiswa);
            
            // Untuk barang habis pakai, langsung otomatis set ke dikembalikan dengan jam_kembali
            if ($peminjamanMahasiswa->jenis_barang === 'habis_pakai') {
                $peminjamanMahasiswa->status = 'dikembalikan';
                $peminjamanMahasiswa->jam_kembali = now()->format('H:i:s');
                $peminjamanMahasiswa->saveQuietly(); // Save tanpa trigger observer lagi untuk menghindari loop
            }
        }
        
        // Jika status berubah dari approved ke dikembalikan, tambah stok kembali dan set jam_kembali
        // Hanya untuk barang tidak habis pakai (barang habis pakai tidak perlu dikembalikan)
        if ($oldStatus === 'approved' && $newStatus === 'dikembalikan') {
            // Hanya tambah stok jika barang tidak habis pakai
            if ($peminjamanMahasiswa->jenis_barang === 'tidak_habis_pakai') {
                $this->tambahStok($peminjamanMahasiswa);
            }
            
            // Set jam_kembali otomatis jika belum ada
            if (empty($peminjamanMahasiswa->jam_kembali)) {
                $peminjamanMahasiswa->jam_kembali = now()->format('H:i:s');
                $peminjamanMahasiswa->saveQuietly(); // Save tanpa trigger observer lagi
            }
        }
        
        // Jika status berubah dari approved ke pending (rollback), tambah stok kembali
        // Hanya untuk barang tidak habis pakai
        if ($oldStatus === 'approved' && $newStatus === 'pending') {
            if ($peminjamanMahasiswa->jenis_barang === 'tidak_habis_pakai') {
                $this->tambahStok($peminjamanMahasiswa);
            }
        }
        
        // Jika status berubah dari dikembalikan ke approved (rollback), kurangi stok lagi
        // Hanya untuk barang tidak habis pakai
        if ($oldStatus === 'dikembalikan' && $newStatus === 'approved') {
            if ($peminjamanMahasiswa->jenis_barang === 'tidak_habis_pakai') {
                $this->kurangiStok($peminjamanMahasiswa);
            }
        }
    }

    /**
     * Kurangi stok barang saat peminjaman disetujui
     */
    private function kurangiStok(PeminjamanMahasiswa $peminjaman): void
    {
        if ($peminjaman->jenis_barang === 'habis_pakai') {
            $barang = BarangHabisPakai::find($peminjaman->barang_id);
            if ($barang && $barang->total_stok >= $peminjaman->jumlah) {
                $barang->total_stok -= $peminjaman->jumlah;
                $barang->save();
            }
        } else {
            $barang = BarangTidakHabisPakai::find($peminjaman->barang_id);
            if ($barang && $barang->total_stok >= $peminjaman->jumlah) {
                $barang->total_stok -= $peminjaman->jumlah;
                if ($barang->stok_baik >= $peminjaman->jumlah) {
                    $barang->stok_baik -= $peminjaman->jumlah;
                } else {
                    $barang->stok_rusak = max(0, $barang->stok_rusak - ($peminjaman->jumlah - $barang->stok_baik));
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
    private function tambahStok(PeminjamanMahasiswa $peminjaman): void
    {
        // Barang habis pakai tidak perlu dikembalikan, stok tidak ditambah kembali
        if ($peminjaman->jenis_barang === 'habis_pakai') {
            return; // Tidak perlu menambah stok untuk barang habis pakai
        }
        
        // Hanya untuk barang tidak habis pakai
        $barang = BarangTidakHabisPakai::find($peminjaman->barang_id);
        if ($barang) {
            $barang->total_stok += $peminjaman->jumlah;
            // Tambahkan ke stok baik (asumsi barang dikembalikan dalam kondisi baik)
            $barang->stok_baik += $peminjaman->jumlah;
            $barang->save();
        }
    }

    /**
     * Handle the PeminjamanMahasiswa "deleted" event.
     */
    public function deleted(PeminjamanMahasiswa $peminjamanMahasiswa): void
    {
        //
    }

    /**
     * Handle the PeminjamanMahasiswa "restored" event.
     */
    public function restored(PeminjamanMahasiswa $peminjamanMahasiswa): void
    {
        //
    }

    /**
     * Handle the PeminjamanMahasiswa "force deleted" event.
     */
    public function forceDeleted(PeminjamanMahasiswa $peminjamanMahasiswa): void
    {
        //
    }
}
