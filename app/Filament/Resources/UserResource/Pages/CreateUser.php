<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

protected function mutateFormDataBeforeCreate(array $data): array
{
    $user = new \App\Models\User($data);
    $user->assignRole($data['role']);
    return $data;
}


}
