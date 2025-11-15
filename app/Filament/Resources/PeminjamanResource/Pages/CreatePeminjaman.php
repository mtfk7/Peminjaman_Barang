<?php

namespace App\Filament\Resources\PeminjamanResource\Pages;

use App\Filament\Resources\PeminjamanResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\BarangTidakHabisPakai;
use App\Models\BarangHabisPakai;
use Filament\Notifications\Notification;

class CreatePeminjaman extends CreateRecord
{
    protected static string $resource = PeminjamanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Cek jenis barang dulu
        if ($data['jenis_barang'] === 'habis_pakai') {
            $barang = BarangHabisPakai::find($data['barang_id']);
        } else {
            $barang = BarangTidakHabisPakai::find($data['barang_id']);
        }

        if (!$barang) {
            $this->halt();
        }

        // Hitung total stok
        if ($data['jenis_barang'] === 'habis_pakai') {
            $totalStok = $barang->total_stok ?? 0;
        } else {
            $totalStok = ($barang->stok_baik ?? 0)
                + ($barang->stok_kurang_baik ?? 0)
                + ($barang->stok_tidak_baik ?? 0);
        }

        // Cek apakah stok mencukupi
        if ($totalStok < $data['jumlah']) {
            Notification::make()
                ->title('Stok tidak mencukupi!')
                ->danger()
                ->send();

            $this->halt();
        }

        // ❌ Tidak perlu mengubah stok di sini
        // Observer sudah akan menangani update stok

        return $data;
    }
}
