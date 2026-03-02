<?php $page="roles";?>
@extends('layout.mainlayout')
@section('content')    
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Roles & Permissions</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Roles & Permissions</li>
            </ul>
        </div>
        @can('role-create')
        <div class="col-auto float-end ms-auto">
            <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_role"><i class="fa fa-plus"></i> Add Roles</a>
        </div>
        @endcan
    </div>
</div>      
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="form-group form-focus datatableButtons">
            @can('role-delete')
            <button type="button" class="btn btn-danger exportbtn-custom" id="btnAllDelete"><i class="fa fa-trash-o"></i> Delete</button>
            @endcan
        </div>
    </div>
</div>
@include('layout.flash-message')
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table datatable" id="rolesDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th>Role</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $key => $v)
                    <tr id="rolerow_{{$v->id}}">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="chk{{$v->id}}" class="custom-control-input cb-element" value="{{$v->id}}" aria-invalid="false"> 
                                <label for="chk{{$v->id}}"></label>
                            </div>
                        </td>
                        <td>{{$v->name}}</td>
                        <td class="text-center">
                            <div class="dropdown dropdown-action">
                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    @can('role-edit')
                                    <a class="dropdown-item editRole" href="javascript:;" data-id="{{$v->id}}">
                                        <i class="fa fa-pencil m-r-5"></i> Edit
                                    </a>
                                    @endcan
                                    @can('role-delete')
                                        <a class="dropdown-item deleteRole" href="javascript:void(0)" data-id="{{$v->id}}">
                                            <i class="fa fa-trash-o m-r-5"></i> Delete
                                        </a>
                                    @endcan
                                </div>
                            </div>
                           
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>   
<div id="add_role" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Role</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('roles.create')}}" id="addRoleForm">
                    @csrf
                    <div class="form-group">
                        <label>Role Name <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="name">
                    </div>
                    <div class="form-group">
                        <label>Permissions <span class="text-danger">*</span></label>
                        @foreach($permission->chunk(4) as $chunk)
                            <div class="row table table-striped">
                                @foreach($chunk as $p)
                                <div class="col-3">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" value="{{$p->id}}" name="permission[]"> 
                                            {{$p->name}}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endforeach
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
<div id="edit_role" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Role</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('roles.update')}}" id="editRoleForm">
                    @csrf
                    <input type="hidden" name="role_id" value="" id="role_id">
                    <div class="form-group">
                        <label>Role Name <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="name" value=""  id="name">
                    </div>
                    <div class="form-group">
                        <label>Permissions <span class="text-danger">*</span></label>
                        <div class="permissions"></div>
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
<script src="{{ URL::asset('public/assets/js/pages/roles.js')}}"></script>
@endsection