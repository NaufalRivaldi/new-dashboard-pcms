<div>
    <canvas id="compare-student-lessons-record-chart"></canvas>
</div>

@push('scripts')
<script>
    const compareStudentLessonsChart = document.getElementById('compare-student-lessons-record-chart');

    new Chart(compareStudentLessonsChart, {
        type: 'bar',
        data: @json($data),
    });
</script>
@endpush