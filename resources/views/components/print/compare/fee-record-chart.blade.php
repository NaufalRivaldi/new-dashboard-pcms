<div>
    <canvas id="compare-fee-record-chart"></canvas>
</div>

@push('scripts')
<script>
    const compareFeeChart = document.getElementById('compare-fee-record-chart');

    new Chart(compareFeeChart, {
        type: 'bar',
        data: @json($data),
    });
</script>
@endpush