<?php

namespace App\Filament\Resources\ImportedActiveStudentEducationResource\Pages;

use App\Filament\Resources\ImportedActiveStudentEducationResource;
use App\Imports\ActiveStudentEducationImport;
use App\Services\ImportService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportedActiveStudentEducation extends ListRecords
{
    protected static string $resource = ImportedActiveStudentEducationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            app(ImportService::class)->importAction(ActiveStudentEducationImport::class),
            Actions\CreateAction::make(),
        ];
    }
}
