<?php

namespace App\Filament\Resources\ImportedNewStudentResource\Pages;

use App\Filament\Resources\ImportedNewStudentResource;
use App\Imports\NewStudentImport;
use App\Services\ImportService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportedNewStudents extends ListRecords
{
    protected static string $resource = ImportedNewStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            app(ImportService::class)->importAction(NewStudentImport::class),
            Actions\CreateAction::make(),
        ];
    }
}
