<?php

namespace App\Filament\Resources\ImportedActiveStudentEducationResource\Pages;

use App\Filament\Resources\ImportedActiveStudentEducationResource;
use App\Models\ImportedActiveStudentEducation;
use App\Services\ImportService;
use App\Services\NotificationService;
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

        $isImportedActiveStudentEducationExists = app(ImportService::class)->isImportedDataExists(
            ImportedActiveStudentEducation::class,
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
        $importService = app(ImportService::class);

        $data = $this->data;

        $month = $data['month'];
        $year = $data['year'];
        $branchId = $data['branch_id'];

        $isImportedActiveStudentEducationExists = $importService->isImportedDataExists(
            ImportedActiveStudentEducation::class,
            $month,
            $year,
            $branchId,
        );

        if (
            $isImportedActiveStudentEducationExists
            && !$this->isNotificationSended
        ) {
            $importService->sendNotificationDataExists();

            $this->isNotificationSended = true;
        }

        return $isImportedActiveStudentEducationExists;
    }
}
