<div>
    <canvas id="analysis-student-record-chart"></canvas>
</div>

@push('scripts')
<script>
    const analysisStudentChart = document.getElementById('analysis-student-record-chart');

    new Chart(analysisStudentChart, {
        type: 'bar',
        data: @json($data),
    });
</script>
@endpush