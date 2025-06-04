<div>
    <canvas id="analysis-royalty-record-chart"></canvas>
</div>

@push('scripts')
<script>
    const analysisRoyaltyChart = document.getElementById('analysis-royalty-record-chart');

    new Chart(analysisRoyaltyChart, {
        type: 'line',
        data: @json($data),
    });
</script>
@endpush