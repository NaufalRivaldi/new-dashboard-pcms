<div>
    <canvas id="topunder-student-record-chart"></canvas>
</div>

@push('scripts')
<script>
    const topunderStudentChart = document.getElementById('topunder-student-record-chart');

    new Chart(topunderStudentChart, {
        type: 'bar',
        data: @json($data),
    });
</script>
@endpush