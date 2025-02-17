<?php

namespace App\Filament\Resources\ImportedFeeResource\Pages;

use App\Filament\Resources\ImportedFeeResource;
use App\Imports\FeeImport;
use App\Services\ImportService;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Collection;

class ListImportedFees extends ListRecords
{
    protected static string $resource = ImportedFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            app(ImportService::class)->importAction(FeeImport::class),
            Actions\CreateAction::make(),
        ];
    }
}
