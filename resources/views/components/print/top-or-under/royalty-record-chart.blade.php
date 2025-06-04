<div>
    <canvas id="topunder-royalty-record-chart"></canvas>
</div>

@push('scripts')
<script>
    const topunderRoyaltyChart = document.getElementById('topunder-royalty-record-chart');

    new Chart(topunderRoyaltyChart, {
        type: 'line',
        data: @json($data),
    });
</script>
@endpush