<?php $page = "forgot-password"; ?>
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
                        <h3 class="account-title">Forgot Password?</h3>
                        <p class="account-subtitle">Enter your email to get a password reset link</p>
                        <form method="post" action="{{route('forgot-password.action')}}" id="forgotPasswordForm">
                            @csrf
                            <div class="form-group">
                                <label>Email Address</label>
                                <input class="form-control" type="email" name="email" id="email">
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary account-btn" type="submit">Reset Password</button>
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
    <script src="{{ URL::asset('public/assets/js/pages/forgot-password.js')}}"></script>
    @endsection