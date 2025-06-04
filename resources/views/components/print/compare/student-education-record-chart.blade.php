<div>
    <canvas id="compare-student-educations-record-chart"></canvas>
</div>

@push('scripts')
<script>
    const compareStudentEducationsChart = document.getElementById('compare-student-educations-record-chart');

    new Chart(compareStudentEducationsChart, {
        type: 'bar',
        data: @json($data),
    });
</script>
@endpush