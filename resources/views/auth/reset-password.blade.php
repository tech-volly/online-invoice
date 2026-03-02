<?php $page = "forgot-password"; ?>
<?php $page = "reset-password"; ?>
@extends('layout.mainlayout')
@section('content')
<body class="account-page">
    <div class="main-wrapper">
        <div class="account-content">
            <div class="container">
                <div class="account-logo">
                    <a href="{{url('dashboard')}}"><img src="{{ URL::asset('public/assets/img/logo2.png')}}?28062022" alt="HDS Financials"></a>
                    <h4>Hospitality Dietary Solutions</h4>
                </div>
                @include('layout.flash-message')
                <div class="account-box">
                    <div class="account-wrapper">
                        <h3 class="account-title">Change Password</h3>
                        <p class="account-subtitle">Please enter the password to generate a new</p>
                        <form method="post" action="{{route('reset-password.action')}}" id="resetPasswordForm">
                            @csrf
                            <input type="hidden" name="reset_token" value="{{ $token }}">
                            <input type="hidden" name="reset_email" value="{{ $email }}">
                            <div class="form-group">
                                <label>New password</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Confirm password</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary account-btn" type="submit">Update Password</button>
                            </div>
                            <div class="account-footer">
                                <p>Remember your password? <a href="{{route('login')}}">Login</a></p>
                            </div>
                        </form>							
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('script')
    <script src="{{ URL::asset('public/assets/js/pages/reset-password.js')}}"></script>
    @endsection