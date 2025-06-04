<div>
    <canvas id="analysis-student-education-record-chart"></canvas>
</div>

@push('scripts')
<script>
    const analysisStudentEducationChart = document.getElementById('analysis-student-education-record-chart');

    new Chart(analysisStudentEducationChart, {
        type: 'bar',
        data: @json($data),
    });
</script>
@endpush