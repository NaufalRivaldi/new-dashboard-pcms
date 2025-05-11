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

        $date = Carbon::now()->subMonth()->locale($lang)->isoFormat('MMMM YYYY');
        $totalReceiveFee = app(SummaryService::class)->getTotalReceiveFee();
        $totalRoyalty = app(SummaryService::class)->getTotalRoyalty();
        $totalActiveStudent = app(SummaryService::class)->getTotalActiveStudent();
        $totalNewStudent = app(SummaryService::class)->getTotalNewStudent();
        $totalInactiveStudent = app(SummaryService::class)->getTotalInactiveStudent();
        $totalLeaveStudent = app(SummaryService::class)->getTotalLeaveStudent();

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
            Stat::make(__('Total New Student'), number_format($totalNewStudent))
                ->description($date)
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('success'),
            Stat::make(__('Total Inactive Student'), number_format($totalInactiveStudent))
                ->description($date)
                ->descriptionIcon('heroicon-m-arrow-trending-down', IconPosition::Before)
                ->color('danger'),
            Stat::make(__('Total Leave Student'), number_format($totalLeaveStudent))
                ->description($date)
                ->descriptionIcon('heroicon-m-arrow-trending-down', IconPosition::Before)
                ->color('secondary'),
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}
