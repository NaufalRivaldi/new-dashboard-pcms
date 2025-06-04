<div>
    <canvas id="topunder-student-lessons-record-chart"></canvas>
</div>

@push('scripts')
<script>
    const topunderStudentLessonsChart = document.getElementById('topunder-student-lessons-record-chart');

    new Chart(topunderStudentLessonsChart, {
        type: 'bar',
        data: @json($data),
    });
</script>
@endpush