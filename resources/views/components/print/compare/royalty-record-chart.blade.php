<div>
    <canvas id="compare-royalty-record-chart"></canvas>
</div>

@push('scripts')
<script>
    const compareRoyaltyChart = document.getElementById('compare-royalty-record-chart');

    new Chart(compareRoyaltyChart, {
        type: 'line',
        data: @json($data),
    });
</script>
@endpush