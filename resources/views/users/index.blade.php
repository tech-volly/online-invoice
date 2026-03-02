<?php $page="users";?>
@extends('layout.mainlayout')
@section('css')
    <link rel="stylesheet" href="{{url('public/assets/css/dataTables.bootstrap4.min.css')}}"> 
@endsection
@section('content')
    @component('components.breadcrumb')                
        @slot('title') Users @endslot
        @slot('li_1') Dashboard @endslot
        @slot('li_2') Users @endslot
    @endcomponent
    @include('layout.flash-message')
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="form-group form-focus datatableButtons">
            <a class="btn btn-primary exportbtn-custom" href="{{route('users.export-users')}}">
                <i class="las la-file-export"></i>
                Export Users
            </a>
            <a class="btn btn-primary exportbtn-custom" data-bs-toggle="modal" data-bs-target="#import_users">
                <i class="las la-file-import"></i>
                Import Users
            </a>
            @can('user-delete')
            <button type="button" class="btn btn-danger exportbtn-custom" id="btnAllDelete"><i class="fa fa-trash-o"></i> Delete</button>
            @endcan
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table datatable" id="usersDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $key => $v)
                    <tr id="userrow_{{$v->id}}">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="chk{{$v->id}}" class="custom-control-input cb-element" value="{{$v->id}}" aria-invalid="false"> 
                                <label for="chk{{$v->id}}"></label>
                            </div>
                        </td>
                        <td>{{ $v->first_name }} {{ $v->last_name }}</td>
                        <td>{{ $v->email }}</td>
                        <td>{{ $v->department ? $v->department->name : ''}}</td>
                        <td>
                            @if(!empty($v->getRoleNames()))
                                @foreach($v->getRoleNames() as $v1)
                                    {{$v1}}
                                @endforeach
                            @endif
                        </td>
                        <td>{{ $v->phone_number }}</td>
                        <td>
                            @if($v->is_status == 1)
                                <span class="btn btn-white btn-sm btn-rounded">
                                    <i class="fa fa-dot-circle-o text-success"></i> Active 
                                </span>
                            @else
                                <span class="btn btn-white btn-sm btn-rounded">
                                    <i class="fa fa-dot-circle-o text-danger"></i> Inactive 
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(!in_array('Super Admin', $v->roles->pluck('name')->toArray() ))
                            <div class="dropdown dropdown-action">
                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    @can('user-edit')
                                    <a class="dropdown-item" href="{{route('users.edit', $v->id)}}">
                                        <i class="fa fa-pencil m-r-5"></i> Edit
                                    </a>
                                    @endcan
                                    @can('user-delete')
                                        @if(Auth::user()->id != $v->id)
                                        <a class="dropdown-item deleteUserBtn" href="javascript:void(0)" data-id="{{$v->id}}">
                                            <i class="fa fa-trash-o m-r-5"></i> Delete
                                        </a>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>    
<!-- Import Users Model -->
<div class="modal custom-modal fade" id="import_users" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Users</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('users.import-users')}}" id="importUserForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Import File <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="import_users_file" id="import_users_file">
                    </div>
                    <div class="form-group">
                        <a class="active" download="Users - Sheet1.csv"
                            href="{{URL::asset('public/uploads/samples/Users - Sheet1.csv')}}">
                            <i class="fa fa-download"></i> Download Sample CSV
                        </a>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a type="button" data-bs-dismiss="modal" class="btn btn-dark">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/pages/users.js')}}"></script>
@endsection