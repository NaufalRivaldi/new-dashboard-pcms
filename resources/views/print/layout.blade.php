<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }

            .container {
                padding: 0 !important;
                box-shadow: none !important;
            }

            table {
                font-size: 12px;
            }

            .text-muted {
                color: black !important;
            }

            canvas {
                max-width: 100% !important;
                height: auto !important;
                page-break-inside: avoid;
                display: block;
            }

            .chart-container {
                width: 100%;
                height: auto;
            }

            .page-break {
                page-break-before: always;
                break-before: page;
            }
        }
    </style>

</head>

<body>
    <div class="text-center mb-3 mt-2 d-print-none">
        <button class="btn btn-outline-primary" onclick="window.print()">
            <i class="bi bi-printer"></i> {{ __('Print') }}
        </button>
    </div>

    @yield('content')

    <!-- Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('scripts')
</body>

</html>