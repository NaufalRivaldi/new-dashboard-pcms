<?php

namespace App\Filament\Resources\ImportedNewStudentResource\Pages;

use App\Filament\Resources\ImportedNewStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportedNewStudents extends ListRecords
{
    protected static string $resource = ImportedNewStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
