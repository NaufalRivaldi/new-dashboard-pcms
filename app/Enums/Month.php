<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Month: string implements HasLabel
{
    case January = 1;
    case February = 2;
    case March = 3;
    case April = 4;
    case May = 5;
    case June = 6;
    case July = 7;
    case August = 8;
    case September = 9;
    case October = 10;
    case November = 11;
    case December = 12;

    public function getLabel(): ?string
    {
        return __($this->name);
    }

    public static function name(int $month): string
    {
        $case = collect(self::cases())->firstWhere('value', $month);

        return $case->name ?? '';
    }
}