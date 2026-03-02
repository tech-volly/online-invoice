<?php $page = "login"; ?>
@extends('layout.mainlayout')
@section('content')
<body class="account-page">

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="account-content">
            <div class="container">

                <!-- Account Logo -->
                <div class="account-logo">
                    <a href="{{url('dashboard')}}"><img src="{{ URL::asset('public/assets/img/logo2.png')}}?28062022" alt="HDS Financials"></a>
                    <h4>Hospitality Dietary Solutions</h4>
                </div>
                <!-- /Account Logo -->
                @include('layout.flash-message')
                <div class="account-box">
                    <div class="account-wrapper">
                        <h3 class="account-title">Login</h3>
                        <p class="account-subtitle">Access to our dashboard</p>


                        <!-- Account Form -->

                        <form method="POST" action="{{ route('login.custom') }}" id="loginForm">
                            @csrf
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" placeholder="Email" id="email" class="form-control" name="email">
                                <div class="text-danger pt-2">
                                    @error('0')
                                    {{$message}}
                                    @enderror
                                    @error('email')
                                    {{$message}}
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group Password-icon">
                                <div class="row">
                                    <div class="col">
                                        <label>Password</label>
                                    </div>
                                    <div class="col-auto">
                                        <a class="text-muted" href="{{route('forgot-password')}}" >
                                            Forgot password?
                                        </a>
                                    </div>
                                </div>
                                <input type="password" placeholder="Password" id="password" class="form-control pass-input" name="password"><span class="fa fa-eye-slash toggle-password pt-4"></span>
                                <div class="text-danger pt-2">
                                    @error('password')
                                    {{$message}}
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <button class="btn btn-primary account-btn" type="submit">Login</button>
                            </div>
                            <!-- <div class="account-footer">
                                    <p>Don't have an account yet? <a href="{{url('registration')}}">Register</a></p>
                            </div> -->
                        </form>
                        <!-- /Account Form -->

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Wrapper -->
    @endsection
    @section('script')
    <script src="{{ URL::asset('public/assets/js/pages/login.js')}}"></script>
    @endsection    