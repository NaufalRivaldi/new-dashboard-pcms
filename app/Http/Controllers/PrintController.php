<?php

namespace App\Http\Controllers;

use App\Services\BranchService;
use App\Services\PrintService;
use App\Traits\HasReportFilter;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    use HasReportFilter;

    public function analysis(PrintService $printService, Request $request)
    {
        $records = $printService->getFeeRecords();
        $studentLessonRecords = $printService->getActiveStudentLessonRecords();
        $studentEducationRecords = $printService->getActiveStudentEducationRecords();

        app()->setLocale($request->locale ?? config('app.locale'));

        return view('print.analysis', [
            'isMonthly' => $records['isMonthly'],
            'isBranchFiltered' => !empty($request->get('branch_id')),
            'isRegionFiltered' => !empty($request->get('region_id')),
            'branchName' => $this->getBranchName($request->get('branch_id')),
            'regionName' => $this->getRegionName($request->get('region_id')),
            'startPeriod' => $request->get('start_period') ?? $request->get('start_year'),
            'endPeriod' => $request->get('end_period') ?? $request->get('end_year'),
            'feeRecords' => $records['records'],
            'royaltyRecords' => $printService->getRoyaltyRecords()['records'],
            'studentRecords' => $printService->getStudentRecords()['records'],
            'lessons' => $studentLessonRecords['lessons'],
            'studentLessonRecords' => $studentLessonRecords['results'],
            'educations' => $studentEducationRecords['educations'],
            'studentEducationRecords' => $studentEducationRecords['results'],
        ]);
    }

    public function compare(PrintService $printService, Request $request)
    {
        $records = $printService->getCompareFeeRecords();
        $studentLessonRecords = $printService->getCompareActiveStudentLessonRecords();
        $studentEducationRecords = $printService->getCompareActiveStudentEducationRecords();

        app()->setLocale($request->locale ?? config('app.locale'));

        return view('print.compare', [
            'isMonthly' => $records['isMonthly'],
            'isBranchFiltered' => (
                !empty($request->get('first_branch_id'))
                || !empty($request->get('second_branch_id'))
            ),
            'isRegionFiltered' => (
                !empty($request->get('first_region_id'))
                || !empty($request->get('second_region_id'))
            ),
            'firstBranchName' => $this->getBranchName($request->get('first_branch_id')),
            'secondBranchName' => $this->getBranchName($request->get('second_branch_id')),
            'firstRegionName' => $this->getRegionName($request->get('first_region_id')),
            'secondRegionName' => $this->getRegionName($request->get('second_region_id')),
            'startPeriod' => $request->get('start_period') ?? $request->get('start_year'),
            'endPeriod' => $request->get('end_period') ?? $request->get('end_year'),
            'feeRecords' => $records['records'],
            'royaltyRecords' => $printService->getCompareRoyaltyRecords()['records'],
            'studentRecords' => $printService->getCompareStudentRecords()['records'],
            'lessons' => $studentLessonRecords['lessons'],
            'studentLessonRecords' => $studentLessonRecords['results'],
            'educations' => $studentEducationRecords['educations'],
            'studentEducationRecords' => $studentEducationRecords['results'],
        ]);
    }

    public function topOrUnderFive(PrintService $printService, Request $request)
    {
        $records = $printService->getTopFiveFeeRecords();
        $studentLessonRecords = $printService->getTopFiveActiveStudentLessonRecords();
        $studentEducationRecords = $printService->getTopFiveActiveStudentEducationRecords();

        app()->setLocale($request->locale ?? config('app.locale'));

        return view('print.top-or-under-five', [
            'isMonthly' => $records['isMonthly'],
            'period' => $request->get('period') ?? $request->get('year'),
            'type' => str($request->get('type'))->upper(),
            'feeRecords' => $records['records'],
            'royaltyRecords' => $printService->getTopFiveRoyaltyRecords()['records'],
            'studentRecords' => $printService->getTopFiveStudentRecords()['records'],
            'lessons' => $studentLessonRecords['lessons'],
            'studentLessonRecords' => $studentLessonRecords['results'],
            'educations' => $studentEducationRecords['educations'],
            'studentEducationRecords' => $studentEducationRecords['results'],
        ]);
    }

    public function unreportBranches(Request $request)
    {
        $period = $request->get('period');

        app()->setLocale($request->locale ?? config('app.locale'));

        return view('print.unreport-branches', [
            'records' => resolve(BranchService::class)->getBranchNotImportedByPeriod($period),
            'period' => $period,
        ]);
    }
}
