<?php $page="add-suppliers";?>
@extends('layout.mainlayout')
@section('css')
    <link href="{{ URL::asset('public/assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
@component('components.breadcrumb')                
    @slot('title') {{ $data->id > 0 ? 'Edit' : 'Add' }} User @endslot
    @slot('li_1') Dashboard @endslot
    @slot('li_2') Users @endslot
    @slot('li_3') {{ $data->id > 0 ? 'Edit' : 'Add' }} User @endslot
@endcomponent

@include('layout.flash-message')
<form action="{{route('users.add.action')}}" method="post" id="addEditUser">
    @csrf
    <input type="hidden" name="id" id="id" value="{{$data->id}}">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">User Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">                  
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">First Name</label>
                                <div class="col-lg-9">
                                    <input type="text" name="first_name" id="first_name" class="form-control" value="{{$data->first_name}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Email</label>
                                <div class="col-lg-9">
                                    <input type="email" name="email" id="email" class="form-control" value="{{$data->email}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Department</label>
                                <div class="col-lg-9">
                                    <select class="form-control dept_list" name="department_id" id="department_id">
                                        <option selected disabled>Select Department</option>
                                        @foreach($departments as $department)
                                        <option value="{{$department->id}}" {{$data->department_id === $department->id ? "selected" : ''}}>
                                            {{$department->name}}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label id="errorToShowDepartment"></label>
                                </div>
                            </div>
                            @if($data->id > 0)
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Password</label>
                                <div class="col-lg-9">
                                    <input type="password" name="user_password" id="user_password" class="form-control" value="">
                                </div>
                            </div>
                            @endif
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Status</label>
                                <div class="col-lg-9">
                                    <select class="select" name="is_status" id="is_status">
                                        <option>Select</option>
                                        <option value="1" {{ $data->id == 0 ? 'selected' : ($data->is_status === 1 ? "selected" : '')  }}>
                                            Active
                                        </option>
                                        <option value="0" {{$data->is_status === 0 ? "selected" : ''}}>
                                            Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Last Name</label>
                                <div class="col-lg-9">
                                    <input type="text" name="last_name" id="last_name" class="form-control" value="{{$data->last_name}}">
                                </div>
                            </div>  
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Phone Number</label>
                                <div class="col-lg-9">
                                    <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{$data->phone_number}}">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Role</label>
                                <div class="col-lg-9">
                                    <select class="form-control role_list" name="is_role" id="is_role">
                                        <option selected disabled>Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{$role->name}}" {{$data->id > 0 ? ($data->roles[0]->name == $role->name ? 'selected' : '') : ''}}   >
                                                {{$role->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label id="errorToShowRole"></label>
                                </div>
                            </div>
                            @if($data->id > 0)
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Confirm Password</label>
                                <div class="col-lg-9">
                                    <input type="password" name="user_confirm_password" id="user_confirm_password" class="form-control" value="">
                                </div>
                            </div>
                            @endif
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Login Attempts</label>
                                <div class="col-lg-9">
                                    <input type="number" name="failed_login_attempts" id="failed_login_attempts" class="form-control" value="{{$data->failed_login_attempts}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{route('users')}}" class="btn btn-dark">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/pages/users.js')}}"></script>
@endsection