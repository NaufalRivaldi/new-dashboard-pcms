<?php

namespace App\Livewire;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class PeriodText extends Component
{
    #[Reactive]
    public array $filters = [];

    public bool $isSingle = false;

    public function getFirstPeriod(): ?string
    {
        if ($this->isMonthly()) {
            return getFormattedPeriod($this->filters['start_period']);
        }

        return $this->filters['start_year'];
    }

    public function getSecondPeriod()
    {
        if ($this->isMonthly()) {
            return getFormattedPeriod($this->filters['end_period']);
        }

        return $this->filters['end_year'];
    }

    public function getPeriod(): ?string
    {
        if ($this->isSingleMonthly()) {
            return getFormattedPeriod($this->filters['period']);
        }

        return $this->filters['year'];
    }

    private function isMonthly(): bool
    {
        return (
            isset($this->filters['start_period'])
            || isset($this->filters['end_period'])
        );
    }

    private function isSingleMonthly(): bool
    {
        return isset($this->filters['period']);
    }

    public function render()
    {
        return view('livewire.period-text');
    }
}
