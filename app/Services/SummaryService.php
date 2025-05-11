<?php

namespace App\Services;

use App\Models\Summary;
use Carbon\Carbon;

class SummaryService
{
    public function getTotalReceiveFee()
    {
        $date = Carbon::now()->subMonth();
        $month = (int) $date->copy()->format('m');
        $year = (int) $date->copy()->format('Y');

        return Summary::where('month', $month)
            ->where('year', $year)
            ->get()
            ->sum('total_fee');
    }

    public function getTotalRoyalty()
    {
        $date = Carbon::now()->subMonth();
        $month = (int) $date->copy()->format('m');
        $year = (int) $date->copy()->format('Y');

        return Summary::where('month', $month)
            ->where('year', $year)
            ->get()
            ->sum('royalty');
    }

    public function getTotalActiveStudent()
    {
        $date = Carbon::now()->subMonth();
        $month = (int) $date->copy()->format('m');
        $year = (int) $date->copy()->format('Y');

        return Summary::where('month', $month)
            ->where('year', $year)
            ->get()
            ->sum('active_student');
    }

    public function getTotalNewStudent()
    {
        $date = Carbon::now()->subMonth();
        $month = (int) $date->copy()->format('m');
        $year = (int) $date->copy()->format('Y');

        return Summary::where('month', $month)
            ->where('year', $year)
            ->get()
            ->sum('new_student');
    }

    public function getTotalInactiveStudent()
    {
        $date = Carbon::now()->subMonth();
        $month = (int) $date->copy()->format('m');
        $year = (int) $date->copy()->format('Y');

        return Summary::where('month', $month)
            ->where('year', $year)
            ->get()
            ->sum('inactive_student');
    }

    public function getTotalLeaveStudent()
    {
        $date = Carbon::now()->subMonth();
        $month = (int) $date->copy()->format('m');
        $year = (int) $date->copy()->format('Y');

        return Summary::where('month', $month)
            ->where('year', $year)
            ->get()
            ->sum('leave_student');
    }
}