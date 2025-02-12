<?php

namespace App\Filament\Resources\ImportedNewStudentResource\Pages;

use App\Filament\Resources\ImportedNewStudentResource;
use App\Services\ImportService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateImportedNewStudent extends CreateRecord
{
    protected static string $resource = ImportedNewStudentResource::class;

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

        $isImportedNewStudentExists = app(ImportService::class)->isImportedNewStudentExists(
            $month,
            $year,
            $branchId,
        );

        if ($isImportedNewStudentExists) {
            return [
                $this->getCancelFormAction()
            ];
        }

        return parent::getFormActions();
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->visible(fn () => !$this->isImportedNewStudentExists());
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->visible(fn () => !$this->isImportedNewStudentExists());
    }

    private function isImportedNewStudentExists(): bool
    {
        $data = $this->data;

        $month = $data['month'];
        $year = $data['year'];
        $branchId = $data['branch_id'];

        $isImportedNewStudentExists = app(ImportService::class)->isImportedNewStudentExists(
            $month,
            $year,
            $branchId,
        );

        if (
            $isImportedNewStudentExists
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

        return $isImportedNewStudentExists;
    }
}
