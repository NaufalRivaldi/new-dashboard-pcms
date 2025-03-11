<div>
    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <table class="w-full border-collapse">
            <!-- Table Head -->
            <thead class="bg-gray-50 text-left text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-white">
                <tr>
                    <th class="px-4 py-3 border-b dark:border-gray-700">
                        {{ __('Branch') }}
                    </th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">
                        {{ __('Registration fee') }}
                    </th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">
                        {{ __('Course fee') }}
                    </th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">
                        {{ __('Total fee') }}
                    </th>
                </tr>
            </thead>

            <!-- Table Body -->
            <tbody class="text-sm text-gray-900">
                @foreach ($records as $record)
                    <tr>
                        <td class="px-4 py-3">
                            {{ $this->getBranchName($record['branch_id']) }}
                        </td>
                        <td class="px-4 py-3">{{ "Rp. ".number_format($record['total_registration_fee']) }}</td>
                        <td class="px-4 py-3">{{ "Rp. ".number_format($record['total_course_fee']) }}</td>
                        <td class="px-4 py-3">{{ "Rp. ".number_format($record['total_total_fee']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>