<?php

namespace App\Imports;

use App\Models\Branch;
use App\Services\ImportService;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

abstract class BaseImport implements ToCollection
{
    protected array $customImportData = [];
    protected $model = Model::class;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        if ($collection->isNotEmpty()) {
            $customData = $this->customImportData;

            $importService = app(ImportService::class);

            $branch = Branch::select([
                    'id',
                    'code',
                    'name',
                ])
                ->firstWhere('id', $customData['branch_id']);

            if ($branch) {
                $dataIsExists = $importService->isImportedDataExists(
                    $this->model,
                    $customData['month'],
                    $customData['year'],
                    $branch->id,
                );

                if (!$dataIsExists) {
                    DB::beginTransaction();

                    try {
                        $this->process(
                            $collection,
                            $branch,
                            $customData
                        );

                        NotificationService::success(
                            title: __('Import data successfully!'),
                        );

                        DB::commit();
                    } catch (\Throwable $th) {
                        NotificationService::danger(
                            title: __('Error!'),
                            body: $th->getMessage(),
                        );

                        DB::rollBack();
                    }
                } else {

                    NotificationService::warning(
                        title: __('Oops!'),
                        body: __('The imported data already exists in this :resource. Remove the old one to replace it.', [
                            'resource' => __('Branch'),
                        ])
                    );

                }
            } else {

                NotificationService::warning(
                    title: __('Oops!'),
                    body: __('The :resource is not founded, please add a new one.', [
                        'resource' => __('Branch'),
                    ])
                );

            }
        } else {

            NotificationService::warning(
                title: __('Oops!'),
                body: __('Data on CSV file is empty!')
            );

        }
    }

    public function setCustomImportData(array $customImportData): void
    {
        $this->customImportData = $customImportData;
    }

    abstract protected function process(
        Collection $collection,
        Branch $branch,
        array $dateArray,
    ): void;
}