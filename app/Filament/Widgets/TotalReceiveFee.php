<?php

namespace App\Filament\Widgets;

use App\Services\SummaryService;
use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\App;

class TotalReceiveFee extends BaseWidget
{
    protected function getStats(): array
    {
        $lang = App::currentLocale();

        $date = Carbon::now()->locale($lang)->isoFormat('MMMM YYYY');
        $totalReceiveFee = app(SummaryService::class)->getTotalReceiveFee();
        $totalRoyalty = app(SummaryService::class)->getTotalRoyalty();
        $totalActiveStudent = app(SummaryService::class)->getTotalActiveStudent();

        return [
            Stat::make(__('Total Receive Fee'), 'Rp. '.number_format($totalReceiveFee))
                ->description($date)
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('success'),
            Stat::make(__('Total Royalty'), 'Rp. '.number_format($totalRoyalty))
                ->description($date)
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('success'),
            Stat::make(__('Total Active Student'), number_format($totalActiveStudent))
                ->description($date)
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('success'),
        ];
    }
}
