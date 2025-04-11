<x-filament-panels::page>
    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    <center>
        <h1 class="font-bold">
            {{ __(':type 5 SUMMARY OF RECEIPT', ['type' => str()->upper($filters['type'] ?? 'top')]) }}
        </h1>
        <h2>{{ __('PERIOD: :firstPeriod', ['firstPeriod' => $this->getFormattedPeriod($filters['period'])]) }}</h2>
    </center>

    @livewire(\App\Livewire\Chart\TopFiveFeeChart::class)

    <livewire:reports.top-five-fee />

    <hr>

    <center>
        <h1 class="font-bold">
            {{ __(':type 5 SUMMARY OF ROYALTY', ['type' => str()->upper($filters['type'] ?? 'top')]) }}
        </h1>
        <h2>{{ __('PERIOD: :firstPeriod', ['firstPeriod' => $this->getFormattedPeriod($filters['period'])]) }}</h2>
    </center>

    @livewire(\App\Livewire\Chart\TopFiveRoyaltyChart::class)

    <livewire:reports.top-five-royalty />

    <hr>

    <center>
        <h1 class="font-bold">
            {{ __(':type 5 SUMMARY OF STUDENT', ['type' => str()->upper($filters['type'] ?? 'top')]) }}
        </h1>
        <h2>{{ __('PERIOD: :firstPeriod', ['firstPeriod' => $this->getFormattedPeriod($filters['period'])]) }}</h2>
    </center>

    @livewire(\App\Livewire\Chart\TopFiveStudentChart::class)

    <livewire:reports.top-five-student />

    <hr>

    <center>
        <h1 class="font-bold">
            {{ __(':type 5 SUMMARY OF ACTIVE STUDENT BASED ON LESSON', ['type' => str()->upper($filters['type'] ?? 'top')]) }}
        </h1>
        <h2>{{ __('PERIOD: :firstPeriod', ['firstPeriod' => $this->getFormattedPeriod($filters['period'])]) }}</h2>
    </center>

    @livewire(\App\Livewire\Chart\TopFiveActiveStudentLessonChart::class)

    <livewire:reports.top-five-active-student-lesson />

    <hr>

    <center>
        <h1 class="font-bold">
            {{ __(':type 5 SUMMARY OF ACTIVE STUDENT BASED ON EDUCATION', ['type' => str()->upper($filters['type'] ?? 'top')]) }}
        </h1>
        <h2>{{ __('PERIOD: :firstPeriod', ['firstPeriod' => $this->getFormattedPeriod($filters['period'])]) }}</h2>
    </center>

    @livewire(\App\Livewire\Chart\TopFiveActiveStudentEducationChart::class)

    <livewire:reports.top-five-active-student-education />
</x-filament-panels::page>
