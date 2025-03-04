<?php

namespace App\Filament\Resources\ReportBranchNotImportedDataResource\Pages;

use App\Filament\Resources\ReportBranchNotImportedDataResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class ListReportBranchNotImportedData extends ListRecords
{
    protected static string $resource = ReportBranchNotImportedDataResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTitle(): string | Htmlable
    {
        return Str::title(__('List of branches that have not imported data'));
    }
}
