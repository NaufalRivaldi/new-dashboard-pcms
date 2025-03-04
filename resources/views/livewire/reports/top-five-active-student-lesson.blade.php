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
                        {{ __('Branch') }}
                    </th>
                    @foreach ($lessons as $lesson)
                        <th class="px-4 py-3 border-b dark:border-gray-700">
                            {{ $lesson->name }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <!-- Table Body -->
            <tbody class="text-sm text-gray-900">
                @foreach ($records as $record)
                    <tr>
                        <td class="px-4 py-3">
                            {{ $this->getBranchName($record['branch_id']) }}
                        </td>
                        @foreach ($lessons as $lesson)
                            <td class="px-4 py-3">
                                {{ $this->getTotalValue($record['details'], $lesson->id) }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>