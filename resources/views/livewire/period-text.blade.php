<h2>
    @if ($isSingle)
        {{ __('PERIOD: :period', ['period' => $this->getPeriod()]) }}
    @else
        {{ __('PERIOD: :firstPeriod - :secondPeriod', ['firstPeriod' => $this->getFirstPeriod(), 'secondPeriod' => $this->getSecondPeriod()]) }}
    @endif
</h2>
