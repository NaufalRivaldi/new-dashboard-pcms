<x-filament-panels::page>
    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    <center>
        <h1 class="font-bold">
            {{ __('COMPARING OF RECEIPT REPORT') }}
        </h1>
        @if ($this->isComparedBranchFiltered())
            <h2>{{ __('BRANCH: :firstBranch and :secondBranch', ['firstBranch' => $this->getBranchName($filters['first_branch_id']), 'secondBranch' => $this->getBranchName($filters['second_branch_id'])]) }}</h2>
        @endif

        @if ($this->isComparedRegionFiltered())
            <h2>{{ __('REGION: :firstRegion and :secondRegion', ['firstRegion' => $this->getRegionName($filters['first_region_id']), 'secondRegion' => $this->getRegionName($filters['second_region_id'])]) }}</h2>
        @endif

        <livewire:period-text :filters="$filters" />
    </center>

    @livewire(\App\Livewire\Chart\CompareFeeChart::class)

    <livewire:compare-fee />

    <hr>

    <center>
        <h1 class="font-bold">
            {{ __('COMPARING OF TOTAL ROYALTY') }}
        </h1>
        @if ($this->isComparedBranchFiltered())
            <h2>{{ __('BRANCH: :firstBranch and :secondBranch', ['firstBranch' => $this->getBranchName($filters['first_branch_id']), 'secondBranch' => $this->getBranchName($filters['second_branch_id'])]) }}</h2>
        @endif

        @if ($this->isComparedRegionFiltered())
            <h2>{{ __('REGION: :firstRegion and :secondRegion', ['firstRegion' => $this->getRegionName($filters['first_region_id']), 'secondRegion' => $this->getRegionName($filters['second_region_id'])]) }}</h2>
        @endif

        <livewire:period-text :filters="$filters" />
    </center>

    @livewire(\App\Livewire\Chart\CompareRoyaltyChart::class)

    <livewire:compare-royalty />

    <hr>

    <center>
        <h1 class="font-bold">
            {{ __('COMPARING OF STUDENT') }}
        </h1>
         @if ($this->isComparedBranchFiltered())
            <h2>{{ __('BRANCH: :firstBranch and :secondBranch', ['firstBranch' => $this->getBranchName($filters['first_branch_id']), 'secondBranch' => $this->getBranchName($filters['second_branch_id'])]) }}</h2>
        @endif

        @if ($this->isComparedRegionFiltered())
            <h2>{{ __('REGION: :firstRegion and :secondRegion', ['firstRegion' => $this->getRegionName($filters['first_region_id']), 'secondRegion' => $this->getRegionName($filters['second_region_id'])]) }}</h2>
        @endif

        <livewire:period-text :filters="$filters" />
    </center>

    @livewire(\App\Livewire\Chart\CompareStudentChart::class)

    <livewire:compare-student />

    <hr>

    <center>
        <h1 class="font-bold">
            {{ __('COMPARING OF ACTIVE STUDENT BASED ON LESSON') }}
        </h1>
         @if ($this->isComparedBranchFiltered())
            <h2>{{ __('BRANCH: :firstBranch and :secondBranch', ['firstBranch' => $this->getBranchName($filters['first_branch_id']), 'secondBranch' => $this->getBranchName($filters['second_branch_id'])]) }}</h2>
        @endif

        @if ($this->isComparedRegionFiltered())
            <h2>{{ __('REGION: :firstRegion and :secondRegion', ['firstRegion' => $this->getRegionName($filters['first_region_id']), 'secondRegion' => $this->getRegionName($filters['second_region_id'])]) }}</h2>
        @endif

        <livewire:period-text :filters="$filters" />
    </center>

    @livewire(\App\Livewire\Chart\CompareActiveStudentLessonChart::class)

    <livewire:compare-active-student-lesson />

    <hr>

    <center>
        <h1 class="font-bold">
            {{ __('COMPARING OF ACTIVE STUDENT BASED ON EDUCATION') }}
        </h1>
         @if ($this->isComparedBranchFiltered())
            <h2>{{ __('BRANCH: :firstBranch and :secondBranch', ['firstBranch' => $this->getBranchName($filters['first_branch_id']), 'secondBranch' => $this->getBranchName($filters['second_branch_id'])]) }}</h2>
        @endif

        @if ($this->isComparedRegionFiltered())
            <h2>{{ __('REGION: :firstRegion and :secondRegion', ['firstRegion' => $this->getRegionName($filters['first_region_id']), 'secondRegion' => $this->getRegionName($filters['second_region_id'])]) }}</h2>
        @endif

        <livewire:period-text :filters="$filters" />
    </center>

    @livewire(\App\Livewire\Chart\CompareActiveStudentEducationChart::class)

    <livewire:compare-active-student-education />

</x-filament-panels::page>
