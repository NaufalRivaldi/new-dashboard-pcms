<x-filament-panels::page>
    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    <center>
        <h1 class="font-bold">
            {{ __('COMPARING OF RECEIPT REPORT') }}
        </h1>
        <h2>{{ __('BRANCH: :firstBranch and :secondBranch', ['firstBranch' => $this->getBranchName($filters['first_branch_id']), 'secondBranch' => $this->getBranchName($filters['second_branch_id'])]) }}</h2>
        <h2>{{ __('PERIOD: :firstPeriod - :secondPeriod', ['firstPeriod' => $this->getFormattedPeriod($filters['start_period']), 'secondPeriod' => $this->getFormattedPeriod($filters['end_period'])]) }}</h2>
    </center>

    @livewire(\App\Livewire\Chart\CompareFeeChart::class)

    <livewire:compare-fee />

    <center>
        <h1 class="font-bold">
            {{ __('COMPARING OF TOTAL ROYALTY') }}
        </h1>
        <h2>{{ __('BRANCH: :firstBranch and :secondBranch', ['firstBranch' => $this->getBranchName($filters['first_branch_id']), 'secondBranch' => $this->getBranchName($filters['second_branch_id'])]) }}</h2>
        <h2>{{ __('PERIOD: :firstPeriod - :secondPeriod', ['firstPeriod' => $this->getFormattedPeriod($filters['start_period']), 'secondPeriod' => $this->getFormattedPeriod($filters['end_period'])]) }}</h2>
    </center>

    @livewire(\App\Livewire\Chart\CompareRoyaltyChart::class)

    <livewire:compare-royalty />

    <center>
        <h1 class="font-bold">
            {{ __('COMPARING OF STUDENT') }}
        </h1>
        <h2>{{ __('BRANCH: :firstBranch and :secondBranch', ['firstBranch' => $this->getBranchName($filters['first_branch_id']), 'secondBranch' => $this->getBranchName($filters['second_branch_id'])]) }}</h2>
        <h2>{{ __('PERIOD: :firstPeriod - :secondPeriod', ['firstPeriod' => $this->getFormattedPeriod($filters['start_period']), 'secondPeriod' => $this->getFormattedPeriod($filters['end_period'])]) }}</h2>
    </center>

    @livewire(\App\Livewire\Chart\CompareStudentChart::class)

    <livewire:compare-student />

    <center>
        <h1 class="font-bold">
            {{ __('COMPARING OF ACTIVE STUDENT BASED ON LESSON') }}
        </h1>
        <h2>{{ __('BRANCH: :firstBranch and :secondBranch', ['firstBranch' => $this->getBranchName($filters['first_branch_id']), 'secondBranch' => $this->getBranchName($filters['second_branch_id'])]) }}</h2>
        <h2>{{ __('PERIOD: :firstPeriod - :secondPeriod', ['firstPeriod' => $this->getFormattedPeriod($filters['start_period']), 'secondPeriod' => $this->getFormattedPeriod($filters['end_period'])]) }}</h2>
    </center>

    @livewire(\App\Livewire\Chart\CompareActiveStudentLessonChart::class)

    <livewire:compare-active-student-lesson />

    <center>
        <h1 class="font-bold">
            {{ __('COMPARING OF ACTIVE STUDENT BASED ON EDUCATION') }}
        </h1>
        <h2>{{ __('BRANCH: :firstBranch and :secondBranch', ['firstBranch' => $this->getBranchName($filters['first_branch_id']), 'secondBranch' => $this->getBranchName($filters['second_branch_id'])]) }}</h2>
        <h2>{{ __('PERIOD: :firstPeriod - :secondPeriod', ['firstPeriod' => $this->getFormattedPeriod($filters['start_period']), 'secondPeriod' => $this->getFormattedPeriod($filters['end_period'])]) }}</h2>
    </center>

    @livewire(\App\Livewire\Chart\CompareActiveStudentEducationChart::class)

    <livewire:compare-active-student-education />
</x-filament-panels::page>
