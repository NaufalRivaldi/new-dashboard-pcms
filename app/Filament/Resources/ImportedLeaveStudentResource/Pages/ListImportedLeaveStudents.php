<?php

namespace App\Filament\Resources\ImportedLeaveStudentResource\Pages;

use App\Filament\Resources\ImportedLeaveStudentResource;
use App\Imports\LeaveStudentImport;
use App\Services\ImportService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportedLeaveStudents extends ListRecords
{
    protected static string $resource = ImportedLeaveStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            app(ImportService::class)->importAction(LeaveStudentImport::class),
            Actions\CreateAction::make(),
        ];
    }
}
