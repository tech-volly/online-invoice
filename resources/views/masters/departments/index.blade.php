<?php $page="departments";?>
@extends('layout.mainlayout')
@section('css')
    <link rel="stylesheet" href="{{url('public/assets/css/dataTables.bootstrap4.min.css')}}"> 
@endsection
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Departments</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Departments</li>
            </ul>
        </div>
        @can('department-create')
        <div class="col-auto float-end ms-auto">
            <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_departments"><i class="fa fa-plus"></i> Add Department</a>
        </div>
        @endcan
    </div>
</div>   
@include('layout.flash-message') 
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="form-group form-focus datatableButtons">
            <a class="btn btn-primary exportbtn-custom" data-bs-toggle="modal" data-bs-target="#import_departments">
                <i class="las la-file-import"></i>
                Import Departments
            </a>
            @can('department-delete')  
                <button type="button" class="btn btn-danger exportbtn-custom" id="btnAllDelete"><i class="fa fa-trash-o"></i> Delete</button>
            @endcan           
        </div>
    </div>
</div>   
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table mb-0 datatable" id="departmentDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th>Department Name </th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $key => $v)
                    <tr id="departmentrow_{{$v->id}}">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="chk{{$v->id}}" class="custom-control-input cb-element" value="{{$v->id}}" aria-invalid="false"> 
                                <label for="chk{{$v->id}}"></label>
                            </div>
                        </td>
                        <td>{{ $v->name }}</td>
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
                            <div class="dropdown dropdown-action">
                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    @can('department-edit')
                                    <a class="dropdown-item editDepartment" href="javascript:void(0)" data-id="{{$v->id}}"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                    @endcan
                                    @can('department-delete')
                                    <a class="dropdown-item deleteDepartmentBtn" href="javascript:void(0)" data-id="{{$v->id}}">
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
<div class="modal custom-modal fade" id="add_departments" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Department</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('departments.create')}}" id="addDepartmentForm">
                    @csrf
                    <div class="form-group">
                        <label>Department Name <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="department_name" id="department_name">
                    </div>
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select class="select" name="is_status" id="is_status">
                            <option>Select</option>
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
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
<div class="modal custom-modal fade" id="edit_departments" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Department</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('departments.update')}}" id="editDepartmentForm">
                    @csrf
                    <input type="hidden" name="department_id" value="" id="department_id">
                    <div class="form-group">
                        <label>Category Name <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="department_name" id="update_department_name">
                    </div>
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <!-- <div class="isstatus"></div> -->
                        <select class="select" name="is_status" id="update_is_status">
                            <option>Select</option>
                        </select>
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
<div class="modal custom-modal fade" id="import_departments" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Departments</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('departments.import-departments')}}" id="importDepartmentForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Import File <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="import_department_file" id="import_department_file">
                    </div>
                    <div class="form-group">
                        <a class="active" download="Departments - Sheet1.csv"
                            href="{{URL::asset('public/uploads/samples/Departments - Sheet1.csv')}}">
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
<script src="{{ URL::asset('public/assets/js/pages/masters/departments.js')}}"></script>
@endsection