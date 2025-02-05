<?php

namespace App\Filament\Resources\ImportedFeeResource\Pages;

use App\Filament\Resources\ImportedFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportedFees extends ListRecords
{
    protected static string $resource = ImportedFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
