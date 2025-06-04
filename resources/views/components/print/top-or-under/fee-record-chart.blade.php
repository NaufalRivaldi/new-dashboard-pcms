<div>
    <canvas id="topunder-fee-record-chart"></canvas>
</div>

@push('scripts')
<script>
    const topunderFeeChart = document.getElementById('topunder-fee-record-chart');

    new Chart(topunderFeeChart, {
        type: 'bar',
        data: @json($data),
    });
</script>
@endpush