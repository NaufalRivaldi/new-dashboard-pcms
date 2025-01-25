<div>
    @foreach ($getState() ?? [] as $branch)
        <x-filament::badge
            class="mb-2"
            icon="heroicon-o-building-storefront"
        >
            {{ $branch->name }}
        </x-filament::badge>
    @endforeach
</div>