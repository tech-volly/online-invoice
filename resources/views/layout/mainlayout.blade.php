<!DOCTYPE html>
<html lang="en">
    <head>
        @php
        $seo_data = getSEOData(@$seo_data);
        @endphp
        <title>{{@$seo_data['seo_title']}}</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="description" content="Smarthr - Bootstrap Admin Template">
        <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
        <meta name="author" content="HDS Financials">
        <!-- <meta name="robots" content="noindex, nofollow"> -->
        <meta name="robots" content="noindex">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="{{URL::asset('public/assets/img/favicon.png')}}">
        @include('layout.partials.head')
    </head>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        @if(!Route::is(['error-404','error-500','lock-screen','verify-account','forgot-password', 'reset-password' ,'login','registration'])) 
        @include('layout.partials.header')
        @if(!Route::is(['error-404','job-list','job-view'])) 
        @include('layout.partials.nav')
        @endif
        @if(!Route::is(['chat','contacts','ticket-view','incoming-call','job-list','job-view','outgoing-call','tasks','ticket-view','video-call','voice-call'])) 
        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content container-fluid">

                @yield('content')
            </div>		
        </div>
        @endif	
        <!-- /Page Wrapper -->
        @if(Route::is(['chat','tasks','ticket-view','video-call','voice-call'])) 
        <!-- Page Wrapper -->
        <div class="page-wrapper">

            <!-- Chat Main Row -->
            <div class="chat-main-row">

                <!-- Chat Main Wrapper -->
                <div class="chat-main-wrapper">
                    @yield('content')
                </div>
            </div>
        </div>
        @endif
        @endif
        @if(Route::is(['error-404','error-500','lock-screen','verify-account','forgot-password','reset-password','login','registration'])) 
        @yield('content')
        @endif

    </div>
    <!-- /Main Wrapper -->
    <script type="text/javascript">
        var base_url = "{{URL::to('/')}}/";

        function getPrice(amt) {
            var formatter = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
            });
            var final_amt = formatter.format(amt);

            var last3 = final_amt.substr(final_amt.length - 3);
            if (last3 == ".00") {
                final_amt = final_amt.replace(".00", "");
            }
            return final_amt.replace("₹", "$ ");
        }

        function formatDate(date) {
            var splitDate = date.split('-');
            var formatDate = splitDate[2] + '-' + splitDate[1] + '-' + splitDate[0];

            return formatDate;
        }

        function getRoundedAmount(X = 0) {
            let amount_data;
            var fixed_input = X.toFixed(2);
            var ip_exp = fixed_input.toString().split(".");
            var IP = ip_exp[0];
            var FP = ip_exp[1];
            if(FP == undefined || FP == '') {
                amount_data = {
                    amount : 0,
                    round_amount: 0
                };
                return amount_data;
            }
            if (FP.length == 1) {
                var FP1 = FP.substring(0);
                var FP2 = 0;
            } else if (FP.length == 0) {
                var FP1 = 0;
                var FP2 = 0;
            } else {
                var FP1 = FP.substring(0, 1);
                var FP2 = FP.substring(1);
            }

            switch (FP2) {
                case "0":
                    break;
                case "1":
                case "2":
                case "3":
                    FP2 = 0;
                    break;
                case "4":
                case "5":
                case "6":
                case "7":
                    FP2 = 5;
                    break;
                case "8":
                case "9":
                    FP2 = 0;
                    FP1++;
                    break;
                default:
                    break;
            }

            if (FP1 == 10 && FP2 == 0) {
                IP++;
                var Y = IP + ".00";
            } else {
                var Y = IP + "." + FP1 + String(FP2);
            }

            var round_amount = 0.00;
            if (fixed_input > Y) {
                round_amount = ((Math.floor(fixed_input * 100) - Math.floor(Y * 100)) / 100);
            } else if (Y > fixed_input) {
                round_amount = ((Math.floor(Y * 100) - Math.floor(fixed_input * 100)) / 100);
            }
            amount_data = {
                amount : Y,
                round_amount: round_amount
            };

            return amount_data;
        }
    </script>
    @include('layout.partials.footer-scripts')
</body>
</html>