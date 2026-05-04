<?php $page = "expenses"; ?>
@extends('layout.mainlayout')
@section('css')
<link rel="stylesheet" href="{{URL::asset('public/assets/libs/magnific-popup/magnific-popup.css')}}" />
@endsection
@section('content')
@component('components.breadcrumb')
@slot('title') ExpenseKeys @endslot
@slot('li_1') Dashboard @endslot
@slot('li_2') ExpenseKeys @endslot
@endcomponent

<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Expenses Keys</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Expenses Keys</li>
            </ul>
        </div>
        @can('expected-expense-create')
        <div class="col-auto float-end ms-auto">
            <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_expected_expense"><i class="fa fa-plus"></i> Add Expense Keys</a>
        </div>
        @endcan
    </div>
</div>
@include('layout.flash-message')


<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table datatable" id="expenseDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th>Keys</th>
                        <th>Expense Category</th>
                        <th></th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
               

                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal custom-modal fade" id="add_expected_expense" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Expense Key</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('expense-keys.store') }}" id="addExpectedExpenseForm">
                    @csrf
                    <div class="form-group">
                        <label class="col-lg-3 col-form-label">Keys</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" aria-describedby="basic-addon2"
                                name="key" id="key">

                        </div>
                    </div>
                    <div class="form-group">
                        <label>Expense Category <span class="text-danger">*</span></label>
                        <select class="select" name="category_id" id="category_id">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                            @endforeach
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
<script src="{{URL::asset('public/assets/libs/magnific-popup/jquery.magnific-popup.js')}}"></script>
<script src="{{ URL::asset('public/assets/js/pages/expense-keys.js')}}"></script>
@endsection