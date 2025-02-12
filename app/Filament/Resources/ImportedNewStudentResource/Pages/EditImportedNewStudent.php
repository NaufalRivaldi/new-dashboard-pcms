<?php

namespace App\Filament\Resources\ImportedNewStudentResource\Pages;

use App\Filament\Resources\ImportedNewStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImportedNewStudent extends EditRecord
{
    protected static string $resource = ImportedNewStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
