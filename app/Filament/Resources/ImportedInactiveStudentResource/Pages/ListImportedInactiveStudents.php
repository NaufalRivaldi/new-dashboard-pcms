<?php

namespace App\Filament\Resources\ImportedInactiveStudentResource\Pages;

use App\Filament\Resources\ImportedInactiveStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportedInactiveStudents extends ListRecords
{
    protected static string $resource = ImportedInactiveStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
