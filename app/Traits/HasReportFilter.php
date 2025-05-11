<?php

namespace App\Traits;

use App\Models\Branch;
use App\Models\Region;

trait HasReportFilter
{
    public function getBranchName(?int $branchId = null): ?string
    {
        if (!is_null($branchId)) {
            return Branch::find($branchId)->name ?? '-';
        }

        return $this->defaultBranchName();
    }

    protected function defaultBranchName(): ?string
    {
        return null;
    }

    public function getRegionName(?int $regionId = null): ?string
    {
        if (!is_null($regionId)) {
            return Region::find($regionId)->name ?? '-';
        }

        return $this->defaultRegionName();
    }

    protected function defaultRegionName(): ?string
    {
        return null;
    }

    public function isBranchFiltered(): bool
    {
        return (
            isset($this->filters['branch_id'])
            && !is_null($this->filters['branch_id'])
            && $this->filters['branch_id'] != ''
        );
    }

    public function isRegionFiltered(): bool
    {
        return isset($this->filters['region_id'])
            && !is_null($this->filters['region_id'])
            && $this->filters['region_id'] != '';
    }

    public function isComparedBranchFiltered(): bool
    {
        return (
            isset($this->filters['first_branch_id'])
            && isset($this->filters['second_branch_id'])
        );
    }

    public function isComparedRegionFiltered(): bool
    {
        return (
            isset($this->filters['first_region_id'])
            && isset($this->filters['second_region_id'])
        );
    }
}