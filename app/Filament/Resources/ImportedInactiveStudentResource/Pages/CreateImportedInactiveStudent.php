<?php

namespace App\Filament\Resources\ImportedInactiveStudentResource\Pages;

use App\Filament\Resources\ImportedInactiveStudentResource;
use App\Models\ImportedInactiveStudent;
use App\Services\ImportService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateImportedInactiveStudent extends CreateRecord
{
    protected static string $resource = ImportedInactiveStudentResource::class;

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

        $isImportedInactiveStudentExists = app(ImportService::class)->isImportedDataExists(
            ImportedInactiveStudent::class,
            $month,
            $year,
            $branchId,
        );

        if ($isImportedInactiveStudentExists) {
            return [
                $this->getCancelFormAction()
            ];
        }

        return parent::getFormActions();
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->visible(fn () => !$this->isImportedInactiveStudentExists());
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->visible(fn () => !$this->isImportedInactiveStudentExists());
    }

    private function isImportedInactiveStudentExists(): bool
    {
        $importService = app(ImportService::class);

        $data = $this->data;

        $month = $data['month'];
        $year = $data['year'];
        $branchId = $data['branch_id'];

        $isImportedInactiveStudentExists = $importService->isImportedDataExists(
            ImportedInactiveStudent::class,
            $month,
            $year,
            $branchId,
        );

        if (
            $isImportedInactiveStudentExists
            && !$this->isNotificationSended
        ) {
            $importService->sendNotificationDataExists();

            $this->isNotificationSended = true;
        }

        return $isImportedInactiveStudentExists;
    }
}
