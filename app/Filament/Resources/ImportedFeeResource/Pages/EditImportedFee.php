<?php

namespace App\Filament\Resources\ImportedFeeResource\Pages;

use App\Filament\Resources\ImportedFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImportedFee extends EditRecord
{
    protected static string $resource = ImportedFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
