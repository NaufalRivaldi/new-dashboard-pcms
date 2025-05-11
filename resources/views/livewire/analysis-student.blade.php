<div>
    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <table class="w-full border-collapse">
            <!-- Table Head -->
            <thead class="bg-gray-50 text-left text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-white">
                <tr>
                    <th class="px-4 py-3 border-b dark:border-gray-700">{{ __('Period') }}</th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">{{ __('Active student') }}</th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">{{ __('New student') }}</th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">{{ __('Inactive student') }}</th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">{{ __('Leave student') }}</th>
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
                        <td class="px-4 py-3">{{ number_format($record['total_active_student']) }}</td>
                        <td class="px-4 py-3">{{ number_format($record['total_new_student']) }}</td>
                        <td class="px-4 py-3">{{ number_format($record['total_inactive_student']) }}</td>
                        <td class="px-4 py-3">{{ number_format($record['total_leave_student']) }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot class="bg-gray-50 text-left text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-white">
                <tr>
                    <th class="px-4 py-3 border-b dark:border-gray-700">{{ __('Total') }}</th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">
                        {{ number_format($records->sum('total_active_student')) }}
                    </th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">
                        {{ number_format($records->sum('total_new_student')) }}
                    </th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">
                        {{ number_format($records->sum('total_inactive_student')) }}
                    </th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">
                        {{ number_format($records->sum('total_leave_student')) }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>