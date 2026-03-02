<?php $page="clients";?>
@extends('layout.mainlayout')
@section('content')
    @component('components.breadcrumb')                
        @slot('title') Pending Expense @endslot
        @slot('li_1') Dashboard @endslot
        @slot('li_2') Pending Expense @endslot
    @endcomponent
    @include('layout.flash-message')
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="form-group form-focus datatableButtons">
            <a class="btn btn-primary exportbtn-custom" data-bs-toggle="modal" data-bs-target="#import_pending_expense">
                <i class="las la-file-import"></i>
                Import Pending Expense
            </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table datatable" id="pendingExpenseDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Expense Category</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $key => $v)
                    <tr id="pendingexpenserow_{{$v->id}}">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="chk{{$v->id}}" class="custom-control-input cb-element" value="{{$v->id}}" aria-invalid="false"> 
                                <label for="chk{{$v->id}}"></label>
                            </div>
                        </td>
                        <td class="expense_description" data-description="{{ $v->expense_description }}">{{ $v->expense_description }}</td>
                        <td class="expense_amount" data-amount="{{ $v->expense_amount }}">{{ getPrice($v->expense_amount) }}</td>
                        <td class="expense_date" data-date="{{ $v->expense_date }}">{{ getFormatedDate($v->expense_date) }}</td>
                        <td>
                            <select class="form-control supplier_list" name="supplier_id" id="supplier_id">
                                <option selected disabled>Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{$supplier->id}}">
                                    {{$supplier->supplier_business_name}}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                        <select class="form-control exp_category_list" name="supplier_expense_category" id="supplier_expense_category">
                            <option selected disabled>Select Expense Category</option>
                        </select>
                        </td>
                        <td class="text-center">
                            <a class="btn btn-primary btn-sm confirmExpenseBtn" href="javascript:void(0)" data-id="{{$v->id}}">
                                <i class="fa fa-check" aria-hidden="true"></i> Confirm
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Import Clients Modal -->
<div class="modal custom-modal fade" id="import_pending_expense" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Pending Expense</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('pending-expense.import-pending-expense')}}" id="importPendingExpenseForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Import File <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="import_pending_expense_file" id="import_pending_expense_file">
                    </div>
                    <div class="form-group">
                        <a class="active" download="Pending Expenses - Sheet1.csv"
                            href="{{URL::asset('public/uploads/samples/Pending Expenses - Sheet1.csv')}}">
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
<script src="{{ URL::asset('public/assets/js/pages/pending-expenses.js')}}"></script>
@endsection