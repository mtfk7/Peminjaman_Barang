<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WelcomeCard extends Widget
{
    protected static string $view = 'filament.widgets.welcome-card';

    protected int | string | array $columnSpan = 'full';
}
