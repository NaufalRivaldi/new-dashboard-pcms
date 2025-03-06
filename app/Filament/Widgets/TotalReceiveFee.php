<?php

namespace App\Filament\Widgets;

use App\Services\SummaryService;
use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalReceiveFee extends BaseWidget
{
    protected function getStats(): array
    {
        $date = Carbon::now()->format('F Y');
        $totalReceiveFee = app(SummaryService::class)->getTotalReceiveFee();
        $totalRoyalty = app(SummaryService::class)->getTotalRoyalty();
        $totalActiveStudent = app(SummaryService::class)->getTotalActiveStudent();

        return [
            Stat::make('Total Receive Fee', 'Rp. '.number_format($totalReceiveFee))
                ->description($date)
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('success'),
            Stat::make('Total Royalty', 'Rp. '.number_format($totalRoyalty))
                ->description($date)
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('success'),
            Stat::make('Total Active Student', number_format($totalActiveStudent))
                ->description($date)
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('success'),
        ];
    }
}
