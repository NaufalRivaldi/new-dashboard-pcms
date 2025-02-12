<?php

namespace App\Filament\Resources\ImportedLeaveStudentResource\Pages;

use App\Filament\Resources\ImportedLeaveStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImportedLeaveStudent extends EditRecord
{
    protected static string $resource = ImportedLeaveStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
