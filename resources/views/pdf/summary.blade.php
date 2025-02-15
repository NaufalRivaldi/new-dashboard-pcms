<html>

<head>
    <title>Summary</title>

    <style>
        .print-c-col-4 {
            width: 50%;
            float: left;
        }

        .c-col-2 {
            width: 20% float: left;
        }

        .print-h1 {
            font-size: 2em;
            margin-bottom: 20px;
        }

        .print-hr {
            margin-bottom: 40px;
        }

        .print-text-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="print-container">
        <center>
            <h1 class="print-h1 print-text-bold">SUMMARY DATA</h1>
        </center>

        <table width="100%">
            <tr>
                <td>{{ __('Branch Code') }}:</td>
                <td>{{ $record->branch->code }}</td>
            </tr>
            <tr>
                <td>{{ __('Branch Name') }}:</td>
                <td>{{ $record->branch->name }}</td>
            </tr>
            <tr>
                <td>{{ __('Month') }}:</td>
                <td>{{ strtoupper(\App\Enums\Month::name($record->month)) }}</td>
            </tr>
            <tr>
                <td>{{ __('Year') }}:</td>
                <td>{{ $record->year }}</td>
            </tr>
        </table>

        <hr class="print-hr" style="opacity: 0">

        <table width="100%">
            <tr>
                <td>{{ __('Registration Fee Receipt') }}:</td>
                <td align="right">{{ 'Rp. '.number_format($record->registration_fee) }}</td>
            </tr>
            <tr>
                <td>{{ __('Course Fee Receipt') }}:</td>
                <td align="right">{{ 'Rp. '.number_format($record->course_fee) }}</td>
            </tr>
            <tr>
                <td>{{ __('Total Fee') }}:</td>
                <td align="right">{{ 'Rp. '.number_format($record->total_fee) }}</td>
            </tr>
            <tr>
                <td>{{ __('Royalty (10%)') }}:</td>
                <td align="right">{{ 'Rp. '.number_format($record->royalty) }}</td>
            </tr>
        </table>

        <hr class="print-hr" style="opacity: 0">

        <table width="100%">
            <tr>
                <td>{{ __('Total Active Student') }}:</td>
                <td align="right">{{ $record->active_student }}</td>
            </tr>
            <tr>
                <td>{{ __('Total New Student') }}:</td>
                <td align="right">{{ $record->new_student }}</td>
            </tr>
            <tr>
                <td>{{ __('Total Leave Student') }}:</td>
                <td align="right">{{ $record->leave_student }}</td>
            </tr>
            <tr>
                <td>{{ __('Total Inactive Student') }}:</td>
                <td align="right">{{ $record->inactive_student }}</td>
            </tr>
        </table>

        <hr class="print-hr" style="opacity: 0">

        <p class="print-text-bold">{{ __('Student Active Based On Lesson') }}:</p>
        <table width="100%">
            @foreach($record->summaryActiveStudentLesson as $activeStudent)
            <tr>
                <td>{{ $activeStudent->lesson->name }}</td>
                <td align="right">{{ $activeStudent->total }}</td>
            </tr>
            @endforeach
        </table>

        <hr class="print-hr" style="opacity: 0">

        <p class="print-text-bold">{{ __('Student Active Based On Education') }}:</p>
        <table width="100%">
            @foreach($record->summaryActiveStudentEducation as $activeStudent)
            <tr>
                <td>{{ $activeStudent->education->name }}</td>
                <td align="right">{{ $activeStudent->total }}</td>
            </tr>
            @endforeach
        </table>

        <hr class="print-hr" style="opacity: 0">

        <div class="row">
            <div class="print-c-col-4">
                <table width="100%">
                    <tr>
                        <td>Submitted:</td>
                        <td>{{ date('d F Y', strtotime($record->created_at)) }}</td>
                    </tr>
                    <tr>
                        <td>Submitted by</td>
                        <td>{{ $record->author->name }}</td>
                    </tr>
                </table>
            </div>
            <div class="c-col-2"></div>
            <div class="print-c-col-4">
                <table width="100%">
                    <tr>
                        <td>Approved:</td>
                        <td>{{ date('d F Y', strtotime($record->updated_at)) }}</td>
                    </tr>
                    <tr>
                        <td>Approved by</td>
                        <td>{{ $record->approver->name }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>