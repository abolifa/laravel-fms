<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap');
    </style>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Print #{{ $transaction->id }}</title>
    <style>
        body {
            background: #d3d3d3;
            margin: 0;
            font-family: "Almarai", serif;
            direction: rtl;
        }

        .receipt-container {
            background: #e0e0e0;
            border: 5px solid #333;
            width: 100%;
            margin-top: 25px;
            padding: 20px;
            border-radius: 10px;
            box-sizing: border-box;
            position: relative;
            height: 50vh;
            /* Occupy half of the page height */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Header section */
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .receipt-header img {
            max-height: 80px;
            margin-bottom: 10px;
        }

        .receipt-header h2 {
            margin-bottom: 5px;
            font-size: 1.2rem;
        }

        .receipt-header p {
            font-size: 1rem;
            margin-bottom: 0;
        }

        /* Top bar with date, main tank, and fuel type */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .top-bar .left-date {
            direction: ltr;
        }

        /* Main table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        .data-table th,
        .data-table td {
            border: 2px solid #333;
            padding: 10px;
            text-align: center;
            font-size: 1rem;
        }

        .data-table th {
            width: 33.3%;
            font-weight: normal;
        }

        /* Status section */
        .status-section {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            font-size: 1.2rem;
        }

        .status-section span {
            margin-left: 10px;
            font-weight: bold;
        }

        .status-badge {
            background: #ffc107;
            color: #000;
            padding: 5px 15px;
            border: 2px solid #333;
            display: inline-block;
            font-size: 1rem;
            border-radius: 4px;
        }

        .tank-name {
            text-align: center;
            font-size: x-large;
            font-weight: 800;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .receipt-container {
                box-shadow: none;
                border: 5px solid #000;
                height: 50%;
                page-break-inside: avoid;
                /* Ensure the container prints on a single half-page */
            }
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <!-- Header Section -->
        <div class="receipt-header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
            <h2>جهاز الطيران الإلكتروني</h2>
            <p>مركز مصراتة</p>
        </div>
        <div class="tank-name">{{$transaction->tank->name}}</div>

        <!-- Top Bar -->
        <div class="top-bar">
            <div class="left-date">{{ $transaction->created_at->format('d/m/Y') }}</div>
            <div>{{$transaction->tank->fuel->type}}</div>
        </div>

        <!-- Data Table -->
        <table class="data-table">
            <tr>
                <th>الموظف</th>
                <th>{{ $transaction->employee->name }}</th>
            </tr>
            <tr>
                <th>المركبة</th>
                <th>{{ $transaction->car->model }}</th>
            </tr>

            <tr>
                <th>رقم اللوحة</th>
                <th>{{ $transaction->car->plate }}</th>
            </tr>
            <tr>
                <th>الكمية</th>
                <th>{{ $transaction->amount }} </th>
            </tr>
        </table>

        <!-- Status Section -->
        <div class="status-section">
            <span>نوع الطلب:</span>
            <div class="status-badge">{{ $transaction->status }}</div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>

</html>