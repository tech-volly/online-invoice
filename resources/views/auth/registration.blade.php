<?php $page = "registration"; ?>
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
                        <h3 class="account-title">Register</h3>
                        <p class="account-subtitle">Access to our dashboard</p>

                        <!-- Account Form -->
                        <form action="{{ route('register.custom') }}" method="POST" id="registrationForm">
                            @csrf
                            <div class="form-group">
                                <label>Name</label><span class="text-danger ms-1">*</span>
                                <input type="text" placeholder="Name" id="name" class="form-control"
                                       name="name" value="{{old('name')}}">
                                <div class="text-danger pt-2">
                                    @error('name')
                                    {{$message}}
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Email</label><span class="text-danger ms-1">*</span>
                                <input type="text" placeholder="Email" id="email_address" class="form-control"
                                       name="email" value="{{old('email')}}">
                                <div class="text-danger pt-2">
                                    @error('email')
                                    {{$message}}
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group Password-icon">
                                <label>Password</label><span class="text-danger ms-1">*</span>
                                <input type="password" placeholder="Password" id="password" class="form-control pass-input"
                                       name="password" value="{{old('password')}}"><span class="fa fa-eye-slash toggle-password pt-4"></span>
                                <div class="text-danger pt-2">
                                    @error('password')
                                    {{$message}}
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group Password-icon">
                                <label>Confirm Password</label><span class="text-danger ms-1">*</span>
                                <input type="password" name="confirm_password" placeholder="Confirm Password" id="confirm_password" class="form-control pass-input"
                                       value="{{old('confirm_password')}}"><span class="fa fa-eye-slash toggle-password pt-4"></span>
                                <div class="text-danger pt-2">
                                    @error('confirm_password')
                                    {{$message}}
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary account-btn" type="submit">Register</button>
                            </div>
                            <div class="account-footer">
                                <p>Already have an account? <a href="{{url('/')}}" >Login</a></p>
                            </div>
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
    <script src="{{ URL::asset('public/assets/js/pages/registration.js')}}"></script>
    @endsection    