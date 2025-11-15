<?php

namespace App\Filament\Resources\PeminjamanResource\Pages;

use App\Filament\Resources\PeminjamanResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\BarangTidakHabisPakai;

class EditPeminjaman extends EditRecord
{
    protected static string $resource = PeminjamanResource::class;

    protected function saved(): void
    {
        $peminjaman = $this->record;

        if ($peminjaman->status === 'dikembalikan' && $peminjaman->kondisi_kembali) {
            $barang = BarangTidakHabisPakai::find($peminjaman->barang_id);

            if ($barang) {
                match ($peminjaman->kondisi_kembalin) {
                    'baik' => $barang->increment('stok_baik', $peminjaman->jumlah),
                    'kurang_baik' => $barang->increment('stok_kurang_baik', $peminjaman->jumlah),
                    'tidak_baik' => $barang->increment('stok_tidak_baik', $peminjaman->jumlah),
                    default => null,
                };
            }
        }
    }


}
