<div>
    <canvas id="analysis-student-lessons-record-chart"></canvas>
</div>

@push('scripts')
<script>
    const analysisStudentLessonsChart = document.getElementById('analysis-student-lessons-record-chart');

    new Chart(analysisStudentLessonsChart, {
        type: 'bar',
        data: @json($data),
    });
</script>
@endpush