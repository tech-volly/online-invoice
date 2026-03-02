<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>HDS</title>
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">-->
    <!-- <link rel="stylesheet" href="{{url('public/assets/libs/bootstrap/css/bootstrap.min.css')}}"> -->
	<!-- <link rel="stylesheet" type="text/css" href="{{URL::asset('public/assets/css/invoice.css')}}"> -->
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
    margin-bottom:10px;
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
    margin-top:5px;
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
.task table th, .task table td {
	border-bottom: 0;
	border-right: 0;
}
.task table td {
	border-bottom: 1px solid #ddd;
}
.task table th, .task table td {
	padding-bottom: 22px;
	vertical-align: top;
}
.task tbody tr:nth-child(even) {
	background: transparent;
} 
  
tfoot td {
    border-left: none !important;
    border-right:none ;
    /* border-bottom:none !important */
}
tfoot td:first-child {
    border-bottom: none;
}

.border-bottom {
    border-bottom:none !important ;
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
   border:none;
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
    border:none !important;
}   

.br{
    border:1px solid #000 !important;
    margin:3px 0;
}
.invoice-table thead tr th {
    border-right:1px solid #000 !important;
}
</style>
</head> 
<body>
    <div class="main-pdf-section">
         <table class="border-o" >
             <tbody>
                 <tr >
                     <td>
                        <div class="logo">
                            <img width="120px" height="120px" src="{{URL::asset('public/uploads/brands/'.$invoice->brand_id.'/'.$invoice->brand->image)}}" alt="logo" />
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
                            <h2>Tax Invoice</h2>
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
         <table class="border-o">
             <tr>
                 <td class="v-align" width="12%">
                    <div class="left-side">
                        <h4>Bill To :</h4>
                    </div>
                 </td>
                 <td >
                    <div class="address-detail">     
                        <p>{{$invoice->client->client_business_name}}</p>
                        <p>{{$invoice->client->client_street_address_1}}</p>
                        <p>{{$invoice->client->client_street_address_2}}</p>
                        <p>{{$invoice->client->client_city}}, {{$invoice->client->client_state}}</p>
                        <p>{{$invoice->client->client_country}}, {{$invoice->client->client_postalcode}}</p>
                    </div>
                 </td>
                 <td>&nbsp;</td> 
                 <td>&nbsp;</td>
                 <td>
                    <div class="invoice-detail" >
                        @if($invoice->client->client_number)
                            <p>{{$invoice->client->client_business_name}}  ({{$invoice->client->client_number}})</p>
                        @endif
                        @if($invoice->invoice_po_number)
                            <p><span>PO Number: </span>   {{$invoice->invoice_po_number}}</p>
                        @endif
                        <br>
                        <p><span>Invoice No: </span>   {{$invoice->invoice_number}}</p>
                        <p><span>Invoice Date: </span> {{getFormatedDate($invoice->invoice_date)}}</p>
                        <p><span>Due Date: </span>     {{getFormatedDate($invoice->invoice_due_date)}}</p>
                    </div>
                 </td>
             </tr>
             
         </table>
        <br>
        
        <table class="border-o">
            <tr>
                <td class="v-align" width="12%">
                   <div class="left-side">
                       <h4>Ship To :</h4>
                   </div>
                </td>
                <td class="border-0">
                    <div class="address-detail">
                        <p>{{$invoice->client->client_business_name}}</p>
                        <p>{{$invoice->client->shipping_street_address_1}}</p>
                        <p>{{$invoice->client->shipping_street_address_2}}</p>
                        <p>{{$invoice->client->shipping_city}}, {{$invoice->client->shipping_state}}</p>
                        <p>{{$invoice->client->shipping_country}}, {{$invoice->client->shipping_postalcode}}</p>
                    </div>
                </td>
               
            </tr>
            
        </table>
        
         <br> <br>
         <table class="invoice-table">
            <thead>
                <tr>
                    <th width="15%">Item</th>
                    <th width="35%">Description</th>
                    <th width="13%">Unit Price</th>
                    <th width="8%">Qty</th>
                    <th width="15%">Item Total</th>
                    <th width="14%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->invoice_payments as $ipayment)
                <tr>
                    <td>{{$ipayment->product->product_name}}</td>
                    <td>{{$ipayment->product_description}}</td>
                    <td>{{getPrice($ipayment->product_unit_price)}}</td>
                    <td>{{$ipayment->product_quantity}}</td>
                    <td>{{getPrice($ipayment->product_subtotal)}}</td>
                    <td>{{getPrice($ipayment->product_grand_total)}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot >        
                <tr>
                    <td colspan="3" class="border-bottom"></td>
                    <td colspan="2"><strong>Item Total</strong></td>
                    <td>{{getPrice($invoice->invoice_item_total)}}</td>
                </tr>
                @if($invoice->invoice_discount)
                <tr>
                    <td colspan="3" class="border-bottom"></td>
                    <td colspan="2"><strong>Discount ({{$invoice->invoice_discount}} %)</strong></td>
                    <td>{{getPrice($invoice->product_final_discount_amt)}}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="3" class="border-bottom"></td>
                    <td colspan="2"><strong>GST</strong></td>
                    <td>{{getPrice($invoice->invoice_grand_gst)}}</td>
                </tr>          
                <tr>
                    <td colspan="3" class="border-bottom"></td>
                    <td colspan="2" class="gray"><strong>Grand Total</strong></td>
                    <td class="gray"><strong>{{getPrice($invoice->invoice_grand_total)}}</strong></td>
                </tr>
            </tfoot>
        </table>
        <br><br>
        <table width="100%" class="paymeny-border">
            <tr>
                <td >
                    <div class="payment-detail">
                        <h4>Please make Payment to</h4>
                            <br>
                            @if($invoice_setting->invoice_footer_company_name)<p><b>{{$invoice_setting->invoice_footer_company_name}}</b></p>@endif
                            @if($invoice_setting->invoice_footer_bsb_number)<p><b>BSB: {{$invoice_setting->invoice_footer_bsb_number}}</b></p>@endif
                            @if($invoice_setting->invoice_footer_acc_number)<p><b>ACC: {{$invoice_setting->invoice_footer_acc_number}}</b></p>@endif
                            @if($invoice_setting->invoice_footer_email)<p><b>Remittance: {{$invoice_setting->invoice_footer_email}}</b></p>@endif
                            <br>
                            @if($invoice->invoice_master_notes)<p><b>Notes:</b>{!! $invoice->invoice_master_notes !!}</p>@endif
                    </div>
                </td>
            </tr>       
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js"></script>
</body>
</html>  
