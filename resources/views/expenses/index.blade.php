<?php $page = "expenses"; ?>
@extends('layout.mainlayout')
@section('css')
<link rel="stylesheet" href="{{URL::asset('public/assets/libs/magnific-popup/magnific-popup.css')}}" />
@endsection
@section('content')
@component('components.breadcrumb')
@slot('title') Expenses @endslot
@slot('li_1') Dashboard @endslot
@slot('li_2') Expenses @endslot
@endcomponent
@include('layout.flash-message')
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="form-group form-focus datatableButtons">
            <a href="{{route('expenses.export-expenses')}}" class="btn btn-primary exportbtn-custom">
                <i class="las la-file-export"></i>
                Export Expenses
            </a>
            <a class="btn btn-primary exportbtn-custom" data-bs-toggle="modal" data-bs-target="#import_expense">
                <i class="las la-file-import"></i>
                Import Expense
            </a>

            <a class="btn btn-success exportbtn-custom" data-bs-toggle="modal" data-bs-target="#import_expense_from_keys">
                <i class="las la-key"></i>
                Import from Keys
            </a>
            @can('expense-delete')
            <button type="button" class="btn btn-danger exportbtn-custom" id="btnAllDelete"><i class="fa fa-trash-o"></i> Delete</button>
            @endcan
        </div>
    </div>
</div>
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
                        <th>Business Name</th>
                        <th>Invoice Number</th>
                        <th>Project Name</th>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>GST</th>
                        <th>Payment Method</th>
                        <th>Expense Category</th>
                        <th></th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Import Expense Model -->
<div class="modal custom-modal fade" id="import_expense" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Expense</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('expenses.import-expenses')}}" id="importExpenseForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Import File <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="import_expense_file" id="import_expense_file">
                    </div>
                    <div class="form-group">
                        <a class="active"
                            href="{{route('expenses.export-sample-file')}}">
                            <i class="fa fa-download"></i> Download Sample File
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

<div class="modal custom-modal fade" id="import_expense_from_keys" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="las la-key"></i> Import Expense from Keys</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('expenses.import-from-keys.review')}}" id="importExpenseFromKeysForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Upload CSV File <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="import_keys_csv" id="import_keys_csv" accept=".csv">
                        <small class="text-muted">CSV must have columns: <strong>Date, Description, Amount</strong></small>
                    </div>
                   
                    <div class="alert alert-info" style="font-size:13px;">
                        <i class="fa fa-info-circle"></i> 
                        The system will read the <strong>Description</strong> column from your CSV and match keywords 
                        against your <strong>Expense Keys</strong>. You'll review and confirm each row before saving.
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-success">
                            <i class="las la-search"></i> Process & Review
                        </button>
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
<script src="{{ URL::asset('public/assets/js/pages/expenses.js')}}"></script>
@endsection