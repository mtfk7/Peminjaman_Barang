<?php

namespace App\Observers;

use App\Models\Peminjaman;
use App\Models\BarangHabisPakai;
use App\Models\BarangTidakHabisPakai;

class PeminjamanObserver
{
    /**
     * Saat peminjaman dibuat (status = "Dipinjam")
     */
    public function created(Peminjaman $peminjaman): void
    {
        // Barang habis pakai → stok berkurang
        if ($peminjaman->jenis_barang === 'habis_pakai') {
            $barang = BarangHabisPakai::find($peminjaman->barang_id);

            if ($barang) {
                $barang->decrement('total_stok', $peminjaman->jumlah);
            }
        }

        // Barang tidak habis pakai → stok berkurang dari stok_baik
        if ($peminjaman->jenis_barang === 'tidak_habis_pakai') {
            $barang = BarangTidakHabisPakai::find($peminjaman->barang_id);

            if ($barang) {
                $barang->stok_baik -= $peminjaman->jumlah;
                $barang->total_stok = $barang->stok_baik
                    + $barang->stok_kurang_baik
                    + $barang->stok_tidak_baik;
                $barang->save();
            }
        }
    }

    /**
     * Saat peminjaman dikembalikan
     */
    public function updated(Peminjaman $peminjaman): void
    {
        // Jalankan hanya jika status berubah menjadi "Dikembalikan"
        if (! $peminjaman->isDirty('status') || $peminjaman->status !== 'Dikembalikan') {
            return;
        }

        $now = now('Asia/Makassar');

        // Update tanggal & jam kembali jika belum ada
        $peminjaman->updateQuietly([
            'tanggal_kembali' => $peminjaman->tanggal_kembali ?? $now->toDateString(),
            'jam_kembali' => $peminjaman->jam_kembali ?? $now->format('H:i:s'),
        ]);

        // ✅ Hanya proses pengembalian untuk barang tidak habis pakai
        if ($peminjaman->jenis_barang === 'tidak_habis_pakai') {
            $barang = BarangTidakHabisPakai::find($peminjaman->barang_id);
            if (! $barang) return;

            $jumlah = (int) $peminjaman->jumlah;
            $kondisi = strtolower($peminjaman->kondisi_kembali);

            // Tambah stok ke kategori kondisi yang sesuai
            if ($kondisi === 'baik') {
                $barang->stok_baik += $jumlah;
            } elseif ($kondisi === 'kurang_baik') {
                $barang->stok_kurang_baik += $jumlah;
            } elseif ($kondisi === 'tidak_baik') {
                $barang->stok_tidak_baik += $jumlah;
            }

            // Hitung ulang total stok
            $barang->total_stok = $barang->stok_baik
                + $barang->stok_kurang_baik
                + $barang->stok_tidak_baik;

            $barang->save();
        }

        // ❌ Barang habis pakai tidak dikembalikan (stok tidak bertambah)
    }
}
