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

class AnalysisService
{
    private function getFilters(): array
    {
        $previousUrl = url()->previous();
        $queryParams = parse_url($previousUrl, PHP_URL_QUERY);
        parse_str($queryParams, $params);

        $endPeriod = $params['filters']['end_period'] ?? Carbon::now()->format('Y-m');
        $startPeriod = $params['filters']['start_period'] ?? Carbon::parse($endPeriod)->subMonth(11)->format('Y-m');

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

        $lessons = Lesson::select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get();

        $results = collect();

        foreach ($filters['periodValues'] as $period) {
            foreach ($period['months'] as $month) {
                $details = [];

                foreach ($lessons as $lesson) {
                    $summaryLessonTotal = SummaryActiveStudentLesson::where('lesson_id', $lesson->id)
                        ->when($filters['branchId'], function (Builder $query, $branchId) {
                            $query->whereHas('summary', function (Builder $query) use ($branchId) {
                                $query->where('branch_id', $branchId);
                            });
                        })
                        ->when($filters['regionId'], function (Builder $query, $regionId) {
                            $query->whereHas('summary', function (Builder $query) use ($regionId) {
                                $query->whereHas('branch', function (Builder $query) use ($regionId) {
                                    $query->where('region_id', $regionId);
                                });
                            });
                        })
                        ->whereHas('summary', function (Builder $query) use ($period, $month) {
                            $query->where(function (Builder $query) use ($period, $month) {
                                $query->where(function (Builder $query) use ($period, $month) {
                                    $query
                                        ->where('year', $period['year'])
                                        ->where('month', $month);
                                });
                            });
                        })
                        ->get()
                        ->sum('total');

                    $details[] = [
                        'lesson_id' => $lesson->id,
                        'lesson_name' => $lesson->name,
                        'total' => $summaryLessonTotal,
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

        $educations = Education::select(['id', 'name', 'color'])
            ->orderBy('name', 'asc')
            ->get();

        $results = collect();

        foreach ($filters['periodValues'] as $period) {
            foreach ($period['months'] as $month) {
                $details = [];

                foreach ($educations as $education) {
                    $summaryEducationTotal = SummaryActiveStudentEducation::where('education_id', $education->id)
                        ->when($filters['branchId'], function (Builder $query, $branchId) {
                            $query->whereHas('summary', function (Builder $query) use ($branchId) {
                                $query->where('branch_id', $branchId);
                            });
                        })
                        ->when($filters['regionId'], function (Builder $query, $regionId) {
                            $query->whereHas('summary', function (Builder $query) use ($regionId) {
                                $query->whereHas('branch', function (Builder $query) use ($regionId) {
                                    $query->where('region_id', $regionId);
                                });
                            });
                        })
                        ->whereHas('summary', function (Builder $query) use ($period, $month) {
                            $query->where(function (Builder $query) use ($period, $month) {
                                $query->where(function (Builder $query) use ($period, $month) {
                                    $query
                                        ->where('year', $period['year'])
                                        ->where('month', $month);
                                });
                            });
                        })
                        ->get()
                        ->sum('total');

                    $details[] = [
                        'education_id' => $education->id,
                        'education_name' => $education->name,
                        'total' => $summaryEducationTotal,
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

        $endPeriod = Arr::get($params, 'filters.end_period', Carbon::now()->format('Y-m'));
        $startPeriod = Arr::get($params, 'filters.start_period', Carbon::parse($endPeriod)->subMonth(11)->format('Y-m'));

        $periodValues = $this->getPeriodValues($startPeriod, $endPeriod);

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
        ];
    }

    private function getCompareSummaryBuilder(): Builder
    {
        $filters = $this->getCompareFilters();

        $isComparedBranchExists = !is_null($filters['firstBranchId']) && !is_null($filters['secondBranchId']);
        $isComparedRegionExists = !is_null($filters['firstRegionId']) && !is_null($filters['secondRegionId']);

        return Summary::when($isComparedBranchExists, function (Builder $query) use ($filters) {
                $query
                    ->where('branch_id', $filters['firstBranchId'])
                    ->orWhere('branch_id', $filters['secondBranchId']);
            })
            ->when($isComparedRegionExists, function (Builder $query) use ($filters) {
                $query
                    ->where('region_id', $filters['firstRegionId'])
                    ->orWhere('region_id', $filters['secondRegionId']);
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

        $lessons = Lesson::select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get();

        $branchIds = [
            $filters['firstBranchId'],
            $filters['secondBranchId'],
        ];

        $results = collect();

        foreach ($filters['periodValues'] as $period) {
            foreach ($period['months'] as $month) {
                $tmpResult = null;

                foreach ($branchIds as $branchId) {
                    $details = [];

                    foreach ($lessons as $lesson) {
                        $summaryLessonTotal = SummaryActiveStudentLesson::where('lesson_id', $lesson->id)
                            ->when($branchId, function (Builder $query, $branchId) {
                                $query->whereHas('summary', function (Builder $query) use ($branchId) {
                                    $query->where('branch_id', $branchId);
                                });
                            })
                            // ->when($filters['regionId'], function (Builder $query, $regionId) {
                            //     $query->whereHas('summary', function (Builder $query) use ($regionId) {
                            //         $query->whereHas('branch', function (Builder $query) use ($regionId) {
                            //             $query->where('region_id', $regionId);
                            //         });
                            //     });
                            // })
                            ->whereHas('summary', function (Builder $query) use ($period, $month) {
                                $query->where(function (Builder $query) use ($period, $month) {
                                    $query->where(function (Builder $query) use ($period, $month) {
                                        $query
                                            ->where('year', $period['year'])
                                            ->where('month', $month);
                                    });
                                });
                            })
                            ->get()
                            ->sum('total');

                        $details[] = [
                            'lesson_id' => $lesson->id,
                            'lesson_name' => $lesson->name,
                            'total' => $summaryLessonTotal,
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

        $educations = Education::select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get();

        $branchIds = [
            $filters['firstBranchId'],
            $filters['secondBranchId'],
        ];

        $results = collect();

        foreach ($filters['periodValues'] as $period) {
            foreach ($period['months'] as $month) {
                $tmpResult = null;

                foreach ($branchIds as $branchId) {
                    $details = [];

                    foreach ($educations as $education) {
                        $summaryEducationTotal = SummaryActiveStudentEducation::where('education_id', $education->id)
                            ->when($branchId, function (Builder $query, $branchId) {
                                $query->whereHas('summary', function (Builder $query) use ($branchId) {
                                    $query->where('branch_id', $branchId);
                                });
                            })
                            // ->when($filters['regionId'], function (Builder $query, $regionId) {
                            //     $query->whereHas('summary', function (Builder $query) use ($regionId) {
                            //         $query->whereHas('branch', function (Builder $query) use ($regionId) {
                            //             $query->where('region_id', $regionId);
                            //         });
                            //     });
                            // })
                            ->whereHas('summary', function (Builder $query) use ($period, $month) {
                                $query->where(function (Builder $query) use ($period, $month) {
                                    $query->where(function (Builder $query) use ($period, $month) {
                                        $query
                                            ->where('year', $period['year'])
                                            ->where('month', $month);
                                    });
                                });
                            })
                            ->get()
                            ->sum('total');

                        $details[] = [
                            'education_id' => $education->id,
                            'education_name' => $education->name,
                            'total' => $summaryEducationTotal,
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

        $period = collect(explode('-', Arr::get($params, 'filters.period', Carbon::now()->format('Y-m'))))
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

        $lessons = Lesson::select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get();

        $order = $filters['type'] == 'top' ? 'desc' : 'asc';

        $summaries = $this->getTopFiveSummaryBuilder()
            ->selectRaw('year, month, SUM(active_student) as total_active_student, branch_id')
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
            $details = [];

            foreach ($lessons as $lesson) {
                $summaryLessonTotal = SummaryActiveStudentLesson::where('lesson_id', $lesson->id)
                    ->whereHas('summary', function (Builder $query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->whereHas('summary', function (Builder $query) use ($filters) {
                        $query
                            ->where('year', $filters['periodValues']['year'])
                            ->where('month', $filters['periodValues']['month']);
                    })
                    ->get()
                    ->sum('total');

                $details[] = [
                    'lesson_id' => $lesson->id,
                    'lesson_name' => $lesson->name,
                    'total' => $summaryLessonTotal,
                ];
            }

            $results->push([
                'year' => $filters['periodValues']['year'],
                'month' =>$filters['periodValues']['month'],
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

        $educations = Education::select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get();

        $order = $filters['type'] == 'top' ? 'desc' : 'asc';

        $summaries = $this->getTopFiveSummaryBuilder()
            ->selectRaw('year, month, SUM(active_student) as total_active_student, branch_id')
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
            $details = [];

            foreach ($educations as $education) {
                $summaryEducationTotal = SummaryActiveStudentEducation::where('education_id', $education->id)
                    ->whereHas('summary', function (Builder $query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->whereHas('summary', function (Builder $query) use ($filters) {
                        $query
                            ->where('year', $filters['periodValues']['year'])
                            ->where('month', $filters['periodValues']['month']);
                    })
                    ->get()
                    ->sum('total');

                $details[] = [
                    'education_id' => $education->id,
                    'education_name' => $education->name,
                    'total' => $summaryEducationTotal,
                ];
            }

            $results->push([
                'year' => $filters['periodValues']['year'],
                'month' =>$filters['periodValues']['month'],
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