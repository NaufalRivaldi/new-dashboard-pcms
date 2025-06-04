@extends('print.layout')

@section('content')
    <div class="container py-5 bg-white">
        <div class="text-center mb-4">
            <h1 class="h4 fw-bold text-dark">
                {{ __('List of branches that have not imported data') }}
            </h1>
            <p class="text-muted">
                <strong>{{ __('Period: :period', ['period' => $period]) }}</strong>
            </p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th scope="col">{{ __('No') }}</th>
                        <th scope="col">{{ __('Code') }}</th>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('Region') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $key => $record)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $record->code }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record?->region?->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection