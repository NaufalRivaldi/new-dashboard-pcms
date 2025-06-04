@extends('print.layout')

@section('content')
    <div class="container py-5 bg-white">
        <div class="text-center mb-4">
            <h2 class="text-center">
                {{ __('COMPARING OF RECEIPT REPORT') }}
            </h2>

            <x-print.sub-header-compare
                :is-branch-filtered="$isBranchFiltered"
                :is-region-filtered="$isRegionFiltered"
                :first-branch-name="$firstBranchName"
                :second-branch-name="$secondBranchName"
                :first-region-name="$firstRegionName"
                :second-region-name="$secondRegionName"
                :start-period="$startPeriod"
                :end-period="$endPeriod"
                />
        </div>

        <x-print.compare.fee-record-chart
            :records="$feeRecords"
            :is-monthly="$isMonthly"
            />

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th scope="col" rowspan="2">{{ __('Period') }}</th>
                        <th scope="col" colspan="2">{{ __('Registration fee') }}</th>
                        <th scope="col" colspan="2">{{ __('Course fee') }}</th>
                        <th scope="col" colspan="2">{{ __('Total fee') }}</th>
                    </tr>
                    <tr>
                        <th>{{ $firstBranchName ?? $firstRegionName }}</th>
                        <th>{{ $secondBranchName ?? $secondRegionName }}</th>
                        <th>{{ $firstBranchName ?? $firstRegionName }}</th>
                        <th>{{ $secondBranchName ?? $secondRegionName }}</th>
                        <th>{{ $firstBranchName ?? $firstRegionName }}</th>
                        <th>{{ $secondBranchName ?? $secondRegionName }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($feeRecords as $record)
                    <tr>
                        <td>
                            @if ($isMonthly)
                                {{ \App\Enums\Month::name($record[0]['month']) . " {$record[0]['year']}" }}
                            @else
                                {{ $record[0]['year'] }}
                            @endif
                        </td>
                        <td class="text-end">{{ "Rp. ".number_format($record[0]['total_registration_fee']) }}</td>
                        <td class="text-end">{{ "Rp. ".number_format($record[1]['total_registration_fee']) }}</td>
                        <td class="text-end">{{ "Rp. ".number_format($record[0]['total_course_fee']) }}</td>
                        <td class="text-end">{{ "Rp. ".number_format($record[1]['total_course_fee']) }}</td>
                        <td class="text-end">{{ "Rp. ".number_format($record[0]['total_total_fee']) }}</td>
                        <td class="text-end">{{ "Rp. ".number_format($record[1]['total_total_fee']) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>{{ __('Total') }}</th>
                        <th class="text-end">
                            {{ "Rp. ".number_format($feeRecords->sum('0.total_registration_fee')) }}
                        </th>
                        <th class="text-end">
                            {{ "Rp. ".number_format($feeRecords->sum('1.total_registration_fee')) }}
                        </th>
                        <th class="text-end">
                            {{ "Rp. ".number_format($feeRecords->sum('0.total_course_fee')) }}
                        </th>
                        <th class="text-end">
                            {{ "Rp. ".number_format($feeRecords->sum('1.total_course_fee')) }}
                        </th>
                        <th class="text-end">
                            {{ "Rp. ".number_format($feeRecords->sum('0.total_total_fee')) }}
                        </th>
                        <th class="text-end">
                            {{ "Rp. ".number_format($feeRecords->sum('1.total_total_fee')) }}
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
                {{ __('COMPARING OF TOTAL ROYALTY') }}
            </h2>

            <x-print.sub-header-compare
                :is-branch-filtered="$isBranchFiltered"
                :is-region-filtered="$isRegionFiltered"
                :first-branch-name="$firstBranchName"
                :second-branch-name="$secondBranchName"
                :first-region-name="$firstRegionName"
                :second-region-name="$secondRegionName"
                :start-period="$startPeriod"
                :end-period="$endPeriod"
                />
        </div>

        <x-print.compare.royalty-record-chart
            :records="$royaltyRecords"
            :is-monthly="$isMonthly"
            />

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th scope="col" rowspan="2">{{ __('Period') }}</th>
                        <th scope="col" colspan="2">{{ __('Royalty') }}</th>
                    </tr>
                    <tr>
                        <th>{{ $firstBranchName ?? $firstRegionName }}</th>
                        <th>{{ $secondBranchName ?? $secondRegionName }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($royaltyRecords as $record)
                    <tr>
                        <td>
                            @if ($isMonthly)
                                {{ \App\Enums\Month::name($record[0]['month']) . " {$record[0]['year']}" }}
                            @else
                                {{ $record[0]['year'] }}
                            @endif
                        </td>
                        <td class="text-end">{{ "Rp. ".number_format($record[0]['total_royalty']) }}</td>
                        <td class="text-end">{{ "Rp. ".number_format($record[1]['total_royalty']) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>{{ __('Total') }}</th>
                        <th class="text-end">
                            {{ "Rp. ".number_format($royaltyRecords->sum('0.total_royalty')) }}
                        </th>
                        <th class="text-end">
                            {{ "Rp. ".number_format($royaltyRecords->sum('1.total_royalty')) }}
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
                {{ __('COMPARING OF STUDENT') }}
            </h2>

            <x-print.sub-header-compare
                :is-branch-filtered="$isBranchFiltered"
                :is-region-filtered="$isRegionFiltered"
                :first-branch-name="$firstBranchName"
                :second-branch-name="$secondBranchName"
                :first-region-name="$firstRegionName"
                :second-region-name="$secondRegionName"
                :start-period="$startPeriod"
                :end-period="$endPeriod"
                />
        </div>

        <x-print.compare.student-record-chart
            :records="$studentRecords"
            :is-monthly="$isMonthly"
            />

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th scope="col" rowspan="2">{{ __('Period') }}</th>
                        <th scope="col" colspan="2">{{ __('Active student') }}</th>
                        <th scope="col" colspan="2">{{ __('New student') }}</th>
                        <th scope="col" colspan="2">{{ __('Inactive student') }}</th>
                        <th scope="col" colspan="2">{{ __('Leave student') }}</th>
                    </tr>
                    <tr>
                        <th>{{ $firstBranchName ?? $firstRegionName }}</th>
                        <th>{{ $secondBranchName ?? $secondRegionName }}</th>
                        <th>{{ $firstBranchName ?? $firstRegionName }}</th>
                        <th>{{ $secondBranchName ?? $secondRegionName }}</th>
                        <th>{{ $firstBranchName ?? $firstRegionName }}</th>
                        <th>{{ $secondBranchName ?? $secondRegionName }}</th>
                        <th>{{ $firstBranchName ?? $firstRegionName }}</th>
                        <th>{{ $secondBranchName ?? $secondRegionName }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($studentRecords as $record)
                    <tr>
                        <td>
                            @if ($isMonthly)
                                {{ \App\Enums\Month::name($record[0]['month']) . " {$record[0]['year']}" }}
                            @else
                                {{ $record[0]['year'] }}
                            @endif
                        </td>
                        <td class="text-end">{{ number_format($record[0]['total_active_student']) }}</td>
                        <td class="text-end">{{ number_format($record[1]['total_active_student']) }}</td>
                        <td class="text-end">{{ number_format($record[0]['total_new_student']) }}</td>
                        <td class="text-end">{{ number_format($record[1]['total_new_student']) }}</td>
                        <td class="text-end">{{ number_format($record[0]['total_inactive_student']) }}</td>
                        <td class="text-end">{{ number_format($record[1]['total_inactive_student']) }}</td>
                        <td class="text-end">{{ number_format($record[0]['total_leave_student']) }}</td>
                        <td class="text-end">{{ number_format($record[1]['total_leave_student']) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>{{ __('Total') }}</th>
                        <th class="text-end">{{ number_format($studentRecords->sum('0.total_active_student')) }}</th>
                        <th class="text-end">{{ number_format($studentRecords->sum('1.total_active_student')) }}</th>
                        <th class="text-end">{{ number_format($studentRecords->sum('0.total_new_student')) }}</th>
                        <th class="text-end">{{ number_format($studentRecords->sum('1.total_new_student')) }}</th>
                        <th class="text-end">{{ number_format($studentRecords->sum('0.total_inactive_student')) }}</th>
                        <th class="text-end">{{ number_format($studentRecords->sum('1.total_inactive_student')) }}</th>
                        <th class="text-end">{{ number_format($studentRecords->sum('0.total_leave_student')) }}</th>
                        <th class="text-end">{{ number_format($studentRecords->sum('1.total_leave_student')) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="container py-5 bg-white">
        <div class="text-center mb-4">
            <h2 class="text-center">
                {{ __('COMPARING OF ACTIVE STUDENT BASED ON LESSON') }}
            </h2>

            <x-print.sub-header-compare
                :is-branch-filtered="$isBranchFiltered"
                :is-region-filtered="$isRegionFiltered"
                :first-branch-name="$firstBranchName"
                :second-branch-name="$secondBranchName"
                :first-region-name="$firstRegionName"
                :second-region-name="$secondRegionName"
                :start-period="$startPeriod"
                :end-period="$endPeriod"
                />
        </div>

        <x-print.compare.student-lesson-record-chart
            :records="$studentLessonRecords"
            :lessons="$lessons"
            :is-monthly="$isMonthly"
            />

        @foreach ($lessons->chunk(5) as $lessonChunks)
            @php
                $from = $loop->index * 5;
                $until = $from + 5;
            @endphp
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" rowspan="2">{{ __('Period') }}</th>
                            @foreach ($lessonChunks as $lesson)
                                <th scope="col" colspan="2">{{ $lesson->name }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($lessonChunks as $lesson)
                                <th>{{ $firstBranchName ?? $firstRegionName }}</th>
                                <th>{{ $secondBranchName ?? $secondRegionName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentLessonRecords as $record)
                        <tr>
                            <td>
                                @if ($isMonthly)
                                    {{ \App\Enums\Month::name($record[0]['month']) . " {$record[0]['year']}" }}
                                @else
                                    {{ $record[0]['year'] }}
                                @endif
                            </td>

                            @foreach ($lessonChunks as $lesson)
                                <td class="text-end">
                                    {{ number_format(collect($record[0]['details'])->firstWhere('lesson_id', $lesson->id)['total'] ?? 0) }}
                                </td>
                                <td class="text-end">
                                    {{ number_format(collect($record[1]['details'])->firstWhere('lesson_id', $lesson->id)['total'] ?? 0) }}
                                </td>
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
                {{ __('COMPARING OF ACTIVE STUDENT BASED ON EDUCATION') }}
            </h2>

            <x-print.sub-header-compare
                :is-branch-filtered="$isBranchFiltered"
                :is-region-filtered="$isRegionFiltered"
                :first-branch-name="$firstBranchName"
                :second-branch-name="$secondBranchName"
                :first-region-name="$firstRegionName"
                :second-region-name="$secondRegionName"
                :start-period="$startPeriod"
                :end-period="$endPeriod"
                />
        </div>

        <x-print.compare.student-education-record-chart
            :records="$studentEducationRecords"
            :educations="$educations"
            :is-monthly="$isMonthly"
            />

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th scope="col" rowspan="2">{{ __('Period') }}</th>
                        @foreach ($educations as $education)
                            <th scope="col" colspan="2">{{ $education->name }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($educations as $education)
                            <th>{{ $firstBranchName ?? $firstRegionName }}</th>
                            <th>{{ $secondBranchName ?? $secondRegionName }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($studentEducationRecords as $record)
                    <tr>
                        <td>
                            @if ($isMonthly)
                                {{ \App\Enums\Month::name($record[0]['month']) . " {$record[0]['year']}" }}
                            @else
                                {{ $record[0]['year'] }}
                            @endif
                        </td>

                        @foreach ($educations as $education)
                            <td class="text-end">
                                {{ number_format(collect($record[0]['details'])->firstWhere('education_id', $education->id)['total'] ?? 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(collect($record[1]['details'])->firstWhere('education_id', $education->id)['total'] ?? 0) }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection