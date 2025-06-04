<div>
    <canvas id="analysis-fee-record-chart"></canvas>
</div>

@push('scripts')
<script>
    const analysisFeeChart = document.getElementById('analysis-fee-record-chart');

    new Chart(analysisFeeChart, {
        type: 'bar',
        data: @json($data),
    });
</script>
@endpush