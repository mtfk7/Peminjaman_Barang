<?php

namespace App\Filament\Resources\DataBarangTidakHabisPakaiResource\Pages;

use App\Filament\Resources\DataBarangTidakHabisPakaiResource;
use Filament\Resources\Pages\ListRecords;

class ListDataBarangTidakHabisPakais extends ListRecords
{
    protected static string $resource = DataBarangTidakHabisPakaiResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Hilangkan tombol Create/Edit/Delete
    }
}
