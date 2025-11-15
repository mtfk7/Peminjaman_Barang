<?php

namespace App\Filament\Resources\BarangHabisPakaiResource\Pages;

use App\Filament\Resources\BarangHabisPakaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\BarangHabisPakai;
use Filament\Notifications\Notification;

class ListBarangHabisPakais extends ListRecords
{
    protected static string $resource = BarangHabisPakaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

   public function mount(): void
    {
        // cek stok yang sudah <= batas minimum
        $barangMenipis = BarangHabisPakai::whereColumn('total_stok', '<=', 'batas_minimum')->get();

        foreach ($barangMenipis as $barang) {
            Notification::make()
                ->title("Stok {$barang->nama_barang} menipis!")
                ->body("Sisa stok: {$barang->total_stok}. Harap restock segera.")
                ->danger()
                ->send();
        }
    }
}
