<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasLabel
{
    case Active = 'active';
    case Inactive = 'inactive';

    public function getLabel(): ?string
    {
        return __($this->name);
    }
}