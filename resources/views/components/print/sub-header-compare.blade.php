@aware([
    'isBranchFiltered' => false,
    'isRegionFiltered' => false,
    'firstBranchName' => null,
    'secondBranchName' => null,
    'firstRegionName' => null,
    'secondRegionName' => null,
    'startPeriod' => null,
    'endPeriod' => null,
])

<div>
    @if ($isBranchFiltered || !$isRegionFiltered)
        <h3>{{ __('BRANCH: :firstBranch & :secondBranch', ['firstBranch' => $firstBranchName ?? '-', 'secondBranch' => $secondBranchName ?? '-']) }}</h3>
    @endif

    @if ($isRegionFiltered)
        <h3>{{ __('REGION: :firstRegion & :secondRegion', ['firstRegion' => $firstRegionName, 'secondRegion' => $secondRegionName]) }}</h3>
    @endif

    <p>
        <strong>
            {{ __('Period: :startPeriod ~ :endPeriod', ['startPeriod' => $startPeriod, 'endPeriod' => $endPeriod]) }}
        </strong>
    </p>
</div>