<?php

namespace App\Filament\Resources\ImportedLeaveStudentResource\Pages;

use App\Filament\Resources\ImportedLeaveStudentResource;
use App\Services\ImportService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateImportedLeaveStudent extends CreateRecord
{
    protected static string $resource = ImportedLeaveStudentResource::class;

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

        $isImportedLeaveStudentExists = app(ImportService::class)->isImportedLeaveStudentExists(
            $month,
            $year,
            $branchId,
        );

        if ($isImportedLeaveStudentExists) {
            return [
                $this->getCancelFormAction()
            ];
        }

        return parent::getFormActions();
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->visible(fn () => !$this->isImportedLeaveStudentExists());
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->visible(fn () => !$this->isImportedLeaveStudentExists());
    }

    private function isImportedLeaveStudentExists(): bool
    {
        $data = $this->data;

        $month = $data['month'];
        $year = $data['year'];
        $branchId = $data['branch_id'];

        $isImportedLeaveStudentExists = app(ImportService::class)->isImportedLeaveStudentExists(
            $month,
            $year,
            $branchId,
        );

        if (
            $isImportedLeaveStudentExists
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

        return $isImportedLeaveStudentExists;
    }
}
