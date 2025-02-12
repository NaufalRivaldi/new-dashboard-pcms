<?php

namespace App\Filament\Resources\ImportedLeaveStudentResource\Pages;

use App\Filament\Resources\ImportedLeaveStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportedLeaveStudents extends ListRecords
{
    protected static string $resource = ImportedLeaveStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
