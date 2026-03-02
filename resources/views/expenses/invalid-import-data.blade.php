<?php $page="expenses";?>
@extends('layout.mainlayout')
@section('css')
    <link rel="stylesheet" href="{{URL::asset('public/assets/libs/magnific-popup/magnific-popup.css')}}"/> 
@endsection
@section('content')
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    You have imported incorrect data
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="form-group form-focus datatableButtons">
            <a href="{{route('expenses')}}" class="btn btn-primary exportbtn-custom">
                <i class="las la-arrow-left"></i>
                Back to Import
            </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table datatable" id="invalidImportDatatable">
                <thead>
                    <tr>
                        <th>RowID</th>
                        <th>Supplier Invoice Number</th>
                        <th>Payment Method</th>
                        <th>Supplier</th>
                        <th>Supplier Expense Category</th>
                        <th>Tax Type</th>
                        <th>Amount</th>
                        <th>Payment Date</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invalid_arr as $key => $v)
                    <tr>
                        <td>{{ $v['row_id'] }}</td>
                        <td>{{ $v['supplier_invoice_number'] ? $v['supplier_invoice_number'] : '-' }}</td>
                        <td>{{ $v['select_payment_method'] ? $v['select_payment_method'] : '-' }}</td>
                        <td>{{ $v['select_supplier'] ? $v['select_supplier'] : '-' }}</td>
                        <td>{{ $v['select_supplier_category'] ? $v['select_supplier_category'] : '-' }}</td>
                        <td>{{ $v['select_tax'] ? $v['select_tax'] : '-' }}</td>
                        <td>{{ getPrice($v['amount']) ? getPrice($v['amount']) : '-' }}</td>
                        <td>{{ $v['payment_date'] ? getFormatedDate(chnageDateFormat($v['payment_date'])) : '-' }}</td>
                        <td>{{ $v['description'] ? $v['description'] : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>    
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/pages/expenses.js')}}"></script>
@endsection