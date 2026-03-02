<?php $page="profile";?>
@extends('layout.mainlayout')
@section('content')
@component('components.breadcrumb')                
    @slot('title') Profile @endslot
    @slot('li_1') Dashboard @endslot
    @slot('li_2') Profile @endslot
@endcomponent
<div class="row">
    <div class="col-md-8">
        @include('layout.flash-message')
        <form method="post" action="{{route('profile.action')}}" id="editProfileForm">
            @csrf
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>First Name <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="first_name" id="first_name" value="{{$user->first_name}}">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Last Name <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="last_name" id="last_name" value="{{$user->last_name}}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Email</label>
                        <input class="form-control" type="email" name="email" id="email" value="{{$user->email}}" readonly>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input class="form-control" type="text" name="phone_number" id="phone_number" value="{{$user->phone_number}}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Password</label>
                        <input class="form-control pass-input" type="password" name="password" id="password">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input class="form-control" type="password" name="confirm_password" id="confirm_password">
                    </div>
                </div>
            </div>
            <div class="submit-section">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{route('dashboard')}}" class="btn btn-dark">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
<!-- Select2 JS -->
<script src="{{ URL::asset('public/assets/js/pages/profile.js')}}"></script>
@endsection