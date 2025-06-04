@aware([
    'isBranchFiltered' => false,
    'isRegionFiltered' => false,
    'branchName' => null,
    'regionName' => null,
    'startPeriod' => null,
    'endPeriod' => null,
])

<div>
    @if ($isBranchFiltered || !$isRegionFiltered)
        <h3>{{ __('BRANCH: :branch', ['branch' => $branchName ?? __('All')]) }}</h3>
    @endif

    @if ($isRegionFiltered)
        <h3>{{ __('REGION: :region', ['region' => $regionName]) }}</h3>
    @endif

    <p>
        <strong>
            {{ __('Period: :startPeriod ~ :endPeriod', ['startPeriod' => $startPeriod, 'endPeriod' => $endPeriod]) }}
        </strong>
    </p>
</div>