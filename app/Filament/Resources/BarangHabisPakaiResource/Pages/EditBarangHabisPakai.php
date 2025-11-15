<?php

namespace App\Filament\Resources\BarangHabisPakaiResource\Pages;

use App\Filament\Resources\BarangHabisPakaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBarangHabisPakai extends EditRecord
{
    protected static string $resource = BarangHabisPakaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
