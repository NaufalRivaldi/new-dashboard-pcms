<?php

namespace App\Filament\Resources\ImportedNewStudentResource\Pages;

use App\Filament\Resources\ImportedNewStudentResource;
use App\Models\ImportedNewStudent;
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

        $isImportedNewStudentExists = app(ImportService::class)->isImportedDataExists(
            ImportedNewStudent::class,
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
        $importService = app(ImportService::class);

        $data = $this->data;

        $month = $data['month'];
        $year = $data['year'];
        $branchId = $data['branch_id'];

        $isImportedNewStudentExists = $importService->isImportedDataExists(
            ImportedNewStudent::class,
            $month,
            $year,
            $branchId,
        );

        if (
            $isImportedNewStudentExists
            && !$this->isNotificationSended
        ) {
            $importService->sendNotificationDataExists();

            $this->isNotificationSended = true;
        }

        return $isImportedNewStudentExists;
    }
}
