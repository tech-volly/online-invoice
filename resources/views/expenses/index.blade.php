<?php $page="expenses";?>
@extends('layout.mainlayout')
@section('css')
    <link rel="stylesheet" href="{{URL::asset('public/assets/libs/magnific-popup/magnific-popup.css')}}"/> 
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
                    @foreach($data as $key => $v)
                    <tr id="expenserow_{{$v->id}}">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="chk{{$v->id}}" class="custom-control-input cb-element" value="{{$v->id}}" aria-invalid="false"> 
                                <label for="chk{{$v->id}}"></label>
                            </div>
                        </td>
                        <td>{{ $v->supplier ? $v->supplier->supplier_business_name : '' }}</td>
                        <td>{{ $v->supplier_invoice_number }}</td>
                        <td>{{ $v->project_name }}</td>
                        <td>{{ getFormatedDate($v->expense_date) }}</td>
                        <td>{{ getPrice($v->expense_amount) }}</td>
                        <td>{{ getGstPriceForExpense($v->expense_tax, $v->expense_amount) }}</td>
                        <td>{{ getPaymentMethodName($v->payment_method_id) }}</td>
                        <td>{{ getExpenseCategory($v->supplier_expense_category) }}</td>
                        <td>
                            <h2 class="table-avatar">
                                <?php 
                                    $extension = explode(".",$v->expense_attached_receipt);
                                ?>
                                @if($v->expense_attached_receipt)
                                    @if($extension[1] == 'png' || $extension[1] == 'jpg' || $extension[1] == 'jpeg' || $extension[1] == 'heic')
                                        <a href="{{URL::asset('public/uploads/expenses/'.$v->id.'/'.$v->expense_attached_receipt)}}" class="avatar brand-custom image-link">
                                            <img src="{{URL::asset('public/uploads/expenses/'.$v->id.'/'.$v->expense_attached_receipt)}}" alt="" style="height: 45px;width: 45px;">
                                        </a>
                                    @elseif($extension[1] == 'pdf')
                                        <a target="_blank" href="{{URL::asset('public/uploads/expenses/'.$v->id.'/'.$v->expense_attached_receipt)}}" class="btn btn-primary">
                                            <i class="fa fa-file-pdf-o m-r-5" style="color:white;"></i>
                                        </a>
                                    @else
                                        <a class="btn btn-primary" download="{{$v->expense_attached_receipt}}"
                                            href="{{URL::asset('public/uploads/expenses/'.$v->id.'/'.$v->expense_attached_receipt)}}">
                                            <i class="fa fa-download" style="color:white;"></i>
                                        </a>
                                    @endif
                                @else
                                <a href="{{URL::asset('public/assets/img/profiles/avatar-01.jpg')}}" class="avatar brand-custom image-link">
                                    <img src="{{URL::asset('public/assets/img/profiles/avatar-01.jpg')}}" alt="" style="height: 45px;width: 45px;">
                                </a>
                                @endif
                            </h2>
                        </td>
                        <td class="text-center">
                            <div class="dropdown dropdown-action">
                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    @can('expense-edit')
                                    <a class="dropdown-item" href="{{route('expenses.edit', $v->id)}}">
                                        <i class="fa fa-pencil m-r-5"></i> Edit
                                    </a>
                                    @endcan
                                    @can('expense-delete')
                                    <a class="dropdown-item deleteExpenseBtn" href="javascript:void(0)" data-id="{{$v->id}}">
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
@endsection
@section('script')
<script src="{{URL::asset('public/assets/libs/magnific-popup/jquery.magnific-popup.js')}}"></script>
<script src="{{ URL::asset('public/assets/js/pages/expenses.js')}}"></script>
@endsection