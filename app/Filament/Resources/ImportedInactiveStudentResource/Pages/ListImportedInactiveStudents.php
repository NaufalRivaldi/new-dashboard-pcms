<?php

namespace App\Filament\Resources\ImportedInactiveStudentResource\Pages;

use App\Filament\Resources\ImportedInactiveStudentResource;
use App\Imports\InactiveStudentImport;
use App\Services\ImportService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportedInactiveStudents extends ListRecords
{
    protected static string $resource = ImportedInactiveStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            app(ImportService::class)->importAction(InactiveStudentImport::class),
            Actions\CreateAction::make(),
        ];
    }
}
