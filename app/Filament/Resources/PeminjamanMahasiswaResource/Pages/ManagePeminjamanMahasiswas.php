<?php

namespace App\Filament\Resources\PeminjamanMahasiswaResource\Pages;

use App\Filament\Resources\PeminjamanMahasiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ManagePeminjamanMahasiswas extends ListRecords
{
    protected static string $resource = PeminjamanMahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
