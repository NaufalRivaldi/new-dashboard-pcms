<?php

namespace App\Filament\Resources\ImportedActiveStudentResource\Pages;

use App\Filament\Resources\ImportedActiveStudentResource;
use App\Imports\ActiveStudentLessonImport;
use App\Services\ImportService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportedActiveStudents extends ListRecords
{
    protected static string $resource = ImportedActiveStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            app(ImportService::class)->importAction(ActiveStudentLessonImport::class),
            Actions\CreateAction::make(),
        ];
    }
}
