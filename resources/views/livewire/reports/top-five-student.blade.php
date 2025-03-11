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
                        {{ __('Active student') }}
                    </th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">
                        {{ __('New student') }}
                    </th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">
                        {{ __('Inactive student') }}
                    </th>
                    <th class="px-4 py-3 border-b dark:border-gray-700">
                        {{ __('Leave student') }}
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
                        <td class="px-4 py-3">{{ number_format($record['total_active_student']) }}</td>
                        <td class="px-4 py-3">{{ number_format($record['total_new_student']) }}</td>
                        <td class="px-4 py-3">{{ number_format($record['total_inactive_student']) }}</td>
                        <td class="px-4 py-3">{{ number_format($record['total_leave_student']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>