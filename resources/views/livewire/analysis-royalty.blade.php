<div>
    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <table class="w-full border-collapse">
            <!-- Table Head -->
            <thead class="bg-gray-50 text-left text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-white">
                <tr>
                    <th class="px-4 py-3 border-b dark:border-gray-700">{{ __('Period') }}</th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">{{ __('Royalty') }}</th>
                </tr>
            </thead>

            <!-- Table Body -->
            <tbody class="text-sm text-gray-900">
                @foreach ($records as $record)
                    <tr>
                        <td class="px-4 py-3">
                            @if ($isMonthly)
                                {{ \App\Enums\Month::name($record['month']) . " {$record['year']}" }}
                            @else
                                {{ $record['year'] }}
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ "Rp. ".number_format($record['total_royalty']) }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot class="bg-gray-50 text-left text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-white">
                <tr>
                    <th class="px-4 py-3 border-b dark:border-gray-700">{{ __('Total') }}</th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">
                        {{ "Rp. ".number_format($records->sum('total_royalty')) }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>