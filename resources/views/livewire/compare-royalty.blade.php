<div>
    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <table class="w-full border-collapse">
            <!-- Table Head -->
            <thead class="bg-gray-50 text-left text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-white">
                <tr>
                    <th
                        class="px-4 py-3 border-b dark:border-gray-700"
                        rowspan="2"
                    >
                        {{ __('Period') }}
                    </th>
                    <th
                        class="px-4 py-3 border-b dark:border-gray-700"
                        colspan="2"
                    >
                        {{ __('Royalty') }}
                    </th>
                </tr>
                <tr>
                    <th class="px-4 py-3 border-b dark:border-gray-700">
                        {{ $firstBranchName ?? $firstRegionName }}
                    </th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">
                        {{ $secondBranchName ?? $secondRegionName }}
                    </th>
                </tr>
            </thead>

            <!-- Table Body -->
            <tbody class="text-sm text-gray-900">
                @foreach ($records as $record)
                    <tr>
                        <td class="px-4 py-3">
                            @if ($isMonthly)
                                {{ \App\Enums\Month::name($record[0]['month']) . " {$record[0]['year']}" }}
                            @else
                                {{ $record[0]['year'] }}
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ number_format($record[0]['total_royalty']) }}</td>
                        <td class="px-4 py-3">{{ number_format($record[1]['total_royalty']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>