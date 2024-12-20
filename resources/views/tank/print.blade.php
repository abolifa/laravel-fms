<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap');
    </style>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>بيان الخزان #{{ $tank->id }}</title>
    <style>
        body {
            background: #d3d3d3;
            margin: 0;
            font-family: "Almarai", serif;
            direction: rtl;
        }

        .statement-container {
            background: #ffffff;
            border: 5px solid #333;
            width: 90%;
            margin: 25px auto;
            padding: 20px;
            border-radius: 10px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Header section */
        .statement-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .statement-header img {
            max-height: 80px;
            margin-bottom: 10px;
        }

        .statement-header h2 {
            margin-bottom: 5px;
            font-size: 1.5rem;
        }

        .statement-header p {
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        /* Tank Information */
        .tank-info {
            margin-bottom: 20px;
            font-size: 1.1rem;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .tank-info div {
            margin-bottom: 5px;
        }

        /* Unified Data Table */
        .data-section {
            margin-bottom: 20px;
        }

        .data-section h3 {
            margin-bottom: 10px;
            font-size: 1.2rem;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }

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
            background-color: #f2f2f2;
        }

        .positive {
            color: green;
            font-weight: bold;
        }

        .negative {
            color: red;
            font-weight: bold;
        }

        /* Footer Section */
        .footer-section {
            text-align: center;
            font-size: 1rem;
            margin-top: 20px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .statement-container {
                box-shadow: none;
                border: 5px solid #000;
                width: 100%;
                margin: 0;
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="statement-container">
        <!-- Header Section -->
        <div class="statement-header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
            <h2>بيان الخزان الإلكتروني</h2>
            <p>مركز مصراتة</p>
        </div>

        <!-- Tank Information -->
        <div class="tank-info">
            <div><strong>اسم الخزان:</strong> {{ $tank->name }}</div>
            <div><strong>نوع الوقود:</strong> {{ $tank->fuel->type ?? 'غير محدد' }}</div>
            <div><strong>سعة الخزان:</strong> {{ number_format($tank->capacity, 2) }} لتر</div>
        </div>

        <div class="tank-info">
            <div><strong>المستوى الابتدائي:</strong> {{ number_format($initialLevel, 2) }} لتر</div>
            <div><strong>المستوى الحالي:</strong> {{ number_format($tank->level, 2) }} لتر</div>
        </div>

        <!-- Unified Orders and Transactions Table -->
        <div class="data-section">
            <h3>الطلبات والمعاملات</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>المستوى التراكمي (لتر)</th>
                        <th>النوع</th>
                        <th>الوصف</th>
                        <th>الكمية (لتر)</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($combinedEntries as $entry)
                    <tr>
                        <td>{{ number_format($entry['cumulative_level'], 2) }}</td>
                        <td>
                            @if($entry['type'] === 'order')
                            طلب
                            @elseif($entry['type'] === 'transaction')
                            معاملة
                            @else
                            ابتدائي
                            @endif
                        </td>
                        <td>{{ $entry['description'] }}</td>
                        <td>
                            @if($entry['type'] === 'order')
                            <span class="positive">+ {{ number_format($entry['amount'], 2) }}</span>
                            @elseif($entry['type'] === 'transaction')
                            <span class="negative">- {{ number_format(abs($entry['amount']), 2) }}</span>
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($entry['date'])->format('d/m/Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">لا توجد طلبات أو معاملات.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Section -->
        <div class="footer-section">
            <p>تمت الطباعة في {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>

</html>