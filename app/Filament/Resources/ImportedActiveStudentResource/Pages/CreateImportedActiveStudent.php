<?php

namespace App\Filament\Resources\ImportedActiveStudentResource\Pages;

use App\Filament\Resources\ImportedActiveStudentResource;
use App\Models\ImportedActiveStudent;
use App\Services\ImportService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateImportedActiveStudent extends CreateRecord
{
    protected static string $resource = ImportedActiveStudentResource::class;

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

        $isImportedActiveStudentExists = app(ImportService::class)->isImportedDataExists(
            ImportedActiveStudent::class,
            $month,
            $year,
            $branchId,
        );

        if ($isImportedActiveStudentExists) {
            return [
                $this->getCancelFormAction()
            ];
        }

        return parent::getFormActions();
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->visible(fn () => !$this->isImportedActiveStudentExists());
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->visible(fn () => !$this->isImportedActiveStudentExists());
    }

    private function isImportedActiveStudentExists(): bool
    {
        $importService = app(ImportService::class);

        $data = $this->data;

        $month = $data['month'];
        $year = $data['year'];
        $branchId = $data['branch_id'];

        $isImportedActiveStudentExists = $importService->isImportedDataExists(
            ImportedActiveStudent::class,
            $month,
            $year,
            $branchId,
        );

        if (
            $isImportedActiveStudentExists
            && !$this->isNotificationSended
        ) {
            $importService->sendNotificationDataExists();

            $this->isNotificationSended = true;
        }

        return $isImportedActiveStudentExists;
    }
}
