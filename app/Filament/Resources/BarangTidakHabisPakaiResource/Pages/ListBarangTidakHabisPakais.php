<?php

namespace App\Filament\Resources\BarangTidakHabisPakaiResource\Pages;

use App\Filament\Resources\BarangTidakHabisPakaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBarangTidakHabisPakais extends ListRecords
{
    protected static string $resource = BarangTidakHabisPakaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
