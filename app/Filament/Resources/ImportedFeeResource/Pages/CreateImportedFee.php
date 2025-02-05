<?php

namespace App\Filament\Resources\ImportedFeeResource\Pages;

use App\Filament\Resources\ImportedFeeResource;
use App\Services\ImportService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateImportedFee extends CreateRecord
{
    protected static string $resource = ImportedFeeResource::class;

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

        $isImportedFeeExists = app(ImportService::class)->isImportedFeeIsExists(
            $month,
            $year,
            $branchId,
        );

        if ($isImportedFeeExists) {
            return [
                $this->getCancelFormAction()
            ];
        }

        return parent::getFormActions();
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->visible(fn () => !$this->isImportedFeeExists());
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->visible(fn () => !$this->isImportedFeeExists());
    }

    private function isImportedFeeExists(): bool
    {
        $data = $this->data;

        $month = $data['month'];
        $year = $data['year'];
        $branchId = $data['branch_id'];

        $isImportedFeeExists = app(ImportService::class)->isImportedFeeIsExists(
            $month,
            $year,
            $branchId,
        );

        if (
            $isImportedFeeExists
            && !$this->isNotificationSended
        ) {
            Notification::make()
                ->title(__('This imported data already exists!'))
                ->body(__('Please use a different branch, month, or year.'))
                ->danger()
                ->duration(5000)
                ->send();

            $this->isNotificationSended = true;
        }

        return $isImportedFeeExists;
    }
}
