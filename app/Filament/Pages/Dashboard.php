<?php

namespace App\Filament\Pages;

use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends \Filament\Pages\Dashboard
{
    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ??
            static::$title ??
            __('Dashboard');
    }

    public function getTitle(): string | Htmlable
    {
        return static::$title ?? __('Dashboard');
    }
}