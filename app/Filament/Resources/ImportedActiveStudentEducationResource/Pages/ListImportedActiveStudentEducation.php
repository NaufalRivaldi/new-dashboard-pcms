<?php

namespace App\Filament\Resources\ImportedActiveStudentEducationResource\Pages;

use App\Filament\Resources\ImportedActiveStudentEducationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportedActiveStudentEducation extends ListRecords
{
    protected static string $resource = ImportedActiveStudentEducationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
