<?php

namespace App\Imports;

use App\Enums\PaymentType;
use App\Models\Branch;
use App\Models\ImportedFee;
use App\Models\ImportedFeeDetail;
use App\Services\ImportService;
use App\Services\NotificationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class FeeImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        if ($collection->isNotEmpty()) {
            $importService = app(ImportService::class);

            $branchCode = $this->getBranchCode($collection[0][0]);

            $branch = Branch::select([
                    'id',
                    'code',
                    'name',
                ])
                ->firstWhere('code', $branchCode);

            $dateArray = $this->getYearAndMonth($collection[0][0]);

            if ($branch) {
                if (!$importService->isImportedFeeExists($dateArray['month'], $dateArray['year'], $branch->id)) {
                    DB::beginTransaction();

                    try {
                        $importedFeeData = [
                            'month' => $dateArray['month'],
                            'year' => $dateArray['year'],
                            'total' => $collection->sum(10),
                            'branch_id' => $branch->id,
                            'user_id' => auth()->user()->id,
                        ];

                        $importedFee = ImportedFee::create($importedFeeData);

                        $importedFeeDetailData = $collection
                            ->map(function ($data) use ($importedFee) {
                                return [
                                    'type' => $data[4] == 1
                                        ? PaymentType::Register->value
                                        : PaymentType::Course->value,
                                    'payer_name' => $data[13],
                                    'nominal' => $data[10],
                                    'lesson_id' => null,
                                    'imported_fee_id' => $importedFee['id'],
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            })
                            ->all();

                        ImportedFeeDetail::insert($importedFeeDetailData);

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
                        body: __('The imported data already exists in :resource. Remove the old one to replace it.', [
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

    private function getBranchCode(string $value): string
    {
        $rawCode = explode('/', $value)[0] ?? "";

        return explode('-', $rawCode)[0] ?? "";
    }

    private function getYearAndMonth(string $value): array
    {
        $explodeValue = explode('/', $value);

        return [
            'year' => "20".$explodeValue[1],
            'month' => $explodeValue[2],
        ];
    }
}
