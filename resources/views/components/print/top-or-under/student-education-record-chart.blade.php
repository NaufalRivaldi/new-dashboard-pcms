<div>
    <canvas id="topunder-student-education-record-chart"></canvas>
</div>

@push('scripts')
<script>
    const topunderStudentEducationChart = document.getElementById('topunder-student-education-record-chart');

    new Chart(topunderStudentEducationChart, {
        type: 'bar',
        data: @json($data),
    });
</script>
@endpush