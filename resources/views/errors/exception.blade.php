<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="description" content="Smarthr - Bootstrap Admin Template">
        <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
        <meta name="author" content="HDS Financials">
        <!-- <meta name="robots" content="noindex, nofollow"> -->
        <meta name="robots" content="noindex">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Dashboard - HRMS admin template</title>
        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="{{URL::asset('public/assets/img/favicon.png')}}">
        @include('layout.partials.head')
    </head>
    <body class="error-page">
        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <div class="error-box">
                <h1>403</h1>
                <h3><i class="fa fa-warning"></i> Oops! Something went wrong!</h3>
                <p>You do not have the required authorization.</p>
                <a href="{{url('/')}}" class="btn btn-custom">Back to Home</a>
            </div>
        </div>
        <!-- /Main Wrapper -->
        <script type="text/javascript">
            var base_url = "{{URL::to('/')}}/";
        </script>
        @include('layout.partials.footer-scripts')
    </body>
</html>