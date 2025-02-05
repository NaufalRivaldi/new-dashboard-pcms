<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentType: string implements HasLabel
{
    case Register = 'register';
    case Course = 'course';

    public function getLabel(): ?string
    {
        return __($this->name);
    }
}