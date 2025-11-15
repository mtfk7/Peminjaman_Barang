<?php

namespace App\Filament\Resources\BarangMasukResource\Pages;

use App\Filament\Resources\BarangMasukResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBarangMasuk extends CreateRecord
{
    protected static string $resource = BarangMasukResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Barang masuk berhasil ditambahkan!';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
{
    if ($data['jenis_barang'] === 'tidak_habis') {
        $barang = \App\Models\BarangTidakHabisPakai::find($data['barang_id']);
    } else {
        $barang = \App\Models\BarangHabisPakai::find($data['barang_id']);
    }

    $data['satuan'] = $barang?->satuan;

    return $data;
}

}
