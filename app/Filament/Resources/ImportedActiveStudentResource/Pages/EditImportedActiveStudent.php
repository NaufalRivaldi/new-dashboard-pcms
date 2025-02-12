<?php

namespace App\Filament\Resources\ImportedActiveStudentResource\Pages;

use App\Filament\Resources\ImportedActiveStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImportedActiveStudent extends EditRecord
{
    protected static string $resource = ImportedActiveStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
