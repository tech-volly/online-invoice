<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client Statement</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        .center {
            text-align: center;
        }

        ul {
            padding-left: 0;
        }

        ul li {
            list-style: none;
        }

        body {
            font-family: 'examplefont', sans-serif;
            /* font-family: 'Roboto', sans-serif;   */
        }

        .gray {
            background: #e5e5e5;
        }

        /***pdf html***/

        .main-pdf-section {
            background: #fff;
            margin: 41px auto 0 auto;
            padding: 0px 20px;
            width: 210mm;
            height: 297mm;
        }

        .address-list h4 {
            padding-bottom: 10px;
            font-size: 20px;
            margin-bottom: 10px;
            line-height: 35px;
        }

        .address-list p {
            line-height: 19px;
            font-size: 14px;
        }

        .invoice-heading h2 {
            font-weight: 700;
            font-size: 28px;
            line-height: 28px;
            margin-bottom: 0;
            margin-top: 5px;
        }

        hr {
            margin-top: 2px;
            margin-bottom: 2px;
            border-top: 10px solid #000 !important;
            /* border-style: double; */
        }

        .left-side h4 {
            margin-bottom: 0;
            font-weight: 700;
            font-size: 20px;
        }

        .address-detail p {
            line-height: 19px;
            font-size: 14px;
        }

        .invoice-detail p {
            line-height: 19px;
            font-size: 14px;
            font-style: italic;
        }

        .invoice-detail p span {
            font-weight: 700;
            padding-right: 8px;
        }

        /***table css***/


        table {
            border-collapse: collapse;
            color: #4a4a4d;
            width: 100%;
        }

        thead {
            background: #e5e5e5;
            -webkit-background: #e5e5e5;
            -moz-background: #e5e5e5;
            background: #e5e5e5;
            color: #fff;
            border: 1px solid #000;
        }

        tbody tr:nth-child(even) {
            background: #f0f0f2;
        }

        th,
        td {
            padding: 5px 10px;
            vertical-align: middle;
            color: #000;
            font-size: 14px;
            text-align: left;
        }

        td {
            border-bottom: 1px solid #000;
            border-right: 1px solid #000;
            border-left: 1px solid #000
        }

        .book-title {
            color: #395870;
        }

        .item-stock,
        .item-qty {
            text-align: center;
        }

        .item-price {
            text-align: right;
        }

        .item-multiple {
            display: block;
        }

        .task table {
            margin-bottom: 44px;
        }

        .task a {
            color: #666;
        }

        .task thead {
            background-color: #f5f5f5;
            -webkit-background: transparent;
            -moz-background: transparent;
            background: transparent;
            color: #333;
        }

        .task table th,
        .task table td {
            border-bottom: 0;
            border-right: 0;
        }

        .task table td {
            border-bottom: 1px solid #ddd;
        }

        .task table th,
        .task table td {
            padding-bottom: 22px;
            vertical-align: top;
        }

        .task tbody tr:nth-child(even) {
            background: transparent;
        }

        tfoot td {
            border-left: none !important;
            border-right: none;
            /* border-bottom:none !important */
        }

        tfoot td:first-child {
            border-bottom: none;
        }

        .border-bottom {
            border-bottom: none !important;
        }

        .payment-detail h5 {
            padding-bottom: 20px;
            font-style: italic;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 0;
        }

        .payment-detail ul {
            margin-bottom: 0;
        }

        .payment-detail p {
            line-height: 20px;
            font-size: 14px;
            font-weight: 700 !important;
            font-style: italic;
        }

        .border-o tr td {
            border: none;
        }

        .v-align {
            vertical-align: top;
        }

        .invoice-table thead tr {
            background: #e5e5e5 !important;
            color: #000 !important;
            border: 1px solid #000 !important;
        }

        .paymeny-border tr td {
            border: none !important;
        }

        .br {
            border: 1px solid #000 !important;
            margin: 3px 0;
        }

        .invoice-table thead tr th {
            border-right: 1px solid #000 !important;
        }
    </style>
</head>

