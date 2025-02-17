<?php

namespace App\Filament\Resources\SummaryResource\Pages;

use App\Filament\Resources\SummaryResource;
use App\Models\Summary;
use App\Services\ImportService;
use App\Services\NotificationService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateSummary extends CreateRecord
{
    protected static string $resource = SummaryResource::class;

    private $isNotificationSended = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getFormActions(): array
    {
        $data = $this->data;

        $month = $data['month'];
        $year = $data['year'];
        $branchId = $data['branch_id'];

        $isSummaryExists = app(ImportService::class)->isImportedDataExists(
            Summary::class,
            $month,
            $year,
            $branchId,
        );

        if ($isSummaryExists) {
            return [
                $this->getCancelFormAction()
            ];
        }

        return parent::getFormActions();
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->visible(fn () => !$this->isSummaryExists());
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->visible(fn () => !$this->isSummaryExists());
    }

    private function isSummaryExists(): bool
    {
        $data = $this->data;

        $month = $data['month'];
        $year = $data['year'];
        $branchId = $data['branch_id'];

        $isSummaryExists = app(ImportService::class)->isImportedDataExists(
            Summary::class,
            $month,
            $year,
            $branchId,
        );

        if (
            $isSummaryExists
            && !$this->isNotificationSended
        ) {
            NotificationService::danger(
                title: __('This summary data is already exists!'),
                body: __('Please use a different branch, month, or year.'),
            );

            $this->isNotificationSended = true;
        }

        return $isSummaryExists;
    }
}
