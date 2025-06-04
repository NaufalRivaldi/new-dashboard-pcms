<div class="px-6 py-8 bg-white print:bg-white print:p-0 print:shadow-none">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 print:text-black">
            {{ __('List Of Branches That Have Not Imported Data') }}
        </h1>
        <p class="text-sm text-gray-600 mt-2 print:text-black">
            <strong>{{ __('Period: :period', ['period' => $period]) }}</strong>
        </p>
    </div>

    <div class="overflow-x-auto mt-4">
        <table class="w-full table-auto border border-gray-300 divide-y divide-gray-200 rounded-lg">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-3 text-sm font-semibold text-gray-700 uppercase tracking-wide">
                        {{ __('No') }}
                    </th>
                    <th class="px-4 py-3 text-sm font-semibold text-gray-700 uppercase tracking-wide">
                        {{ __('Code') }}
                    </th>
                    <th class="px-4 py-3 text-sm font-semibold text-gray-700 uppercase tracking-wide">
                        {{ __('Name') }}
                    </th>
                    <th class="px-4 py-3 text-sm font-semibold text-gray-700 uppercase tracking-wide">
                        {{ __('Region') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($records as $key => $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-800 whitespace-nowrap">
                            {{ $key + 1 }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                            {{ $record->code }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                            {{ $record->name }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                            {{ $record?->region?->name }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
