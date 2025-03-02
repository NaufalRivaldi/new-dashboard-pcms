<div>
    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <table class="w-full border-collapse">
            <!-- Table Head -->
            <thead class="bg-gray-50 text-left text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-white">
                <tr>
                    <th class="px-4 py-3 border-b dark:border-gray-700">{{ __('Period') }}</th>
                    @foreach ($educations as $education)
                        <th class="px-4 py-3 border-b dark:border-gray-700">{{ $education->name }}</th>
                    @endforeach
                </tr>
            </thead>

            <!-- Table Body -->
            <tbody class="text-sm text-gray-900">
                @foreach ($records as $record)
                    <tr>
                        <td class="px-4 py-3">
                            {{ \App\Enums\Month::name($record['month']) . " {$record['year']}" }}
                        </td>
                        @foreach ($record['details'] as $detail)
                            <td class="px-4 py-3">
                                {{ number_format($detail['total'] ?? 0) }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>