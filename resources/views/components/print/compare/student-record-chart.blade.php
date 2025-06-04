<div>
    <canvas id="compare-student-record-chart"></canvas>
</div>

@push('scripts')
<script>
    const compareStudentChart = document.getElementById('compare-student-record-chart');

    new Chart(compareStudentChart, {
        type: 'bar',
        data: @json($data),
    });
</script>
@endpush