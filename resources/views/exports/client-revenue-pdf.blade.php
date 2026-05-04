<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #366092;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            color: #366092;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 11px;
            color: #666;
        }
        
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f5f5f5;
        }
        
        .summary-item label {
            font-weight: bold;
            font-size: 10px;
            display: block;
            margin-bottom: 3px;
            color: #666;
        }
        
        .summary-item .value {
            font-size: 13px;
            font-weight: bold;
            color: #366092;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        thead {
            background-color: #366092;
            color: white;
        }
        
        thead th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            border: 1px solid #366092;
        }
        
        tbody td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tbody tr:hover {
            background-color: #f0f0f0;
        }
        
        .text-left {
            text-align: left;
        }
        
        .text-right {
            text-align: right;
        }
        
        .currency {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .percentage {
            text-align: right;
            font-weight: bold;
        }
        
        .positive {
            color: green;
        }
        
        .negative {
            color: red;
        }
        
        tfoot {
            background-color: #e8e8e8;
            font-weight: bold;
        }
        
        tfoot td {
            padding: 10px;
            border: 1px solid #999;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            text-align: center;
            color: #666;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Report generated on {{ $report_date }}</p>
        <p>Financial Year: {{ $current_fy }} @if($compareYear) vs {{ $previous_fy }} @endif</p>
    </div>
    
    <!-- Summary Section -->
    <div class="summary">
        <div class="summary-item">
            <label>Total Current Year Revenue</label>
            <div class="value">${{ number_format($totalCurrentRevenue, 2) }}</div>
        </div>
        @if($compareYear)
        <div class="summary-item">
            <label>Total Previous Year Revenue</label>
            <div class="value">${{ number_format($totalPreviousRevenue, 2) }}</div>
        </div>
        <div class="summary-item">
            <label>Total Difference</label>
            <div class="value @if($totalDifference >= 0) positive @else negative @endif">
                @if($totalDifference >= 0) + @endif ${{ number_format($totalDifference, 2) }}
            </div>
        </div>
        <div class="summary-item">
            <label>Overall Change %</label>
            <div class="value @if($totalDifference >= 0) positive @else negative @endif">
                @if($totalDifference >= 0) + @endif 
                {{ $totalPreviousRevenue > 0 ? number_format(($totalDifference / $totalPreviousRevenue) * 100, 2) : 0 }}%
            </div>
        </div>
        @endif
    </div>
    
    <!-- Clients Table -->
    <table>
        <thead>
            <tr>
                <th class="text-left" style="width: 10%;">Client #</th>
                <th class="text-left" style="width: 30%;">Client Name</th>
                @if($compareYear)
                <th class="currency" style="width: 15%;">{{ $previous_fy }}</th>
                @endif
                <th class="currency" style="width: 15%;">{{ $current_fy }}</th>
                @if($compareYear)
                <th class="currency" style="width: 15%;">Difference</th>
                <th class="percentage" style="width: 15%;">Change %</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $client)
            <tr>
                <td class="text-left">{{ $client['client_number'] }}</td>
                <td class="text-left">{{ $client['client_name'] }}</td>
                
                @if($compareYear)
                <td class="currency">${{ number_format($client['previous_year_revenue'], 2) }}</td>
                @endif
                
                <td class="currency">${{ number_format($client['current_year_revenue'], 2) }}</td>
                
                @if($compareYear)
                <td class="currency @if($client['difference'] >= 0) positive @else negative @endif">
                    @if($client['difference'] >= 0) + @endif 
                    ${{ number_format($client['difference'], 2) }}
                </td>
                <td class="percentage @if($client['percentage_change'] >= 0) positive @else negative @endif">
                    @if($client['percentage_change'] >= 0) + @endif 
                    {{ number_format($client['percentage_change'], 2) }}%
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-left">TOTAL</td>
                
                @if($compareYear)
                <td class="currency">${{ number_format($totalPreviousRevenue, 2) }}</td>
                @endif
                
                <td class="currency">${{ number_format($totalCurrentRevenue, 2) }}</td>
                
                @if($compareYear)
                <td class="currency @if($totalDifference >= 0) positive @else negative @endif">
                    @if($totalDifference >= 0) + @endif 
                    ${{ number_format($totalDifference, 2) }}
                </td>
                <td class="percentage @if($totalDifference >= 0) positive @else negative @endif">
                    @if($totalDifference >= 0) + @endif 
                    {{ $totalPreviousRevenue > 0 ? number_format(($totalDifference / $totalPreviousRevenue) * 100, 2) : 0 }}%
                </td>
                @endif
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        <p>This is an automated report. Please verify data accuracy.</p>
    </div>
</body>
</html>