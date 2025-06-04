<?php

namespace App\Services;

use App\Models\Education;
use App\Models\Lesson;
use App\Models\Summary;
use App\Models\SummaryActiveStudentEducation;
use App\Models\SummaryActiveStudentLesson;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AnalysisService
{
    protected function getFilters(): array
    {
        $previousUrl = url()->previous();
        $queryParams = parse_url($previousUrl, PHP_URL_QUERY);
        parse_str($queryParams, $params);

        $isMonthly = (
                isset($params['filters']['start_period'])
                || isset($params['filters']['end_period'])
            )
            || (
                !isset($params['filters']['start_year'])
                || !isset($params['filters']['end_year'])
            );

        $endPeriod = $startPeriod = null;

        if ($isMonthly) {
            $endPeriod = (
                    isset($params['filters']['end_period'])
                    && !empty($params['filters']['end_period'])
                )
                ? $params['filters']['end_period']
                : Carbon::now()->format('Y-m');
            $startPeriod = (
                    isset($params['filters']['start_period'])
                    && !empty($params['filters']['start_period'])
                )
                ? $params['filters']['start_period']
                : Carbon::parse($endPeriod)->subMonth(11)->format('Y-m');
        } else {
            $endPeriod = (
                    isset($params['filters']['end_year'])
                    && !empty($params['filters']['end_year'])
                )
                ? $params['filters']['end_year']
                : Carbon::now()->format('Y');
            $startPeriod = (
                    isset($params['filters']['start_year'])
                    && !empty($params['filters']['start_year'])
                )
                ? $params['filters']['start_year']
                : Carbon::parse($endPeriod)->subYear()->format('Y');
        }

        $periodValues = $this->getPeriodValues(
            $startPeriod,
            $endPeriod,
            $isMonthly,
        );

        $branchId = $params['filters']['branch_id'] ?? null;
        $regionId = $params['filters']['region_id'] ?? null;

        return [
            'periodValues' => $periodValues,
            'branchId' => $branchId,
            'regionId' => $regionId,
            'isMonthly' => $isMonthly,
        ];
    }

    private function getSummaryBuilder(): Builder
    {
        $filters = $this->getFilters();

        $queryBuilder = Summary::when($filters['branchId'], function (Builder $query, $branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($filters['regionId'], function (Builder $query, $regionId) {
                $query->whereHas('branch', function (Builder $query) use ($regionId) {
                    $query->where('region_id', $regionId);
                });
            });

        if ($filters['isMonthly']) {
            $queryBuilder
                ->where(function (Builder $query) use ($filters) {
                    foreach ($filters['periodValues'] as $key => $period) {
                        if ($key == 0) {
                            $query->where(function (Builder $query) use ($period) {
                                $query
                                    ->where('year', $period['year'])
                                    ->whereIn('month', $period['months']);
                            });
                        } else {
                            $query->orWhere(function (Builder $query) use ($period) {
                                $query
                                    ->where('year', $period['year'])
                                    ->whereIn('month', $period['months']);
                            });
                        }
                    }
                })
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc');
        } else {
            $queryBuilder
                ->whereIn('year', $filters['periodValues'])
                ->groupBy('year')
                ->orderBy('year', 'asc');
        }

        return $queryBuilder;
    }

    public function getFeeRecords(): array
    {
        $filters = $this->getFilters();
        $isMonthly = $filters['isMonthly'];

        $selectedColumn = $isMonthly
            ? 'year, month, SUM(registration_fee) as total_registration_fee, SUM(course_fee) as total_course_fee, SUM(total_fee) as total_total_fee'
            : 'year, SUM(registration_fee) as total_registration_fee, SUM(course_fee) as total_course_fee, SUM(total_fee) as total_total_fee';

        $summaries = $this->getSummaryBuilder()
            ->selectRaw($selectedColumn)
            ->get();

        $results = collect();

        if ($filters['isMonthly']) {
            foreach ($filters['periodValues'] as $period) {
                foreach ($period['months'] as $month) {
                    $findSummary = $summaries
                        ->where('year', $period['year'])
                        ->where('month', $month)
                        ->first();

                    $findSummary = $findSummary ? $findSummary->toArray() : [];

                    $results->push([
                        ...[
                            'year' => $period['year'],
                            'month' => $month,
                            'total_registration_fee' => 0,
                            'total_course_fee' => 0,
                            'total_total_fee' => 0,
                        ],
                        ...$findSummary
                    ]);
                }
            }
        } else {
            foreach ($filters['periodValues'] as $year) {
                $findSummary = $summaries
                    ->where('year', $year)
                    ->first();

                $findSummary = $findSummary ? $findSummary->toArray() : [];

                $results->push([
                    ...[
                        'year' => $year,
                        'total_registration_fee' => 0,
                        'total_course_fee' => 0,
                        'total_total_fee' => 0,
                    ],
                    ...$findSummary
                ]);
            }
        }

        return [
            'records' => $results,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getRoyaltyRecords(): array
    {
        $filters = $this->getFilters();
        $isMonthly = $filters['isMonthly'];

        $selectedColumn = $isMonthly
            ? 'year, month, SUM(royalty) as total_royalty'
            : 'year, SUM(royalty) as total_royalty';

        $summaries = $this->getSummaryBuilder()
            ->selectRaw($selectedColumn)
            ->get();

        $results = collect();

        if ($filters['isMonthly']) {
            foreach ($filters['periodValues'] as $period) {
                foreach ($period['months'] as $month) {
                    $findSummary = $summaries
                        ->where('year', $period['year'])
                        ->where('month', $month)
                        ->first();

                    $findSummary = $findSummary ? $findSummary->toArray() : [];

                    $results->push([
                        ...[
                            'year' => $period['year'],
                            'month' => $month,
                            'total_royalty' => 0,
                        ],
                        ...$findSummary
                    ]);
                }
            }
        } else {
            foreach ($filters['periodValues'] as $year) {
                $findSummary = $summaries
                    ->where('year', $year)
                    ->first();

                $findSummary = $findSummary ? $findSummary->toArray() : [];

                $results->push([
                    ...[
                        'year' => $year,
                        'total_royalty' => 0,
                    ],
                    ...$findSummary
                ]);
            }
        }

        return [
            'records' => $results,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getStudentRecords(): array
    {
        $filters = $this->getFilters();
        $isMonthly = $filters['isMonthly'];

        $selectedColumn = $isMonthly
            ? 'year, month, SUM(active_student) as total_active_student, SUM(new_student) as total_new_student, SUM(inactive_student) as total_inactive_student, SUM(leave_student) as total_leave_student'
            : 'year, SUM(active_student) as total_active_student, SUM(new_student) as total_new_student, SUM(inactive_student) as total_inactive_student, SUM(leave_student) as total_leave_student';

        $summaries = $this->getSummaryBuilder()
            ->selectRaw($selectedColumn)
            ->get();

        $results = collect();

        if ($filters['isMonthly']) {
            foreach ($filters['periodValues'] as $period) {
                foreach ($period['months'] as $month) {
                    $findSummary = $summaries
                        ->where('year', $period['year'])
                        ->where('month', $month)
                        ->first();

                    $findSummary = $findSummary ? $findSummary->toArray() : [];

                    $results->push([
                        ...[
                            'year' => $period['year'],
                            'month' => $month,
                            'total_active_student' => 0,
                            'total_new_student' => 0,
                            'total_inactive_student' => 0,
                            'total_leave_student' => 0,
                        ],
                        ...$findSummary
                    ]);
                }
            }
        } else {
            foreach ($filters['periodValues'] as $year) {
                $findSummary = $summaries
                    ->where('year', $year)
                    ->first();

                $findSummary = $findSummary ? $findSummary->toArray() : [];

                $results->push([
                    ...[
                        'year' => $year,
                        'total_active_student' => 0,
                        'total_new_student' => 0,
                        'total_inactive_student' => 0,
                        'total_leave_student' => 0,
                    ],
                    ...$findSummary
                ]);
            }
        }

        return [
            'records' => $results,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getActiveStudentLessonRecords(): array
    {
        $filters = $this->getFilters();
        $isMonthly = $filters['isMonthly'];

        $selectedColumn = $isMonthly
            ? [
                'lesson_id',
                'summaries.year',
                'summaries.month',
                DB::raw('SUM(summary_active_student_lessons.total) as total')
            ]
            : [
                'lesson_id',
                'summaries.year',
                DB::raw('SUM(summary_active_student_lessons.total) as total')
            ];

        $lessons = Lesson::select(['id', 'name'])->orderBy('name', 'asc')->get();

        $periodPairs = $isMonthly
            ? collect($filters['periodValues'])->flatMap(function ($period) use ($isMonthly) {
                if ($isMonthly) {
                    return collect($period['months'])->map(function ($month) use ($period) {
                        return ['year' => $period['year'], 'month' => $month];
                    });
                }

                return $period;
            })
            : $filters['periodValues'];

        $summaryQuery = SummaryActiveStudentLesson::select($selectedColumn)
            ->join('summaries', 'summary_active_student_lessons.summary_id', '=', 'summaries.id')
            ->join('branches', 'summaries.branch_id', '=', 'branches.id')
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->where(function ($query) use ($periodPairs, $isMonthly) {
                foreach ($periodPairs as $pair) {
                    $query->orWhere(function ($q) use ($pair, $isMonthly) {
                        if ($isMonthly) {
                            $q
                                ->where('summaries.year', $pair['year'])
                                ->where('summaries.month', $pair['month']);
                        } else {
                            $q
                                ->where('summaries.year', $pair);
                        }
                    });
                }
            });

        if ($filters['branchId']) {
            $summaryQuery->where('summaries.branch_id', $filters['branchId']);
        }
        if ($filters['regionId']) {
            $summaryQuery->where('branches.region_id', $filters['regionId']);
        }

        if ($isMonthly) {
            $summaryData = $summaryQuery
                ->groupBy('lesson_id', 'summaries.year', 'summaries.month')
                ->get()
                ->groupBy(fn ($item) => "{$item->year}-{$item->month}")
                ->map(fn ($group) => $group->keyBy('lesson_id'));
        } else {
            $summaryData = $summaryQuery
                ->groupBy('lesson_id', 'summaries.year')
                ->get()
                ->groupBy(fn ($item) => "{$item->year}")
                ->map(fn ($group) => $group->keyBy('lesson_id'));
        }

        $results = collect();

        foreach ($filters['periodValues'] as $period) {
            if ($isMonthly) {
                foreach ($period['months'] as $month) {
                    $key = "{$period['year']}-{$month}";
                    $details = [];

                    foreach ($lessons as $lesson) {
                        $total = $summaryData[$key][$lesson->id]->total ?? 0;

                        $details[] = [
                            'lesson_id' => $lesson->id,
                            'lesson_name' => $lesson->name,
                            'total' => $total,
                        ];
                    }

                    $results->push([
                        'year' => $period['year'],
                        'month' => $month,
                        'details' => $details,
                    ]);
                }
            } else {
                $key = "{$period}";
                $details = [];

                foreach ($lessons as $lesson) {
                    $total = $summaryData[$key][$lesson->id]->total ?? 0;

                    $details[] = [
                        'lesson_id' => $lesson->id,
                        'lesson_name' => $lesson->name,
                        'total' => $total,
                    ];
                }

                $results->push([
                    'year' => $period,
                    'details' => $details,
                ]);
            }
        }

        return [
            'results' => $results,
            'lessons' => $lessons,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getActiveStudentEducationRecords(): array
    {
        $filters = $this->getFilters();
        $isMonthly = $filters['isMonthly'];

        $selectedColumn = $isMonthly
            ? [
                'education_id',
                'summaries.year',
                'summaries.month',
                DB::raw('SUM(summary_active_student_education.total) as total')
            ]
            : [
                'education_id',
                'summaries.year',
                DB::raw('SUM(summary_active_student_education.total) as total')
            ];

        $educations = Education::select(['id', 'name', 'color'])->orderBy('name', 'asc')->get();

        $periodPairs = $isMonthly
            ? collect($filters['periodValues'])->flatMap(function ($period) use ($isMonthly) {
                if ($isMonthly) {
                    return collect($period['months'])->map(function ($month) use ($period) {
                        return ['year' => $period['year'], 'month' => $month];
                    });
                }

                return $period;
            })
            : $filters['periodValues'];

        $summaryQuery = SummaryActiveStudentEducation::select($selectedColumn)
            ->join('summaries', 'summary_active_student_education.summary_id', '=', 'summaries.id')
            ->join('branches', 'summaries.branch_id', '=', 'branches.id')
            ->whereIn('education_id', $educations->pluck('id'))
            ->where(function ($query) use ($periodPairs, $isMonthly) {
                foreach ($periodPairs as $pair) {
                    $query->orWhere(function ($q) use ($pair, $isMonthly) {
                        if ($isMonthly) {
                            $q
                                ->where('summaries.year', $pair['year'])
                                ->where('summaries.month', $pair['month']);
                        } else {
                            $q
                                ->where('summaries.year', $pair);
                        }
                    });
                }
            });

        if ($filters['branchId']) {
            $summaryQuery->where('summaries.branch_id', $filters['branchId']);
        }
        if ($filters['regionId']) {
            $summaryQuery->where('branches.region_id', $filters['regionId']);
        }

        if ($isMonthly) {
            $summaryData = $summaryQuery
                ->groupBy('education_id', 'summaries.year', 'summaries.month')
                ->get()
                ->groupBy(fn ($item) => "{$item->year}-{$item->month}")
                ->map(fn ($group) => $group->keyBy('education_id'));
        } else {
            $summaryData = $summaryQuery
                ->groupBy('education_id', 'summaries.year')
                ->get()
                ->groupBy(fn ($item) => "{$item->year}")
                ->map(fn ($group) => $group->keyBy('education_id'));
        }

        $results = collect();

        foreach ($filters['periodValues'] as $period) {
            if ($isMonthly) {
                foreach ($period['months'] as $month) {
                    $key = "{$period['year']}-{$month}";
                    $details = [];

                    foreach ($educations as $education) {
                        $total = $summaryData[$key][$education->id]->total ?? 0;

                        $details[] = [
                            'education_id' => $education->id,
                            'education_name' => $education->name,
                            'total' => $total,
                        ];
                    }

                    $results->push([
                        'year' => $period['year'],
                        'month' => $month,
                        'details' => $details,
                    ]);
                }
            } else {
                $key = "{$period}";
                $details = [];

                foreach ($educations as $education) {
                    $total = $summaryData[$key][$education->id]->total ?? 0;

                    $details[] = [
                        'education_id' => $education->id,
                        'education_name' => $education->name,
                        'total' => $total,
                    ];
                }

                $results->push([
                    'year' => $period,
                    'details' => $details,
                ]);
            }
        }

        return [
            'results' => $results,
            'educations' => $educations,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getCompareFilters(): array
    {
        $previousUrl = url()->previous();
        $queryParams = parse_url($previousUrl, PHP_URL_QUERY);
        parse_str($queryParams, $params);

        $isMonthly = (
                isset($params['filters']['start_period'])
                || isset($params['filters']['end_period'])
            )
            || (
                !isset($params['filters']['start_year'])
                || !isset($params['filters']['end_year'])
            );

        $endPeriod = $startPeriod = null;

        if ($isMonthly) {
            $endPeriod = (
                    isset($params['filters']['end_period'])
                    && !empty($params['filters']['end_period'])
                )
                ? $params['filters']['end_period']
                : Carbon::now()->format('Y-m');
            $startPeriod = (
                    isset($params['filters']['start_period'])
                    && !empty($params['filters']['start_period'])
                )
                ? $params['filters']['start_period']
                : Carbon::parse($endPeriod)->subMonth(11)->format('Y-m');
        } else {
            $endPeriod = (
                    isset($params['filters']['end_year'])
                    && !empty($params['filters']['end_year'])
                )
                ? $params['filters']['end_year']
                : Carbon::now()->format('Y');
            $startPeriod = (
                    isset($params['filters']['start_year'])
                    && !empty($params['filters']['start_year'])
                )
                ? $params['filters']['start_year']
                : Carbon::parse($endPeriod)->subYear()->format('Y');
        }

        $periodValues = $this->getPeriodValues(
            $startPeriod,
            $endPeriod,
            $isMonthly,
        );

        $firstBranchId = Arr::get($params, 'filters.first_branch_id');
        $secondBranchId = Arr::get($params, 'filters.second_branch_id');

        $firstRegionId = Arr::get($params, 'filters.first_region_id');
        $secondRegionId = Arr::get($params, 'filters.second_region_id');

        return [
            'periodValues' => $periodValues,
            'firstBranchId' => $firstBranchId,
            'secondBranchId' => $secondBranchId,
            'firstRegionId' => $firstRegionId,
            'secondRegionId' => $secondRegionId,
            'isMonthly' => $isMonthly,
        ];
    }

    private function getCompareSummaryBuilder(): Builder
    {
        $filters = $this->getCompareFilters();

        $isComparedBranchExists = !is_null($filters['firstBranchId']) && !is_null($filters['secondBranchId']);
        $isComparedRegionExists = !is_null($filters['firstRegionId']) && !is_null($filters['secondRegionId']);

        $queryBuilder = Summary::query()
            ->when($isComparedBranchExists, function (Builder $query) use ($filters) {
                $query->where(function (Builder $query) use ($filters) {
                    $query
                        ->where('branch_id', $filters['firstBranchId'])
                        ->orWhere('branch_id', $filters['secondBranchId']);
                });
            })
            ->when($isComparedRegionExists, function (Builder $query) use ($filters) {
                $query->where(function (Builder $query) use ($filters) {
                    $query
                        ->whereHas('branch', function (Builder $query) use ($filters) {
                            $query
                                ->where('region_id', $filters['firstRegionId'])
                                ->orWhere('region_id', $filters['secondRegionId']);
                        });
                });
            })
            ->join('branches', 'branches.id', '=', 'summaries.branch_id');

        $comparedId = $isComparedBranchExists
            ? 'branch_id'
            : 'region_id';

        if ($filters['isMonthly']) {
            $queryBuilder
                ->where(function (Builder $query) use ($filters) {
                    foreach ($filters['periodValues'] as $key => $period) {
                        if ($key == 0) {
                            $query->where(function (Builder $query) use ($period) {
                                $query
                                    ->where('year', $period['year'])
                                    ->whereIn('month', $period['months']);
                            });
                        } else {
                            $query->orWhere(function (Builder $query) use ($period) {
                                $query
                                    ->where('year', $period['year'])
                                    ->whereIn('month', $period['months']);
                            });
                        }
                    }
                })
                ->groupBy('year', 'month', $comparedId)
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc');
        } else {
            $queryBuilder
                ->whereIn('year', $filters['periodValues'])
                ->groupBy('year', $comparedId)
                ->orderBy('year', 'asc');
        }

        return $queryBuilder;
    }

    public function getCompareFeeRecords(): array
    {
        $filters = $this->getCompareFilters();
        $isComparedBranchExists = !is_null($filters['firstBranchId']) && !is_null($filters['secondBranchId']);
        $isMonthly = $filters['isMonthly'];

        $comparedIdName = $isComparedBranchExists
            ? 'branch_id'
            : 'region_id';

        $selectedColumn = $isMonthly
            ? "year, month, SUM(registration_fee) as total_registration_fee, SUM(course_fee) as total_course_fee, SUM(total_fee) as total_total_fee, $comparedIdName"
            : "year, SUM(registration_fee) as total_registration_fee, SUM(course_fee) as total_course_fee, SUM(total_fee) as total_total_fee, $comparedIdName";

        $summaries = $this->getCompareSummaryBuilder()
            ->selectRaw($selectedColumn)
            ->get();

        $comparedIds = $isComparedBranchExists
            ? [
                $filters['firstBranchId'],
                $filters['secondBranchId'],
            ]
            : [
                $filters['firstRegionId'],
                $filters['secondRegionId'],
            ];

        $results = collect();

        if ($filters['isMonthly']) {
            foreach ($filters['periodValues'] as $period) {
                foreach ($period['months'] as $month) {
                    $tmpResult = [];

                    foreach ($comparedIds as $comparedId) {
                        $findSummary = $summaries
                            ->where('year', $period['year'])
                            ->where('month', $month)
                            ->where($comparedIdName, $comparedId)
                            ->first();

                        $findSummary = $findSummary ? $findSummary->toArray() : [];

                        $tmpResult[] = [
                            ...[
                                'year' => $period['year'],
                                'month' => $month,
                                'total_registration_fee' => 0,
                                'total_course_fee' => 0,
                                'total_total_fee' => 0,
                                $comparedIdName => (int) $comparedId,
                            ],
                            ...$findSummary
                        ];
                    }

                    $results->push($tmpResult);
                }
            }
        } else {
            foreach ($filters['periodValues'] as $year) {
                $tmpResult = [];

                foreach ($comparedIds as $comparedId) {
                    $findSummary = $summaries
                        ->where('year', $year)
                        ->where($comparedIdName, $comparedId)
                        ->first();

                    $findSummary = $findSummary ? $findSummary->toArray() : [];

                    $tmpResult[] = [
                        ...[
                            'year' => $year,
                            'total_registration_fee' => 0,
                            'total_course_fee' => 0,
                            'total_total_fee' => 0,
                            $comparedIdName => (int) $comparedId,
                        ],
                        ...$findSummary
                    ];
                }

                $results->push($tmpResult);
            }
        }

        return [
            'records' => $results,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getCompareRoyaltyRecords(): array
    {
        $filters = $this->getCompareFilters();
        $isComparedBranchExists = !is_null($filters['firstBranchId']) && !is_null($filters['secondBranchId']);
        $isMonthly = $filters['isMonthly'];

        $comparedIdName = $isComparedBranchExists
            ? 'branch_id'
            : 'region_id';

        $selectedColumn = $isMonthly
            ? "year, month, SUM(royalty) as total_royalty, $comparedIdName"
            : "year, SUM(royalty) as total_royalty, $comparedIdName";

        $summaries = $this->getCompareSummaryBuilder()
            ->selectRaw($selectedColumn)
            ->get();

        $comparedIds = $isComparedBranchExists
            ? [
                $filters['firstBranchId'],
                $filters['secondBranchId'],
            ]
            : [
                $filters['firstRegionId'],
                $filters['secondRegionId'],
            ];

        $results = collect();

        if ($filters['isMonthly']) {
            foreach ($filters['periodValues'] as $period) {
                foreach ($period['months'] as $month) {
                    $tmpResult = [];

                    foreach ($comparedIds as $comparedId) {
                        $findSummary = $summaries
                            ->where('year', $period['year'])
                            ->where('month', $month)
                            ->where($comparedIdName, $comparedId)
                            ->first();

                        $findSummary = $findSummary ? $findSummary->toArray() : [];

                        $tmpResult[] = [
                            ...[
                                'year' => $period['year'],
                                'month' => $month,
                                'total_royalty' => 0,
                                $comparedIdName => (int) $comparedId,
                            ],
                            ...$findSummary
                        ];
                    }

                    $results->push($tmpResult);
                }
            }
        } else {
            foreach ($filters['periodValues'] as $year) {
                $tmpResult = [];

                foreach ($comparedIds as $comparedId) {
                    $findSummary = $summaries
                        ->where('year', $year)
                        ->where($comparedIdName, $comparedId)
                        ->first();

                    $findSummary = $findSummary ? $findSummary->toArray() : [];

                    $tmpResult[] = [
                        ...[
                            'year' => $year,
                            'total_royalty' => 0,
                            $comparedIdName => (int) $comparedId,
                        ],
                        ...$findSummary
                    ];
                }

                $results->push($tmpResult);
            }
        }

        return [
            'records' => $results,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getCompareStudentRecords(): array
    {
        $filters = $this->getCompareFilters();
        $isComparedBranchExists = !is_null($filters['firstBranchId']) && !is_null($filters['secondBranchId']);
        $isMonthly = $filters['isMonthly'];

        $comparedIdName = $isComparedBranchExists
            ? 'branch_id'
            : 'region_id';

        $selectedColumn = $isMonthly
            ? "year, month, SUM(active_student) as total_active_student, SUM(new_student) as total_new_student, SUM(inactive_student) as total_inactive_student, SUM(leave_student) as total_leave_student, $comparedIdName"
            : "year, SUM(active_student) as total_active_student, SUM(new_student) as total_new_student, SUM(inactive_student) as total_inactive_student, SUM(leave_student) as total_leave_student, $comparedIdName";

        $summaries = $this->getCompareSummaryBuilder()
            ->selectRaw($selectedColumn)
            ->get();

        $comparedIds = $isComparedBranchExists
            ? [
                $filters['firstBranchId'],
                $filters['secondBranchId'],
            ]
            : [
                $filters['firstRegionId'],
                $filters['secondRegionId'],
            ];

        $results = collect();

        if ($filters['isMonthly']) {
            foreach ($filters['periodValues'] as $period) {
                foreach ($period['months'] as $month) {
                    $tmpResult = [];

                    foreach ($comparedIds as $comparedId) {
                        $findSummary = $summaries
                            ->where('year', $period['year'])
                            ->where('month', $month)
                            ->where($comparedIdName, $comparedId)
                            ->first();

                        $findSummary = $findSummary ? $findSummary->toArray() : [];

                        $tmpResult[] = [
                            ...[
                                'year' => $period['year'],
                                'month' => $month,
                                'total_active_student' => 0,
                                'total_new_student' => 0,
                                'total_inactive_student' => 0,
                                'total_leave_student' => 0,
                                $comparedIdName => (int) $comparedId,
                            ],
                            ...$findSummary
                        ];
                    }

                    $results->push($tmpResult);
                }
            }
        } else {
            foreach ($filters['periodValues'] as $year) {
                $tmpResult = [];

                foreach ($comparedIds as $comparedId) {
                    $findSummary = $summaries
                        ->where('year', $year)
                        ->where($comparedIdName, $comparedId)
                        ->first();

                    $findSummary = $findSummary ? $findSummary->toArray() : [];

                    $tmpResult[] = [
                        ...[
                            'year' => $year,
                            'total_active_student' => 0,
                            'total_new_student' => 0,
                            'total_inactive_student' => 0,
                            'total_leave_student' => 0,
                            $comparedIdName => (int) $comparedId,
                        ],
                        ...$findSummary
                    ];
                }

                $results->push($tmpResult);
            }
        }

        return [
            'records' => $results,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getCompareActiveStudentLessonRecords(): array
    {
        $filters = $this->getCompareFilters();
        $isComparedBranchExists = !is_null($filters['firstBranchId']) && !is_null($filters['secondBranchId']);
        $isMonthly = $filters['isMonthly'];

        $comparedIdName = $isComparedBranchExists
            ? 'summaries.branch_id'
            : 'branches.region_id';

        $selectedColumn = $isMonthly
            ? [
                'lesson_id',
                $comparedIdName,
                'summaries.year',
                'summaries.month',
                DB::raw('SUM(summary_active_student_lessons.total) as total'),
            ]
            : [
                'lesson_id',
                $comparedIdName,
                'summaries.year',
                DB::raw('SUM(summary_active_student_lessons.total) as total')
            ];

        $lessons = Lesson::select(['id', 'name'])->orderBy('name', 'asc')->get();

        $comparedIds = $isComparedBranchExists
            ? [
                $filters['firstBranchId'],
                $filters['secondBranchId'],
            ]
            : [
                $filters['firstRegionId'],
                $filters['secondRegionId'],
            ];

        $periodPairs = $isMonthly
            ? collect($filters['periodValues'])->flatMap(function ($period) use ($isMonthly) {
                if ($isMonthly) {
                    return collect($period['months'])->map(function ($month) use ($period) {
                        return ['year' => $period['year'], 'month' => $month];
                    });
                }

                return $period;
            })
            : $filters['periodValues'];

        $lessonIds = $lessons->pluck('id')->all();

        $summaryQuery = SummaryActiveStudentLesson::query()
            ->select($selectedColumn)
            ->join('summaries', 'summary_active_student_lessons.summary_id', '=', 'summaries.id')
            ->join('branches', 'branches.id', '=', 'summaries.branch_id')
            ->whereIn('lesson_id', $lessonIds)
            ->whereIn($comparedIdName, $comparedIds)
            ->where(function ($query) use ($periodPairs, $isMonthly) {
                foreach ($periodPairs as $pair) {
                    $query->orWhere(function ($q) use ($pair, $isMonthly) {
                        if ($isMonthly) {
                            $q
                                ->where('summaries.year', $pair['year'])
                                ->where('summaries.month', $pair['month']);
                        } else {
                            $q
                                ->where('summaries.year', $pair);
                        }
                    });
                }
            });

        $comparedIdColumnOnly = explode('.', $comparedIdName)[1];
        if ($isMonthly) {
            $groupedData = $summaryQuery
                ->groupBy('lesson_id', $comparedIdName, 'summaries.year', 'summaries.month')
                ->get()
                ->groupBy(fn ($item) => "{$item->year}-{$item->month}-{$item->$comparedIdColumnOnly}")
                ->map(fn ($group) => $group->keyBy('lesson_id'));
        } else {
            $groupedData = $summaryQuery
                ->groupBy('lesson_id', $comparedIdName, 'summaries.year')
                ->get()
                ->groupBy(fn ($item) => "{$item->year}-{$item->$comparedIdColumnOnly}")
                ->map(fn ($group) => $group->keyBy('lesson_id'));
        }

        $results = collect();

        foreach ($filters['periodValues'] as $period) {
            if ($isMonthly) {
                foreach ($period['months'] as $month) {
                    $tmpResult = [];

                    foreach ($comparedIds as $comparedId) {
                        $details = [];

                        foreach ($lessons as $lesson) {
                            $key = "{$period['year']}-{$month}-{$comparedId}";
                            $total = $groupedData[$key][$lesson->id]->total ?? 0;

                            $details[] = [
                                'lesson_id' => $lesson->id,
                                'lesson_name' => $lesson->name,
                                'total' => $total,
                            ];
                        }

                        $tmpResult[] = [
                            'year' => $period['year'],
                            'month' => $month,
                            $comparedIdColumnOnly => $comparedId,
                            'details' => $details,
                        ];
                    }

                    $results->push($tmpResult);
                }
            } else {
                $tmpResult = [];

                foreach ($comparedIds as $comparedId) {
                    $details = [];

                    foreach ($lessons as $lesson) {
                        $key = "{$period}-{$comparedId}";
                        $total = $groupedData[$key][$lesson->id]->total ?? 0;

                        $details[] = [
                            'lesson_id' => $lesson->id,
                            'lesson_name' => $lesson->name,
                            'total' => $total,
                        ];
                    }

                    $tmpResult[] = [
                        'year' => $period,
                        $comparedIdColumnOnly => $comparedId,
                        'details' => $details,
                    ];
                }

                $results->push($tmpResult);
            }
        }

        return [
            'results' => $results,
            'lessons' => $lessons,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getCompareActiveStudentEducationRecords(): array
    {
        $filters = $this->getCompareFilters();
        $isComparedBranchExists = !is_null($filters['firstBranchId']) && !is_null($filters['secondBranchId']);
        $isMonthly = $filters['isMonthly'];

        $comparedIdName = $isComparedBranchExists
            ? 'summaries.branch_id'
            : 'branches.region_id';

        $selectedColumn = $isMonthly
            ? [
                'education_id',
                $comparedIdName,
                'summaries.year',
                'summaries.month',
                DB::raw('SUM(summary_active_student_education.total) as total'),
            ]
            : [
                'education_id',
                $comparedIdName,
                'summaries.year',
                DB::raw('SUM(summary_active_student_education.total) as total'),
            ];

        $educations = Education::select(['id', 'name'])->orderBy('name', 'asc')->get();

        $comparedIds = $isComparedBranchExists
            ? [
                $filters['firstBranchId'],
                $filters['secondBranchId'],
            ]
            : [
                $filters['firstRegionId'],
                $filters['secondRegionId'],
            ];

        $periodPairs = $isMonthly
            ? collect($filters['periodValues'])->flatMap(function ($period) use ($isMonthly) {
                if ($isMonthly) {
                    return collect($period['months'])->map(function ($month) use ($period) {
                        return ['year' => $period['year'], 'month' => $month];
                    });
                }

                return $period;
            })
            : $filters['periodValues'];

        $educationIds = $educations->pluck('id')->all();

        $summaryQuery = SummaryActiveStudentEducation::query()
            ->select($selectedColumn)
            ->join('summaries', 'summary_active_student_education.summary_id', '=', 'summaries.id')
            ->join('branches', 'branches.id', '=', 'summaries.branch_id')
            ->whereIn('education_id', $educationIds)
            ->whereIn($comparedIdName, $comparedIds)
            ->where(function ($query) use ($periodPairs, $isMonthly) {
                foreach ($periodPairs as $pair) {
                    $query->orWhere(function ($q) use ($pair, $isMonthly) {
                        if ($isMonthly) {
                            $q
                                ->where('summaries.year', $pair['year'])
                                ->where('summaries.month', $pair['month']);
                        } else {
                            $q
                                ->where('summaries.year', $pair);
                        }
                    });
                }
            });

        $comparedIdColumnOnly = explode('.', $comparedIdName)[1];
        if ($isMonthly) {
            $groupedData = $summaryQuery
                ->groupBy('education_id', $comparedIdName, 'summaries.year', 'summaries.month')
                ->get()
                ->groupBy(fn ($item) => "{$item->year}-{$item->month}-{$item->$comparedIdColumnOnly}")
                ->map(fn ($group) => $group->keyBy('education_id'));
        } else {
            $groupedData = $summaryQuery
                ->groupBy('education_id', $comparedIdName, 'summaries.year')
                ->get()
                ->groupBy(fn ($item) => "{$item->year}-{$item->$comparedIdColumnOnly}")
                ->map(fn ($group) => $group->keyBy('education_id'));
        }

        $results = collect();

        foreach ($filters['periodValues'] as $period) {
            if ($isMonthly) {
                foreach ($period['months'] as $month) {
                    $tmpResult = [];

                    foreach ($comparedIds as $comparedId) {
                        $details = [];

                        foreach ($educations as $education) {
                            $key = "{$period['year']}-{$month}-{$comparedId}";
                            $total = $groupedData[$key][$education->id]->total ?? 0;

                            $details[] = [
                                'education_id' => $education->id,
                                'education_name' => $education->name,
                                'total' => $total,
                            ];
                        }

                        $tmpResult[] = [
                            'year' => $period['year'],
                            'month' => $month,
                            $comparedIdColumnOnly => $comparedId,
                            'details' => $details,
                        ];
                    }

                    $results->push($tmpResult);
                }
            } else {
                $tmpResult = [];

                foreach ($comparedIds as $comparedId) {
                    $details = [];

                    foreach ($educations as $education) {
                        $key = "{$period}-{$comparedId}";
                        $total = $groupedData[$key][$education->id]->total ?? 0;

                        $details[] = [
                            'education_id' => $education->id,
                            'education_name' => $education->name,
                            'total' => $total,
                        ];
                    }

                    $tmpResult[] = [
                        'year' => $period,
                        $comparedIdColumnOnly => $comparedId,
                        'details' => $details,
                    ];
                }

                $results->push($tmpResult);
            }
        }

        return [
            'results' => $results,
            'educations' => $educations,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getTopUnderFiveFilters(): array
    {
        $previousUrl = url()->previous();
        $queryParams = parse_url($previousUrl, PHP_URL_QUERY);
        parse_str($queryParams, $params);

        $isMonthly = isset($params['filters']['period'])
            || !isset($params['filters']['year']);

        $periodValues = [];

        if ($isMonthly) {
            $period = (
                    isset($params['filters']['period'])
                    && !empty($params['filters']['period'])
                )
                ? $params['filters']['period']
                : Carbon::now()->format('Y-m');

            $period = collect(explode('-', $period))
                ->map(fn ($value): int => (int) $value);

            $periodValues['year'] = $period[0];
            $periodValues['month'] = $period[1];
        } else {
            $period = (
                    isset($params['filters']['year'])
                    && !empty($params['filters']['year'])
                )
                ? $params['filters']['year']
                : Carbon::now()->format('Y');

            $periodValues['year'] = $period;
            $periodValues['month'] = null;
        }

        return [
            'periodValues' => $periodValues,
            'type' => Arr::get($params, 'filters.type', 'top'),
            'isMonthly' => $isMonthly,
        ];
    }

    private function getTopFiveSummaryBuilder(): Builder
    {
        $filters = $this->getTopUnderFiveFilters();
        $isMonthly = $filters['isMonthly'];

        if ($isMonthly) {
            return Summary::query()
                ->where('year', $filters['periodValues']['year'])
                ->where('month', $filters['periodValues']['month'])
                ->groupBy('year', 'month', 'branch_id');
        } else {
            return Summary::query()
                ->where('year', $filters['periodValues']['year'])
                ->groupBy('year', 'branch_id');
        }
    }

    public function getTopFiveFeeRecords(): array
    {
        $filters = $this->getTopUnderFiveFilters();
        $isMonthly = $filters['isMonthly'];

        $selectedColumn = $isMonthly
            ? 'year, month, SUM(registration_fee) as total_registration_fee, SUM(course_fee) as total_course_fee, SUM(total_fee) as total_total_fee, branch_id'
            : 'year, SUM(registration_fee) as total_registration_fee, SUM(course_fee) as total_course_fee, SUM(total_fee) as total_total_fee, branch_id';

        $order = $filters['type'] == 'top' ? 'desc' : 'asc';

        $summaries = $this->getTopFiveSummaryBuilder()
            ->selectRaw($selectedColumn)
            ->orderBy('total_total_fee', $order)
            ->get();

        $branchIds = [
            Arr::get($summaries[0] ?? [], 'branch_id', 0),
            Arr::get($summaries[1] ?? [], 'branch_id', 0),
            Arr::get($summaries[2] ?? [], 'branch_id', 0),
            Arr::get($summaries[3] ?? [], 'branch_id', 0),
            Arr::get($summaries[4] ?? [], 'branch_id', 0),
        ];

        $results = collect();

        if ($filters['isMonthly']) {
            foreach ($branchIds as $branchId) {
                $findSummary = $summaries
                    ->where('year', $filters['periodValues']['year'])
                    ->where('month', $filters['periodValues']['month'])
                    ->where('branch_id', $branchId)
                    ->first();

                $findSummary = $findSummary ? $findSummary->toArray() : [];

                $results->push([
                    ...[
                        'year' => $filters['periodValues']['year'],
                        'month' => $filters['periodValues']['month'],
                        'total_registration_fee' => 0,
                        'total_course_fee' => 0,
                        'total_total_fee' => 0,
                        'branch_id' => (int) $branchId,
                    ],
                    ...$findSummary
                ]);
            }
        } else {
            foreach ($branchIds as $branchId) {
                $findSummary = $summaries
                    ->where('year', $filters['periodValues']['year'])
                    ->where('branch_id', $branchId)
                    ->first();

                $findSummary = $findSummary ? $findSummary->toArray() : [];

                $results->push([
                    ...[
                        'year' => $filters['periodValues']['year'],
                        'total_registration_fee' => 0,
                        'total_course_fee' => 0,
                        'total_total_fee' => 0,
                        'branch_id' => (int) $branchId,
                    ],
                    ...$findSummary
                ]);
            }
        }

        return [
            'records' => $results,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getTopFiveRoyaltyRecords(): array
    {
        $filters = $this->getTopUnderFiveFilters();
        $isMonthly = $filters['isMonthly'];

        $selectedColumn = $isMonthly
            ? 'year, month, SUM(royalty) as total_royalty, branch_id'
            : 'year, SUM(royalty) as total_royalty, branch_id';

        $order = $filters['type'] == 'top' ? 'desc' : 'asc';

        $summaries = $this->getTopFiveSummaryBuilder()
            ->selectRaw($selectedColumn)
            ->orderBy('total_royalty', $order)
            ->get();

        $branchIds = [
            Arr::get($summaries[0] ?? [], 'branch_id', 0),
            Arr::get($summaries[1] ?? [], 'branch_id', 0),
            Arr::get($summaries[2] ?? [], 'branch_id', 0),
            Arr::get($summaries[3] ?? [], 'branch_id', 0),
            Arr::get($summaries[4] ?? [], 'branch_id', 0),
        ];

        $results = collect();

        if ($filters['isMonthly']) {
            foreach ($branchIds as $branchId) {
                $findSummary = $summaries
                    ->where('year', $filters['periodValues']['year'])
                    ->where('month', $filters['periodValues']['month'])
                    ->where('branch_id', $branchId)
                    ->first();

                $findSummary = $findSummary ? $findSummary->toArray() : [];

                $results->push([
                    ...[
                        'year' => $filters['periodValues']['year'],
                        'month' => $filters['periodValues']['month'],
                        'total_royalty' => 0,
                        'branch_id' => (int) $branchId,
                    ],
                    ...$findSummary
                ]);
            }
        } else {
            foreach ($branchIds as $branchId) {
                $findSummary = $summaries
                    ->where('year', $filters['periodValues']['year'])
                    ->where('branch_id', $branchId)
                    ->first();

                $findSummary = $findSummary ? $findSummary->toArray() : [];

                $results->push([
                    ...[
                        'year' => $filters['periodValues']['year'],
                        'total_royalty' => 0,
                        'branch_id' => (int) $branchId,
                    ],
                    ...$findSummary
                ]);
            }
        }

        return [
            'records' => $results,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getTopFiveStudentRecords(): array
    {
        $filters = $this->getTopUnderFiveFilters();
        $isMonthly = $filters['isMonthly'];

        $selectedColumn = $isMonthly
            ? 'year, month, SUM(active_student) as total_active_student, SUM(new_student) as total_new_student, SUM(inactive_student) as total_inactive_student, SUM(leave_student) as total_leave_student, branch_id'
            : 'year, SUM(active_student) as total_active_student, SUM(new_student) as total_new_student, SUM(inactive_student) as total_inactive_student, SUM(leave_student) as total_leave_student, branch_id';

        $order = $filters['type'] == 'top' ? 'desc' : 'asc';

        $summaries = $this->getTopFiveSummaryBuilder()
            ->selectRaw($selectedColumn)
            ->orderBy('total_active_student', $order)
            ->get();

        $branchIds = [
            Arr::get($summaries[0] ?? [], 'branch_id', 0),
            Arr::get($summaries[1] ?? [], 'branch_id', 0),
            Arr::get($summaries[2] ?? [], 'branch_id', 0),
            Arr::get($summaries[3] ?? [], 'branch_id', 0),
            Arr::get($summaries[4] ?? [], 'branch_id', 0),
        ];

        $results = collect();

        if ($filters['isMonthly']) {
            foreach ($branchIds as $branchId) {
                $findSummary = $summaries
                    ->where('year', $filters['periodValues']['year'])
                    ->where('month', $filters['periodValues']['month'])
                    ->where('branch_id', $branchId)
                    ->first();

                $findSummary = $findSummary ? $findSummary->toArray() : [];

                $results->push([
                    ...[
                        'year' => $filters['periodValues']['year'],
                        'month' => $filters['periodValues']['month'],
                        'total_active_student' => 0,
                        'total_new_student' => 0,
                        'total_inactive_student' => 0,
                        'total_leave_student' => 0,
                        'branch_id' => (int) $branchId,
                    ],
                    ...$findSummary
                ]);
            }
        } else {
            foreach ($branchIds as $branchId) {
                $findSummary = $summaries
                    ->where('year', $filters['periodValues']['year'])
                    ->where('branch_id', $branchId)
                    ->first();

                $findSummary = $findSummary ? $findSummary->toArray() : [];

                $results->push([
                    ...[
                        'year' => $filters['periodValues']['year'],
                        'total_active_student' => 0,
                        'total_new_student' => 0,
                        'total_inactive_student' => 0,
                        'total_leave_student' => 0,
                        'branch_id' => (int) $branchId,
                    ],
                    ...$findSummary
                ]);
            }
        }

        return [
            'records' => $results,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getTopFiveActiveStudentLessonRecords(): array
    {
        $filters = $this->getTopUnderFiveFilters();
        $isMonthly = $filters['isMonthly'];

        $selectedColumn = $isMonthly
            ? 'year, month, SUM(active_student) as total_active_student, branch_id'
            : 'year, SUM(active_student) as total_active_student, branch_id';

        $year = $filters['periodValues']['year'];
        $month = $filters['periodValues']['month'];
        $order = $filters['type'] === 'top' ? 'desc' : 'asc';

        $lessons = Lesson::select(['id', 'name'])->orderBy('name', 'asc')->get();

        $groupByColumn = $isMonthly
            ? ['branch_id', 'year', 'month']
            : ['branch_id', 'year'];

        $summaries = $this->getTopFiveSummaryBuilder()
            ->selectRaw($selectedColumn)
            ->where(function (Builder $query) use ($isMonthly, $year, $month) {
                if ($isMonthly) {
                    $query
                        ->where('year', $year)
                        ->where('month', $month);
                } else {
                    $query
                        ->where('year', $year);
                }
            })
            ->groupBy($groupByColumn)
            ->orderBy('total_active_student', $order)
            ->limit(5)
            ->get();

        $branchIds = $summaries->pluck('branch_id')->all();

        $lessonTotals = SummaryActiveStudentLesson::select([
                'lesson_id',
                'summaries.branch_id',
                DB::raw('SUM(summary_active_student_lessons.total) as total'),
            ])
            ->join('summaries', 'summary_active_student_lessons.summary_id', '=', 'summaries.id')
            ->whereIn('summaries.branch_id', $branchIds)
            ->where(function (Builder $query) use ($isMonthly, $year, $month) {
                if ($isMonthly) {
                    $query
                        ->where('summaries.year', $year)
                        ->where('summaries.month', $month);
                } else {
                    $query
                        ->where('summaries.year', $year);
                }
            })
            ->groupBy('lesson_id', 'summaries.branch_id')
            ->get()
            ->groupBy('branch_id');

        $results = collect();

        if ($isMonthly) {
            foreach ($branchIds as $branchId) {
                $lessonData = $lessonTotals->get($branchId, collect())->keyBy('lesson_id');

                $details = $lessons->map(function ($lesson) use ($lessonData) {
                    return [
                        'lesson_id' => $lesson->id,
                        'lesson_name' => $lesson->name,
                        'total' => $lessonData[$lesson->id]->total ?? 0,
                    ];
                })->values();

                $results->push([
                    'year' => $year,
                    'month' => $month,
                    'branch_id' => $branchId,
                    'details' => $details,
                ]);
            }
        } else {
            foreach ($branchIds as $branchId) {
                $lessonData = $lessonTotals->get($branchId, collect())->keyBy('lesson_id');

                $details = $lessons->map(function ($lesson) use ($lessonData) {
                    return [
                        'lesson_id' => $lesson->id,
                        'lesson_name' => $lesson->name,
                        'total' => $lessonData[$lesson->id]->total ?? 0,
                    ];
                })->values();

                $results->push([
                    'year' => $year,
                    'branch_id' => $branchId,
                    'details' => $details,
                ]);
            }
        }

        return [
            'results' => $results,
            'lessons' => $lessons,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getTopFiveActiveStudentEducationRecords(): array
    {
        $filters = $this->getTopUnderFiveFilters();
        $isMonthly = $filters['isMonthly'];

        $selectedColumn = $isMonthly
            ? 'year, month, SUM(active_student) as total_active_student, branch_id'
            : 'year, SUM(active_student) as total_active_student, branch_id';

        $year = $filters['periodValues']['year'];
        $month = $filters['periodValues']['month'];
        $order = $filters['type'] === 'top' ? 'desc' : 'asc';

        $educations = Education::select(['id', 'name'])->orderBy('name', 'asc')->get();

        $groupByColumn = $isMonthly
            ? ['branch_id', 'year', 'month']
            : ['branch_id', 'year'];

        $summaries = $this->getTopFiveSummaryBuilder()
            ->selectRaw($selectedColumn)
            ->where(function (Builder $query) use ($isMonthly, $year, $month) {
                if ($isMonthly) {
                    $query
                        ->where('year', $year)
                        ->where('month', $month);
                } else {
                    $query
                        ->where('year', $year);
                }
            })
            ->groupBy($groupByColumn)
            ->orderBy('total_active_student', $order)
            ->limit(5)
            ->get();

        $branchIds = $summaries->pluck('branch_id')->all();

        $educationTotals = SummaryActiveStudentEducation::select([
                'education_id',
                'summaries.branch_id',
                DB::raw('SUM(summary_active_student_education.total) as total'),
            ])
            ->join('summaries', 'summary_active_student_education.summary_id', '=', 'summaries.id')
            ->whereIn('summaries.branch_id', $branchIds)
            ->where(function (Builder $query) use ($isMonthly, $year, $month) {
                if ($isMonthly) {
                    $query
                        ->where('summaries.year', $year)
                        ->where('summaries.month', $month);
                } else {
                    $query
                        ->where('summaries.year', $year);
                }
            })
            ->groupBy('education_id', 'summaries.branch_id')
            ->get()
            ->groupBy('branch_id');

        $results = collect();

        if ($isMonthly) {
            foreach ($branchIds as $branchId) {
                $educationData = $educationTotals->get($branchId, collect())->keyBy('education_id');

                $details = $educations->map(function ($education) use ($educationData) {
                    return [
                        'education_id' => $education->id,
                        'education_name' => $education->name,
                        'total' => $educationData[$education->id]->total ?? 0,
                    ];
                })->values();

                $results->push([
                    'year' => $year,
                    'month' => $month,
                    'branch_id' => $branchId,
                    'details' => $details,
                ]);
            }
        } else {
            foreach ($branchIds as $branchId) {
                $educationData = $educationTotals->get($branchId, collect())->keyBy('education_id');

                $details = $educations->map(function ($education) use ($educationData) {
                    return [
                        'education_id' => $education->id,
                        'education_name' => $education->name,
                        'total' => $educationData[$education->id]->total ?? 0,
                    ];
                })->values();

                $results->push([
                    'year' => $year,
                    'branch_id' => $branchId,
                    'details' => $details,
                ]);
            }
        }

        return [
            'results' => $results,
            'educations' => $educations,
            'isMonthly' => $isMonthly,
        ];
    }

    protected function getPeriodValues(
        string $startPeriod,
        string $endPeriod,
        bool $isMonthly = true,
    ): array {
        $values = [];

        if ($isMonthly) {
            $startPeriod = collect(explode('-', $startPeriod))->map(fn ($value) => (int) $value);
            $endPeriod = collect(explode('-', $endPeriod))->map(fn ($value) => (int) $value);

            if ($endPeriod[0] >= $startPeriod[0]) {
                $diff = $endPeriod[0] - $startPeriod[0];

                if ($diff > 0) {
                    for ($i = 0; $i <= $diff; $i++) {
                        $year = $startPeriod[0] + $i;

                        if ($i == 0) {
                            $values[] = [
                                'year' => $year,
                                'months' => range($startPeriod[1], 12),
                            ];
                        } elseif ($i == $diff) {
                            $values[] = [
                                'year' => $year,
                                'months' => range(1, $endPeriod[1]),
                            ];
                        } else {
                            $values[] = [
                                'year' => $year,
                                'months' => range(1, 12),
                            ];
                        }
                    }
                } else {
                    $values[] = [
                        'year' => $startPeriod[0],
                        'months' => range($startPeriod[1], $endPeriod[1]),
                    ];
                }
            }
        } else {
            $values = range($startPeriod, $endPeriod);
        }

        return $values;
    }
}