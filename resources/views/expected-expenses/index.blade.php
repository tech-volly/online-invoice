<?php $page="expected-expenses";?>
@extends('layout.mainlayout')
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Expected Expenses</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Expected Expenses</li>
            </ul>
        </div>
        @can('expected-expense-create')
        <div class="col-auto float-end ms-auto">
            <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_expected_expense"><i class="fa fa-plus"></i> Add Expected Expense</a>
        </div>
        @endcan
    </div>
</div>   
@include('layout.flash-message')   
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        @can('expected-expense-delete')
        <div class="form-group form-focus datatableButtons">
            <button type="button" class="btn btn-danger exportbtn-custom" id="btnAllDelete"><i class="fa fa-trash-o"></i> Delete</button>
        </div>
        @endcan
    </div>
</div>           
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table mb-0 datatable" id="expectedExpensesDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th>Expense Year</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $key => $v)
                    <tr id="expectedexpenserow_{{$v->id}}">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="chk{{$v->id}}" class="custom-control-input cb-element" value="{{$v->id}}" aria-invalid="false"> 
                                <label for="chk{{$v->id}}"></label>
                            </div>
                        </td>
                        <td>{{ $v->expected_expense_year }}</td>
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
                                    <a class="dropdown-item" href="{{route('expected-expenses.clone', $v->id)}}" data-id="{{$v->id}}">
                                        <i class="fa fa-clone m-r-5"></i> Clone
                                    </a>
                                    @can('expected-expense-edit')
                                    <a class="dropdown-item" href="{{route('expected-expenses.edit', $v->id)}}"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                    @endcan
                                    @can('expected-expense-delete')
                                    <a class="dropdown-item deleteExpectedExpenseBtn" href="javascript:void(0)" data-id="{{$v->id}}">
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
<div class="modal custom-modal fade" id="add_expected_expense" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Expected Expense</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('expected-expenses.create')}}" id="addExpectedExpenseForm">
                    @csrf
                    <div class="form-group">
                        <label>Year <span class="text-danger">*</span></label>
                        <select class="select" name="expected_expense_year" id="expected_expense_year">
                            <option selected disabled>Select Year</option>
                            <option value="2021 - 2022">2021 - 2022</option>
                            <option value="2022 - 2023">2022 - 2023</option>
                            <option value="2023 - 2024">2023 - 2024</option>
                            <option value="2024 - 2025">2024 - 2025</option>
                            <option value="2025 - 2026">2025 - 2026</option>
                            <option value="2026 - 2027">2026 - 2027</option>
                            <option value="2027 - 2028">2027 - 2028</option>
                            <option value="2028 - 2029">2028 - 2029</option>
                            <option value="2029 - 2030">2029 - 2030</option>
                            <option value="2030 - 2031">2030 - 2031</option>
                        </select>
                        <label id="errorToShow"></label>
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
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/pages/expected-expenses.js')}}"></script>
@endsection