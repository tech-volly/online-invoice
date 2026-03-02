<?php $page = "otp"; ?>
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
                        <h3 class="account-title">OTP</h3>
                        <p class="account-subtitle">We sent code to email : {{ substr(auth()->user()->email, 0, 5) . '******' . substr(auth()->user()->email,  -2) }}</p>
                        <form method="post" action="{{route('verify-account.action')}}">
                            @csrf
                            <div class="otp-wrap">
                                <input type="text" name="otp_1" id="otp_1" data-next="otp_2" placeholder="0" maxlength="1" class="otp-input">
                                <input type="text" name="otp_2" id="otp_2" data-next="otp_3" data-previous="otp_1" placeholder="0" maxlength="1" class="otp-input">
                                <input type="text" name="otp_3" id="otp_3" data-next="otp_4" data-previous="otp_2" placeholder="0" maxlength="1" class="otp-input">
                                <input type="text" name="otp_4" id="otp_4" data-previous="otp_3" placeholder="0" maxlength="1" class="otp-input">
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary account-btn" type="submit">Enter</button>
                            </div>
                            <div class="account-footer">
                                <p>Not yet received? <a href="{{ route('verify-account.resend') }}">Resend OTP</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection