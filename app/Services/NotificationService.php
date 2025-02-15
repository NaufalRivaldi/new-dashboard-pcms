<?php

namespace App\Services;

use Filament\Notifications\Notification;

class NotificationService
{
    public static function success(string $title, string $body = null): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->success()
            ->duration(5000)
            ->send();
    }

    public static function warning(string $title, string $body = null): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->warning()
            ->duration(5000)
            ->send();
    }

    public static function danger(string $title, string $body = null): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->danger()
            ->duration(5000)
            ->send();
    }
}