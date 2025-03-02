<x-filament-panels::page>
    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    <center>
        <h1 class="font-bold">
            {{ __('SUMMARY OF RECEIPT REPORT') }}
        </h1>
        <h2>{{ __('BRANCH: :branch', ['branch' => $this->getBranchName($filters['branch_id'])]) }}</h2>
        <h2>{{ __('PERIOD: :firstPeriod - :secondPeriod', ['firstPeriod' => $this->getFormattedPeriod($filters['start_period']), 'secondPeriod' => $this->getFormattedPeriod($filters['end_period'])]) }}</h2>
    </center>

    @livewire(\App\Livewire\Chart\AnalysisFee::class)

    <livewire:analysis-fee />

    <hr>

    <center>
        <h1 class="font-bold">
            {{ __('SUMMARY OF TOTAL ROYALTY') }}
        </h1>
        <h2>{{ __('BRANCH: :branch', ['branch' => $this->getBranchName($filters['branch_id'])]) }}</h2>
        <h2>{{ __('PERIOD: :firstPeriod - :secondPeriod', ['firstPeriod' => $this->getFormattedPeriod($filters['start_period']), 'secondPeriod' => $this->getFormattedPeriod($filters['end_period'])]) }}</h2>
    </center>

    @livewire(\App\Livewire\Chart\AnalysisRoyalty::class)

    <livewire:analysis-royalty />

    <hr>

    <center>
        <h1 class="font-bold">
            {{ __('SUMMARY OF STUDENT') }}
        </h1>
        <h2>{{ __('BRANCH: :branch', ['branch' => $this->getBranchName($filters['branch_id'])]) }}</h2>
        <h2>{{ __('PERIOD: :firstPeriod - :secondPeriod', ['firstPeriod' => $this->getFormattedPeriod($filters['start_period']), 'secondPeriod' => $this->getFormattedPeriod($filters['end_period'])]) }}</h2>
    </center>

    @livewire(\App\Livewire\Chart\AnalysisStudent::class)

    <livewire:analysis-student />

    <hr>

    <center>
        <h1 class="font-bold">
            {{ __('SUMMARY OF ACTIVE STUDENT BASED ON LESSON') }}
        </h1>
        <h2>{{ __('BRANCH: :branch', ['branch' => $this->getBranchName($filters['branch_id'])]) }}</h2>
        <h2>{{ __('PERIOD: :firstPeriod - :secondPeriod', ['firstPeriod' => $this->getFormattedPeriod($filters['start_period']), 'secondPeriod' => $this->getFormattedPeriod($filters['end_period'])]) }}</h2>
    </center>

    @livewire(\App\Livewire\Chart\AnalysisActiveStudentLesson::class)

    <livewire:analysis-active-student-lesson />

    <hr>

    <center>
        <h1 class="font-bold">
            {{ __('SUMMARY OF ACTIVE STUDENT BASED ON EDUCATION') }}
        </h1>
        <h2>{{ __('BRANCH: :branch', ['branch' => $this->getBranchName($filters['branch_id'])]) }}</h2>
        <h2>{{ __('PERIOD: :firstPeriod - :secondPeriod', ['firstPeriod' => $this->getFormattedPeriod($filters['start_period']), 'secondPeriod' => $this->getFormattedPeriod($filters['end_period'])]) }}</h2>
    </center>

    @livewire(\App\Livewire\Chart\AnalysisActiveStudentEducation::class)

    <livewire:analysis-active-student-education />
</x-filament-panels::page>
