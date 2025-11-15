<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Notifications\Notification;

class Login extends BaseLogin
{
    protected function throwFailureValidationException(): never
    {
        Notification::make()
            ->title('Login Gagal')
            ->body('Email atau password salah.')
            ->danger()
            ->send();

        parent::throwFailureValidationException(); // panggil bawaan Filament
    }
}
