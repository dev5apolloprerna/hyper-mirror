<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Summary {{ $lead->strLeadNo }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        h2, h4 {
            margin: 0 0 8px;
        }

        .mb-12 {
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <h2>Payment Summary</h2>
    <div class="mb-12"><strong>Invoice No:</strong> {{ $lead->strLeadNo }}</div>
    <div class="mb-12"><strong>Customer:</strong> {{ $lead->customer->strCustomer ?? '—' }}</div>

    <table class="mb-12">
        <tr>
            <th>Lead Amount</th>
            <th>Total Paid</th>
            <th>Total Discount</th>
            <th>Unpaid Amount</th>
        </tr>
        <tr>
            <td class="text-right">₹{{ number_format($leadAmount, 2) }}</td>
            <td class="text-right">₹{{ number_format($totalPaid, 2) }}</td>
            <td class="text-right">₹{{ number_format($totalDiscount, 2) }}</td>
            <td class="text-right">₹{{ number_format($pendingAmount, 2) }}</td>
        </tr>
    </table>

    <h4>Payment Entries</h4>
    <table>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Mode</th>
            <th>Paid Amount</th>
            <th>Discount</th>
            <th>Entered By</th>
        </tr>
        @forelse($payments as $index => $payment)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($payment->PaymentDate)->format('d-m-Y') }}</td>
                <td>{{ $payment->PaymentMode }}</td>
                <td class="text-right">₹{{ number_format((float) $payment->iPaidAmount, 2) }}</td>
                <td class="text-right">₹{{ number_format((float) ($payment->iDiscountAmount ?? 0), 2) }}</td>
                <td>{{ $payment->user->full_name ?? $payment->user->name ?? $payment->user->strUserName ?? '—' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No payment entries found.</td>
            </tr>
        @endforelse
    </table>
</body>

</html>
