<?php

namespace App\Filament\Resources\ImportedActiveStudentEducationResource\Pages;

use App\Filament\Resources\ImportedActiveStudentEducationResource;
use App\Services\ImportService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateImportedActiveStudentEducation extends CreateRecord
{
    protected static string $resource = ImportedActiveStudentEducationResource::class;

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

        $isImportedActiveStudentEducationExists = app(ImportService::class)->isImportedActiveStudentEducationExists(
            $month,
            $year,
            $branchId,
        );

        if ($isImportedActiveStudentEducationExists) {
            return [
                $this->getCancelFormAction()
            ];
        }

        return parent::getFormActions();
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->visible(fn () => !$this->isImportedActiveStudentEducationExists());
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->visible(fn () => !$this->isImportedActiveStudentEducationExists());
    }

    private function isImportedActiveStudentEducationExists(): bool
    {
        $data = $this->data;

        $month = $data['month'];
        $year = $data['year'];
        $branchId = $data['branch_id'];

        $isImportedActiveStudentEducationExists = app(ImportService::class)->isImportedActiveStudentEducationExists(
            $month,
            $year,
            $branchId,
        );

        if (
            $isImportedActiveStudentEducationExists
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

        return $isImportedActiveStudentEducationExists;
    }
}
