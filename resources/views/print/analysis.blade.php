@extends('print.layout')

@section('content')
    <div class="container py-5 bg-white">
        <div class="text-center mb-4">
            <h2 class="text-center">
                {{ __('SUMMARY OF RECEIPT REPORT') }}
            </h2>

            <x-print.sub-header
                :is-branch-filtered="$isBranchFiltered"
                :is-region-filtered="$isRegionFiltered"
                :branch-name="$branchName"
                :region-name="$regionName"
                :start-period="$startPeriod"
                :end-period="$endPeriod"
                />
        </div>

        <x-print.analysis.fee-record-chart
            :records="$feeRecords"
            :is-monthly="$isMonthly"
            />

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th scope="col">{{ __('Period') }}</th>
                        <th scope="col">{{ __('Registration fee') }}</th>
                        <th scope="col">{{ __('Course fee') }}</th>
                        <th scope="col">{{ __('Total fee') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($feeRecords as $record)
                    <tr>
                        <td>
                            @if ($isMonthly)
                                {{ \App\Enums\Month::name($record['month']) . " {$record['year']}" }}
                            @else
                                {{ $record['year'] }}
                            @endif
                        </td>
                        <td class="text-end">{{ "Rp. ".number_format($record['total_registration_fee']) }}</td>
                        <td class="text-end">{{ "Rp. ".number_format($record['total_course_fee']) }}</td>
                        <td class="text-end">{{ "Rp. ".number_format($record['total_total_fee']) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>{{ __('Total') }}</th>
                        <th class="text-end">
                            {{ "Rp. ".number_format($feeRecords->sum('total_registration_fee')) }}
                        </th>
                        <th class="text-end">
                            {{ "Rp. ".number_format($feeRecords->sum('total_course_fee')) }}
                        </th>
                        <th class="text-end">
                            {{ "Rp. ".number_format($feeRecords->sum('total_total_fee')) }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="container py-5 bg-white">
        <div class="text-center mb-4">
            <h2 class="text-center">
                {{ __('SUMMARY OF TOTAL ROYALTY') }}
            </h2>

            <x-print.sub-header
                :is-branch-filtered="$isBranchFiltered"
                :is-region-filtered="$isRegionFiltered"
                :branch-name="$branchName"
                :region-name="$regionName"
                :start-period="$startPeriod"
                :end-period="$endPeriod"
                />
        </div>

        <x-print.analysis.royalty-record-chart
            :records="$royaltyRecords"
            :is-monthly="$isMonthly"
            />

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th scope="col">{{ __('Period') }}</th>
                        <th scope="col">{{ __('Royalty') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($royaltyRecords as $record)
                    <tr>
                        <td>
                            @if ($isMonthly)
                                {{ \App\Enums\Month::name($record['month']) . " {$record['year']}" }}
                            @else
                                {{ $record['year'] }}
                            @endif
                        </td>
                        <td class="text-end">{{ "Rp. ".number_format($record['total_royalty']) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>{{ __('Total') }}</th>
                        <th class="text-end">
                            {{ "Rp. ".number_format($royaltyRecords->sum('total_royalty')) }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="container py-5 bg-white">
        <div class="text-center mb-4">
            <h2 class="text-center">
                {{ __('SUMMARY OF STUDENT') }}
            </h2>

            <x-print.sub-header
                :is-branch-filtered="$isBranchFiltered"
                :is-region-filtered="$isRegionFiltered"
                :branch-name="$branchName"
                :region-name="$regionName"
                :start-period="$startPeriod"
                :end-period="$endPeriod"
                />
        </div>

        <x-print.analysis.student-record-chart
            :records="$studentRecords"
            :is-monthly="$isMonthly"
            />

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th scope="col">{{ __('Period') }}</th>
                        <th scope="col">{{ __('Active student') }}</th>
                        <th scope="col">{{ __('New student') }}</th>
                        <th scope="col">{{ __('Inactive student') }}</th>
                        <th scope="col">{{ __('Leave student') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($studentRecords as $record)
                    <tr>
                        <td>
                            @if ($isMonthly)
                                {{ \App\Enums\Month::name($record['month']) . " {$record['year']}" }}
                            @else
                                {{ $record['year'] }}
                            @endif
                        </td>
                        <td class="text-end">{{ number_format($record['total_active_student']) }}</td>
                        <td class="text-end">{{ number_format($record['total_new_student']) }}</td>
                        <td class="text-end">{{ number_format($record['total_inactive_student']) }}</td>
                        <td class="text-end">{{ number_format($record['total_leave_student']) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>{{ __('Total') }}</th>
                        <th class="text-end">
                            {{ number_format($studentRecords->sum('total_active_student')) }}
                        </th>
                        <th class="text-end">
                            {{ number_format($studentRecords->sum('total_new_student')) }}
                        </th>
                        <th class="text-end">
                            {{ number_format($studentRecords->sum('total_inactive_student')) }}
                        </th>
                        <th class="text-end">
                            {{ number_format($studentRecords->sum('total_leave_student')) }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="container py-5 bg-white">
        <div class="text-center mb-4">
            <h2 class="text-center">
                {{ __('SUMMARY OF ACTIVE STUDENT BASED ON LESSON') }}
            </h2>

            <x-print.sub-header
                :is-branch-filtered="$isBranchFiltered"
                :is-region-filtered="$isRegionFiltered"
                :branch-name="$branchName"
                :region-name="$regionName"
                :start-period="$startPeriod"
                :end-period="$endPeriod"
                />
        </div>

        <x-print.analysis.student-lesson-record-chart
            :records="$studentLessonRecords"
            :lessons="$lessons"
            :is-monthly="$isMonthly"
            />

        @foreach ($lessons->chunk(10) as $lessonChunks)
            @php
                $from = $loop->index * 10;
                $until = $from + 10;
            @endphp
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">{{ __('Period') }}</th>
                            @foreach ($lessonChunks as $lesson)
                                <th scope="col">{{ $lesson->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentLessonRecords as $record)
                        <tr>
                            <td>
                                @if ($isMonthly)
                                    {{ \App\Enums\Month::name($record['month']) . " {$record['year']}" }}
                                @else
                                    {{ $record['year'] }}
                                @endif
                            </td>

                            @foreach(collect($record['details'])->slice($from, $until) as $detail)
                                <td class="text-end">{{ number_format($detail['total']) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

    <div class="page-break"></div>

    <div class="container py-5 bg-white">
        <div class="text-center mb-4">
            <h2 class="text-center">
                {{ __('SUMMARY OF ACTIVE STUDENT BASED ON EDUCATION') }}
            </h2>

            <x-print.sub-header
                :is-branch-filtered="$isBranchFiltered"
                :is-region-filtered="$isRegionFiltered"
                :branch-name="$branchName"
                :region-name="$regionName"
                :start-period="$startPeriod"
                :end-period="$endPeriod"
                />
        </div>

        <x-print.analysis.student-education-record-chart
            :records="$studentEducationRecords"
            :educations="$educations"
            :is-monthly="$isMonthly"
            />

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th scope="col">{{ __('Period') }}</th>
                        @foreach ($educations as $education)
                            <th scope="col">{{ $education->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($studentEducationRecords as $record)
                    <tr>
                        <td>
                            @if ($isMonthly)
                                {{ \App\Enums\Month::name($record['month']) . " {$record['year']}" }}
                            @else
                                {{ $record['year'] }}
                            @endif
                        </td>

                        @foreach($record['details'] as $detail)
                            <td class="text-end">{{ number_format($detail['total']) }}</td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection