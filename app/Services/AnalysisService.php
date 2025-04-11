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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalysisService
{
    private function getFilters(): array
    {
        $previousUrl = url()->previous();
        $queryParams = parse_url($previousUrl, PHP_URL_QUERY);
        parse_str($queryParams, $params);

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

        $periodValues = $this->getPeriodValues($startPeriod, $endPeriod);

        $branchId = $params['filters']['branch_id'] ?? null;
        $regionId = $params['filters']['region_id'] ?? null;

        return [
            'periodValues' => $periodValues,
            'branchId' => $branchId,
            'regionId' => $regionId,
        ];
    }

    private function getSummaryBuilder(): Builder
    {
        $filters = $this->getFilters();

        return Summary::when($filters['branchId'], function (Builder $query, $branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($filters['regionId'], function (Builder $query, $regionId) {
                $query->whereHas('branch', function (Builder $query) use ($regionId) {
                    $query->where('region_id', $regionId);
                });
            })
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
    }

    public function getFeeRecords(): Collection
    {
        $filters = $this->getFilters();

        $summaries = $this->getSummaryBuilder()
            ->selectRaw('year, month, SUM(registration_fee) as total_registration_fee, SUM(course_fee) as total_course_fee, SUM(total_fee) as total_total_fee')
            ->get();

        $results = collect();

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

        return $results;
    }

    public function getRoyaltyRecords(): Collection
    {
        $filters = $this->getFilters();

        $summaries = $this->getSummaryBuilder()
            ->selectRaw('year, month, SUM(royalty) as total_royalty')
            ->get();

        $results = collect();

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

        return $results;
    }

    public function getStudentRecords(): Collection
    {
        $filters = $this->getFilters();

        $summaries = $this->getSummaryBuilder()
            ->selectRaw('year, month, SUM(active_student) as total_active_student, SUM(new_student) as total_new_student, SUM(inactive_student) as total_inactive_student, SUM(leave_student) as total_leave_student')
            ->get();

        $results = collect();

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

        return $results;
    }

    public function getActiveStudentLessonRecords(): array
    {
        $filters = $this->getFilters();

        $lessons = Lesson::select(['id', 'name'])->orderBy('name', 'asc')->get();

        $periodMonthPairs = collect($filters['periodValues'])->flatMap(function ($period) {
            return collect($period['months'])->map(function ($month) use ($period) {
                return ['year' => $period['year'], 'month' => $month];
            });
        });

        $summaryQuery = SummaryActiveStudentLesson::select([
                'lesson_id',
                'summaries.year',
                'summaries.month',
                DB::raw('SUM(summary_active_student_lessons.total) as total')
            ])
            ->join('summaries', 'summary_active_student_lessons.summary_id', '=', 'summaries.id')
            ->join('branches', 'summaries.branch_id', '=', 'branches.id')
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->where(function ($query) use ($periodMonthPairs) {
                foreach ($periodMonthPairs as $pair) {
                    $query->orWhere(function ($q) use ($pair) {
                        $q->where('summaries.year', $pair['year'])
                        ->where('summaries.month', $pair['month']);
                    });
                }
            });

        if ($filters['branchId']) {
            $summaryQuery->where('summaries.branch_id', $filters['branchId']);
        }
        if ($filters['regionId']) {
            $summaryQuery->where('branches.region_id', $filters['regionId']);
        }

        $summaryData = $summaryQuery
            ->groupBy('lesson_id', 'summaries.year', 'summaries.month')
            ->get()
            ->groupBy(fn ($item) => "{$item->year}-{$item->month}")
            ->map(fn ($group) => $group->keyBy('lesson_id'));

        $results = collect();

        foreach ($filters['periodValues'] as $period) {
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
        }

        return [
            'results' => $results,
            'lessons' => $lessons,
        ];
    }

    public function getActiveStudentEducationRecords(): array
    {
        $filters = $this->getFilters();

        $educations = Education::select(['id', 'name', 'color'])->orderBy('name', 'asc')->get();

        $periodMonthPairs = collect($filters['periodValues'])->flatMap(function ($period) {
            return collect($period['months'])->map(function ($month) use ($period) {
                return ['year' => $period['year'], 'month' => $month];
            });
        });

        $summaryQuery = SummaryActiveStudentEducation::select([
                'education_id',
                'summaries.year',
                'summaries.month',
                DB::raw('SUM(summary_active_student_education.total) as total')
            ])
            ->join('summaries', 'summary_active_student_education.summary_id', '=', 'summaries.id')
            ->join('branches', 'summaries.branch_id', '=', 'branches.id')
            ->whereIn('education_id', $educations->pluck('id'))
            ->where(function ($query) use ($periodMonthPairs) {
                foreach ($periodMonthPairs as $pair) {
                    $query->orWhere(function ($q) use ($pair) {
                        $q->where('summaries.year', $pair['year'])
                        ->where('summaries.month', $pair['month']);
                    });
                }
            });

        if ($filters['branchId']) {
            $summaryQuery->where('summaries.branch_id', $filters['branchId']);
        }
        if ($filters['regionId']) {
            $summaryQuery->where('branches.region_id', $filters['regionId']);
        }

        $summaryData = $summaryQuery
            ->groupBy('education_id', 'summaries.year', 'summaries.month')
            ->get()
            ->groupBy(fn ($item) => "{$item->year}-{$item->month}")
            ->map(fn ($group) => $group->keyBy('education_id'));

        $results = collect();

        foreach ($filters['periodValues'] as $period) {
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
        }

        return [
            'results' => $results,
            'educations' => $educations,
        ];
    }

    public function getCompareFilters(): array
    {
        $previousUrl = url()->previous();
        $queryParams = parse_url($previousUrl, PHP_URL_QUERY);
        parse_str($queryParams, $params);

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

        $periodValues = $this->getPeriodValues($startPeriod, $endPeriod);

        $firstBranchId = Arr::get($params, 'filters.first_branch_id');
        $secondBranchId = Arr::get($params, 'filters.second_branch_id');

        return [
            'periodValues' => $periodValues,
            'firstBranchId' => $firstBranchId,
            'secondBranchId' => $secondBranchId,
        ];
    }

    private function getCompareSummaryBuilder(): Builder
    {
        $filters = $this->getCompareFilters();

        $isComparedBranchExists = !is_null($filters['firstBranchId']) && !is_null($filters['secondBranchId']);

        return Summary::when($isComparedBranchExists, function (Builder $query) use ($filters) {
                $query
                    ->where('branch_id', $filters['firstBranchId'])
                    ->orWhere('branch_id', $filters['secondBranchId']);
            })
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
            ->groupBy('year', 'month', 'branch_id')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc');
    }

    public function getCompareFeeRecords(): Collection
    {
        $filters = $this->getCompareFilters();

        $summaries = $this->getCompareSummaryBuilder()
            ->selectRaw('year, month, SUM(registration_fee) as total_registration_fee, SUM(course_fee) as total_course_fee, SUM(total_fee) as total_total_fee, branch_id')
            ->get();

        $branchIds = [
            $filters['firstBranchId'],
            $filters['secondBranchId'],
        ];

        $results = collect();

        foreach ($filters['periodValues'] as $period) {
            foreach ($period['months'] as $month) {
                $tmpResult = [];

                foreach ($branchIds as $branchId) {
                    $findSummary = $summaries
                        ->where('year', $period['year'])
                        ->where('month', $month)
                        ->where('branch_id', $branchId)
                        ->first();

                    $findSummary = $findSummary ? $findSummary->toArray() : [];

                    $tmpResult[] = [
                        ...[
                            'year' => $period['year'],
                            'month' => $month,
                            'total_registration_fee' => 0,
                            'total_course_fee' => 0,
                            'total_total_fee' => 0,
                            'branch_id' => (int) $branchId,
                        ],
                        ...$findSummary
                    ];
                }

                $results->push($tmpResult);
            }
        }

        return $results;
    }

    public function getCompareRoyaltyRecords(): Collection
    {
        $filters = $this->getCompareFilters();

        $summaries = $this->getCompareSummaryBuilder()
            ->selectRaw('year, month, SUM(royalty) as total_royalty, branch_id')
            ->get();

        $branchIds = [
            $filters['firstBranchId'],
            $filters['secondBranchId'],
        ];

        $results = collect();

        foreach ($filters['periodValues'] as $period) {
            foreach ($period['months'] as $month) {
                $tmpResult = [];

                foreach ($branchIds as $branchId) {
                    $findSummary = $summaries
                        ->where('year', $period['year'])
                        ->where('month', $month)
                        ->where('branch_id', $branchId)
                        ->first();

                    $findSummary = $findSummary ? $findSummary->toArray() : [];

                    $tmpResult[] = [
                        ...[
                            'year' => $period['year'],
                            'month' => $month,
                            'total_royalty' => 0,
                            'branch_id' => (int) $branchId,
                        ],
                        ...$findSummary
                    ];
                }

                $results->push($tmpResult);
            }
        }

        return $results;
    }

    public function getCompareStudentRecords(): Collection
    {
        $filters = $this->getCompareFilters();

        $summaries = $this->getCompareSummaryBuilder()
            ->selectRaw('year, month, SUM(active_student) as total_active_student, SUM(new_student) as total_new_student, SUM(inactive_student) as total_inactive_student, SUM(leave_student) as total_leave_student, branch_id')
            ->get();

        $branchIds = [
            $filters['firstBranchId'],
            $filters['secondBranchId'],
        ];

        $results = collect();

        foreach ($filters['periodValues'] as $period) {
            foreach ($period['months'] as $month) {
                $tmpResult = [];

                foreach ($branchIds as $branchId) {
                    $findSummary = $summaries
                        ->where('year', $period['year'])
                        ->where('month', $month)
                        ->where('branch_id', $branchId)
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
                            'branch_id' => (int) $branchId,
                        ],
                        ...$findSummary
                    ];
                }

                $results->push($tmpResult);
            }
        }

        return $results;
    }

    public function getCompareActiveStudentLessonRecords(): array
    {
        $filters = $this->getCompareFilters();

        $lessons = Lesson::select(['id', 'name'])->orderBy('name', 'asc')->get();

        $branchIds = [
            $filters['firstBranchId'],
            $filters['secondBranchId'],
        ];

        $periodMonthPairs = collect($filters['periodValues'])->flatMap(function ($period) {
            return collect($period['months'])->map(function ($month) use ($period) {
                return ['year' => $period['year'], 'month' => $month];
            });
        });

        $lessonIds = $lessons->pluck('id')->all();

        $summaryQuery = SummaryActiveStudentLesson::select([
                'lesson_id',
                'summaries.branch_id',
                'summaries.year',
                'summaries.month',
                DB::raw('SUM(summary_active_student_lessons.total) as total'),
            ])
            ->join('summaries', 'summary_active_student_lessons.summary_id', '=', 'summaries.id')
            ->whereIn('lesson_id', $lessonIds)
            ->whereIn('summaries.branch_id', $branchIds)
            ->where(function ($query) use ($periodMonthPairs) {
                foreach ($periodMonthPairs as $pair) {
                    $query->orWhere(function ($q) use ($pair) {
                        $q->where('summaries.year', $pair['year'])
                        ->where('summaries.month', $pair['month']);
                    });
                }
            })
            ->groupBy('lesson_id', 'summaries.branch_id', 'summaries.year', 'summaries.month')
            ->get();

        $groupedData = $summaryQuery->groupBy(fn ($item) => "{$item->year}-{$item->month}-{$item->branch_id}")
            ->map(fn ($group) => $group->keyBy('lesson_id'));

        $results = collect();

        foreach ($filters['periodValues'] as $period) {
            foreach ($period['months'] as $month) {
                $tmpResult = [];

                foreach ($branchIds as $branchId) {
                    $details = [];

                    foreach ($lessons as $lesson) {
                        $key = "{$period['year']}-{$month}-{$branchId}";
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
                        'branch_id' => $branchId,
                        'details' => $details,
                    ];
                }

                $results->push($tmpResult);
            }
        }

        return [
            'results' => $results,
            'lessons' => $lessons,
        ];
    }

    public function getCompareActiveStudentEducationRecords(): array
    {
        $filters = $this->getCompareFilters();

        $educations = Education::select(['id', 'name'])->orderBy('name', 'asc')->get();

        $branchIds = [
            $filters['firstBranchId'],
            $filters['secondBranchId'],
        ];

        $periodMonthPairs = collect($filters['periodValues'])->flatMap(function ($period) {
            return collect($period['months'])->map(function ($month) use ($period) {
                return ['year' => $period['year'], 'month' => $month];
            });
        });

        $educationIds = $educations->pluck('id')->all();

        $summaryData = SummaryActiveStudentEducation::select([
                'education_id',
                'summaries.branch_id',
                'summaries.year',
                'summaries.month',
                DB::raw('SUM(summary_active_student_education.total) as total'),
            ])
            ->join('summaries', 'summary_active_student_education.summary_id', '=', 'summaries.id')
            ->whereIn('education_id', $educationIds)
            ->whereIn('summaries.branch_id', $branchIds)
            ->where(function ($query) use ($periodMonthPairs) {
                foreach ($periodMonthPairs as $pair) {
                    $query->orWhere(function ($q) use ($pair) {
                        $q->where('summaries.year', $pair['year'])
                        ->where('summaries.month', $pair['month']);
                    });
                }
            })
            ->groupBy('education_id', 'summaries.branch_id', 'summaries.year', 'summaries.month')
            ->get();

        $groupedData = $summaryData->groupBy(fn ($item) => "{$item->year}-{$item->month}-{$item->branch_id}")
            ->map(fn ($group) => $group->keyBy('education_id'));

        $results = collect();

        foreach ($filters['periodValues'] as $period) {
            foreach ($period['months'] as $month) {
                $tmpResult = [];

                foreach ($branchIds as $branchId) {
                    $details = [];

                    foreach ($educations as $education) {
                        $key = "{$period['year']}-{$month}-{$branchId}";
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
                        'branch_id' => $branchId,
                        'details' => $details,
                    ];
                }

                $results->push($tmpResult);
            }
        }

        return [
            'results' => $results,
            'educations' => $educations,
        ];
    }

    public function getTopUnderFiveFilters(): array
    {
        $previousUrl = url()->previous();
        $queryParams = parse_url($previousUrl, PHP_URL_QUERY);
        parse_str($queryParams, $params);

        $period = (
                isset($params['filters']['period'])
                && !empty($params['filters']['period'])
            )
            ? $params['filters']['period']
            : Carbon::now()->format('Y-m');
        $period = collect(explode('-', $period))
            ->map(fn ($value): int => (int) $value);

        $periodValues = [];
        $periodValues['year'] = $period[0];
        $periodValues['month'] = $period[1];

        return [
            'periodValues' => $periodValues,
            'type' => Arr::get($params, 'filters.type', 'top'),
        ];
    }

    private function getTopFiveSummaryBuilder(): Builder
    {
        $filters = $this->getTopUnderFiveFilters();

        return Summary::where('year', $filters['periodValues']['year'])
            ->where('month', $filters['periodValues']['month'])
            ->groupBy('year', 'month', 'branch_id');
    }

    public function getTopFiveFeeRecords(): Collection
    {
        $filters = $this->getTopUnderFiveFilters();

        $order = $filters['type'] == 'top' ? 'desc' : 'asc';

        $summaries = $this->getTopFiveSummaryBuilder()
            ->selectRaw('year, month, SUM(registration_fee) as total_registration_fee, SUM(course_fee) as total_course_fee, SUM(total_fee) as total_total_fee, branch_id')
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

        return $results;
    }

    public function getTopFiveRoyaltyRecords(): Collection
    {
        $filters = $this->getTopUnderFiveFilters();

        $order = $filters['type'] == 'top' ? 'desc' : 'asc';

        $summaries = $this->getTopFiveSummaryBuilder()
            ->selectRaw('year, month, SUM(royalty) as total_royalty, branch_id')
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

        return $results;
    }

    public function getTopFiveStudentRecords(): Collection
    {
        $filters = $this->getTopUnderFiveFilters();

        $order = $filters['type'] == 'top' ? 'desc' : 'asc';

        $summaries = $this->getTopFiveSummaryBuilder()
            ->selectRaw('year, month, SUM(active_student) as total_active_student, SUM(new_student) as total_new_student, SUM(inactive_student) as total_inactive_student, SUM(leave_student) as total_leave_student, branch_id')
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

        return $results;
    }

    public function getTopFiveActiveStudentLessonRecords(): array
    {
        $filters = $this->getTopUnderFiveFilters();

        $year = $filters['periodValues']['year'];
        $month = $filters['periodValues']['month'];
        $order = $filters['type'] === 'top' ? 'desc' : 'asc';

        $lessons = Lesson::select(['id', 'name'])->orderBy('name', 'asc')->get();

        $summaries = $this->getTopFiveSummaryBuilder()
            ->selectRaw('year, month, SUM(active_student) as total_active_student, branch_id')
            ->where('year', $year)
            ->where('month', $month)
            ->groupBy('branch_id', 'year', 'month')
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
            ->where('summaries.year', $year)
            ->where('summaries.month', $month)
            ->groupBy('lesson_id', 'summaries.branch_id')
            ->get()
            ->groupBy('branch_id');

        $results = collect();

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

        return [
            'results' => $results,
            'lessons' => $lessons,
        ];
    }

    public function getTopFiveActiveStudentEducationRecords(): array
    {
        $filters = $this->getTopUnderFiveFilters();

        $year = $filters['periodValues']['year'];
        $month = $filters['periodValues']['month'];
        $order = $filters['type'] === 'top' ? 'desc' : 'asc';

        $educations = Education::select(['id', 'name'])->orderBy('name', 'asc')->get();

        $summaries = $this->getTopFiveSummaryBuilder()
            ->selectRaw('year, month, SUM(active_student) as total_active_student, branch_id')
            ->where('year', $year)
            ->where('month', $month)
            ->groupBy('branch_id', 'year', 'month')
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
            ->where('summaries.year', $year)
            ->where('summaries.month', $month)
            ->groupBy('education_id', 'summaries.branch_id')
            ->get()
            ->groupBy('branch_id');

        $results = collect();

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

        return [
            'results' => $results,
            'educations' => $educations,
        ];
    }

    private function getPeriodValues(
        string $startPeriod,
        string $endPeriod,
    ): array {
        $values = [];

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

        return $values;
    }
}