<body>
    <div class="main-pdf-section">

        {{-- ── HEADER: Logo + Company Info + Title (same 3-col as invoice) ── --}}
        <table class="border-o">
            <tbody>
                <tr>
                    <td>
                        <div class="logo">
                            @if($brand && $brand->image)
                            <img width="120px" height="120px"
                                src="{{ URL::asset('public/uploads/brands/' . $brand->id . '/' . $brand->image) }}"
                                alt="logo" />
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="address-list v-align">
                            @if($invoice_setting->invoice_header_company_name)<h4>{{$invoice_setting->invoice_header_company_name}}</h4>@endif
                            @if($invoice_setting->invoice_header_number)<p>ABN:{{$invoice_setting->invoice_header_number}}</p>@endif
                            @if($invoice_setting->invoice_header_street_address)<p>{{$invoice_setting->invoice_header_street_address}}</p>@endif
                            @if($invoice_setting->invoice_header_city)<p>{{$invoice_setting->invoice_header_city}}</p>@endif
                            <p>{{$invoice_setting->invoice_header_state}} - {{$invoice_setting->invoice_header_postalcode}}</p>
                            @if($invoice_setting->invoice_header_tollfree)<p>{{$invoice_setting->invoice_header_tollfree}}</p>@endif
                            @if($invoice_setting->invoice_header_email)<p>{{$invoice_setting->invoice_header_email}}</p>@endif
                            @if($invoice_setting->invoice_header_website)<p>{{$invoice_setting->invoice_header_website}}</p>@endif
                        </div>
                    </td>
                    <td class="v-align">
                        <div class="invoice-heading">
                            <h2>Client Statement</h2>
                            @if($from_date || $to_date)
                            <p class="statement-period">
                                @if($from_date && $to_date)
                                {{ \Carbon\Carbon::parse($from_date)->format('d M Y') }}
                                &nbsp;&ndash;&nbsp;
                                {{ \Carbon\Carbon::parse($to_date)->format('d M Y') }}
                                @elseif($from_date)
                                From {{ \Carbon\Carbon::parse($from_date)->format('d M Y') }}
                                @elseif($to_date)
                                Upto {{ \Carbon\Carbon::parse($to_date)->format('d M Y') }}
                                @endif
                            </p>
                            @endif
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <br>
        <div class="br"></div>
        <div class="br"></div>
        <br>
        <br>

        {{-- ── BILL TO (same structure as invoice) ── --}}
        <table class="border-o">
            <tr>
                <td class="v-align" width="12%">
                    <div class="left-side">
                        <h4>Bill To :</h4>
                    </div>
                </td>
                <td>
                    <div class="address-detail">
                        <p>{{$client->client_business_name}}</p>
                        <p>{{$client->client_street_address_1}}</p>
                        <p>{{$client->client_street_address_2}}</p>
                        <p>{{$client->client_city}}, {{$client->client_state}}</p>
                        <p>{{$client->client_country}}, {{$client->client_postalcode}}</p>
                    </div>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>
                    <div class="invoice-detail">
                        @if($client->client_number)
                        <p>{{$client->client_business_name}} ({{$client->client_number}})</p>
                        @endif
                        @if($client->client_email)
                        <p><span>Email: </span>{{$client->client_email}}</p>
                        @endif
                        @if($client->client_phone)
                        <p><span>Phone: </span>{{$client->client_phone}}</p>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <br>

        {{-- ── SHIP TO (same structure as invoice) ── --}}
        <table class="border-o">
            <tr>
                <td class="v-align" width="12%">
                    <div class="left-side">
                        <h4>Ship To :</h4>
                    </div>
                </td>
                <td class="border-0">
                    <div class="address-detail">
                        <p>{{$client->client_business_name}}</p>
                        <p>{{$client->shipping_street_address_1}}</p>
                        <p>{{$client->shipping_street_address_2}}</p>
                        <p>{{$client->shipping_city}}, {{$client->shipping_state}}</p>
                        <p>{{$client->shipping_country}}, {{$client->shipping_postalcode}}</p>
                    </div>
                </td>
            </tr>
        </table>

        <br><br>

        {{-- ── STATEMENT TABLE ── --}}
        @php
        $allRows = collect($data);
        $totalRow = $allRows->last();
        $dataRows = $allRows->slice(0, $allRows->count() - 1);
        @endphp

        <table class="invoice-table">
            <thead>
                <tr>
                    {{-- Column widths tuned so all 7 cols fit within A4 without overflow --}}
                    <th width="16%">Invoice No</th>
                    <th width="13%">Due Date</th>
                    <th width="13%">Payment Date</th>
                    <!-- <th width="13%">Status</th>
                    <th width="15%" class="text-right">Amount</th>
                    <th width="15%" class="text-right">Paid</th>
                    <th width="15%" class="text-right">Outstanding</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach($dataRows as $row)
                <tr>
                    <td>{{ $row['invoice_number'] }}</td>
                    <td>{{ $row['due_date'] }}</td>
                    <td>{{ $row['payment_date'] }}</td>
                    <!-- <td>{{ $row['payment_status'] }}</td>
                        <td class="text-right">{{ $row['invoice_amount'] }}</td>
                        <td class="text-right">{{ $row['paid_amount'] }}</td>
                        <td class="text-right">{{ $row['outstanding_amount'] }}</td> -->
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                {{-- Grand Total row: same gray style as invoice Grand Total ── --}}
                <tr>
                    <td colspan="4" style="text-align:right; background:#e5e5e5 !important; border:1px solid #000 !important; border-left:1px solid #000 !important;"><strong>TOTAL</strong></td>
                    <td class="text-right" style="background:#e5e5e5 !important; border:1px solid #000 !important;"><strong>{{ $totalRow['invoice_amount'] }}</strong></td>
                    <td class="text-right" style="background:#e5e5e5 !important; border:1px solid #000 !important;"><strong>{{ $totalRow['paid_amount'] }}</strong></td>
                    <td class="text-right" style="background:#e5e5e5 !important; border:1px solid #000 !important;"><strong>{{ $totalRow['outstanding_amount'] }}</strong></td>
                </tr>
            </tfoot>
        </table>

        <br><br>

        {{-- ── FOOTER: same as invoice ── --}}
        <table width="100%" class="paymeny-border">
            <tr>
                <td>
                    <div class="payment-detail">
                        <h4>Please make Payment to</h4>
                        <br>
                        @if($invoice_setting->invoice_footer_company_name)<p><b>{{$invoice_setting->invoice_footer_company_name}}</b></p>@endif
                        @if($invoice_setting->invoice_footer_bsb_number)<p><b>BSB: {{$invoice_setting->invoice_footer_bsb_number}}</b></p>@endif
                        @if($invoice_setting->invoice_footer_acc_number)<p><b>ACC: {{$invoice_setting->invoice_footer_acc_number}}</b></p>@endif
                        @if($invoice_setting->invoice_footer_email)<p><b>Remittance: {{$invoice_setting->invoice_footer_email}}</b></p>@endif
                    </div>
                </td>
            </tr>
        </table>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js"></script>
</body>

</html>