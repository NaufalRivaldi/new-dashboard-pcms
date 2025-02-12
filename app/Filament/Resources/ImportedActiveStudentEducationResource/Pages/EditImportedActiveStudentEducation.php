<?php

namespace App\Filament\Resources\ImportedActiveStudentEducationResource\Pages;

use App\Filament\Resources\ImportedActiveStudentEducationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImportedActiveStudentEducation extends EditRecord
{
    protected static string $resource = ImportedActiveStudentEducationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
