<?php

namespace App\Filament\Resources\DataBarangHabisPakaiResource\Pages;

use App\Filament\Resources\DataBarangHabisPakaiResource;
use Filament\Resources\Pages\ListRecords;
use App\Models\BarangHabisPakai;
use Filament\Notifications\Notification;



class ListDataBarangHabisPakais extends ListRecords
{
    protected static string $resource = DataBarangHabisPakaiResource::class;

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    // protected function getTableQuery()
    // {
    //     return parent::getTableQuery();
    // }

    public function mount(): void
    {
        parent::mount();

        // Cek barang yang sudah di bawah atau sama dengan batas minimum
        $barangKurang = BarangHabisPakai::whereColumn('total_stok', '<=', 'batas_minimum')->get();

        if ($barangKurang->isNotEmpty()) {
            $namaBarang = $barangKurang->pluck('nama_barang')->join(', ');

            Notification::make()
                ->title('⚠️ Stok Barang Menipis')
                ->body("Barang berikut sudah mencapai batas minimum: {$namaBarang}. Segera lakukan restock!")
                ->danger()
                ->duration(5000)
                ->send();
        }
    }
}
