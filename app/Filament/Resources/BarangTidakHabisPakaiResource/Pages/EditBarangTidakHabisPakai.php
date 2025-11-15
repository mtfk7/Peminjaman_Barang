<?php

namespace App\Filament\Resources\BarangTidakHabisPakaiResource\Pages;

use App\Filament\Resources\BarangTidakHabisPakaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBarangTidakHabisPakai extends EditRecord
{
    protected static string $resource = BarangTidakHabisPakaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
