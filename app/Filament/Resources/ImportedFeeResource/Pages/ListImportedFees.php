<?php

namespace App\Filament\Resources\ImportedFeeResource\Pages;

use App\Filament\Resources\ImportedFeeResource;
use App\Imports\FeeImport;
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
            ExcelImportAction::make()
                ->color("primary")
                ->processCollectionUsing(function (string $modelClass, Collection $collection) {
                    $collection
                        ->map(function ($data) {
                            return $data->values();
                        });

                    return $collection;
                })
                ->use(FeeImport::class),
            Actions\CreateAction::make(),
        ];
    }
}
