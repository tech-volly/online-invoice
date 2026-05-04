<!DOCTYPE html>
<html>
<head>
    <title>Client Statement</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #70AD47;
            color: #fff;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h2>Client Statement Report</h2>

<table>
    <thead>
        <tr>
            <th>Client Name</th>
            <th>Shipping Name</th>
            <th>Invoice #</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Payment Date</th>
            <th>Invoice Amount</th>
            <th>Paid Amount</th>
            <th>Outstanding</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
            <tr class="{{ $row['invoice_number'] == 'TOTAL' ? 'total-row' : '' }}">
                <td>{{ $row['client_name'] }}</td>
                <td>{{ $row['shipping_name'] }}</td>
                <td>{{ $row['invoice_number'] }}</td>
                <td>{{ $row['due_date'] }}</td>
                <td>{{ $row['payment_status'] }}</td>
                <td>{{ $row['payment_date'] }}</td>
                <td class="text-right">{{ $row['invoice_amount'] }}</td>
                <td class="text-right">{{ $row['paid_amount'] }}</td>
                <td class="text-right">{{ $row['outstanding_amount'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>