<?php

namespace App\Filament\Resources\ReportBranchNotImportedDataResource\Pages;

use App\Filament\Resources\ReportBranchNotImportedDataResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class ListReportBranchNotImportedData extends ListRecords
{
    protected static string $resource = ReportBranchNotImportedDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Print')
                ->icon('heroicon-o-printer')
                ->url(function (): string {
                        $previousUrl = url()->previous();
                        $queryParams = parse_url($previousUrl, PHP_URL_QUERY);

                        parse_str($queryParams, $params);

                        $period = data_get($params, 'tableFilters.period.period');

                        return route('print.unreport-branches', [
                            'period' => $period,
                            'locale' => app()->currentLocale(),
                        ]);
                }, shouldOpenInNewTab: true),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return Str::title(__('List of branches that have not imported data'));
    }
}
