<?php

namespace App\Filament\Resources\ReportBranchNotImportedDataResource\Pages;

use App\Filament\Resources\ReportBranchNotImportedDataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReportBranchNotImportedData extends EditRecord
{
    protected static string $resource = ReportBranchNotImportedDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
