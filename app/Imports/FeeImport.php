<?php

namespace App\Imports;

use App\Enums\PaymentType;
use App\Models\Branch;
use App\Models\ImportedFee;
use App\Models\ImportedFeeDetail;
use Illuminate\Support\Collection;

class FeeImport extends BaseImport
{
    protected $model = ImportedFee::class;

    protected function process(
        Collection $collection,
        Branch $branch,
        array $customData,
    ): void {
        $importedFeeData = [
            'month' => $customData['month'],
            'year' => $customData['year'],
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
    }
}
