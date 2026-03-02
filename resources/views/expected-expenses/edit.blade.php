<?php $page="add-clients";?>
@extends('layout.mainlayout')
@section('css')
    <link href="{{ URL::asset('public/assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
@component('components.breadcrumb')                
    @slot('title') Edit Expected Expense @endslot
    @slot('li_1') Dashboard @endslot
    @slot('li_2') Expected Expenses @endslot
    @slot('li_3') Edit Expected Expense @endslot
@endcomponent

@include('layout.flash-message')
<form action="{{route('expected-expenses.update')}}" method="post" id="editExpectedExpenseForm">
    @csrf
    <input type="hidden" name="id" id="id" value="{{$data->id}}">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Expected Expense Details ({{ $data->expected_expense_year }})</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="expectedExpenseListTable">
                            <thead>
                                <tr>
                                    <th class="purple-white" width="14%">Expense Title</th>
                                    <th class="purple-white text-center">Jul - {{$data_year[0]}}</th>
                                    <th class="purple-white text-center">Aug - {{$data_year[0]}}</th>
                                    <th class="purple-white text-center">Sep - {{$data_year[0]}}</th>
                                    <th class="purple-white text-center">Oct - {{$data_year[0]}}</th>
                                    <th class="purple-white text-center">Nov - {{$data_year[0]}}</th>
                                    <th class="purple-white text-center">Dec - {{$data_year[0]}}</th>
                                    <th class="purple-white text-center">Jan - {{$data_year[1]}}</th>
                                    <th class="purple-white text-center">Feb - {{$data_year[1]}}</th>
                                    <th class="purple-white text-center">Mar - {{$data_year[1]}}</th>
                                    <th class="purple-white text-center">Apr - {{$data_year[1]}}</th>
                                    <th class="purple-white text-center">May - {{$data_year[1]}}</th>
                                    <th class="purple-white text-center">Jun - {{$data_year[1]}}</th>
                                    <th class="purple-white text-center">Annual</th>
                                    <th class="purple-white text-center" width="1%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($data->epxected_expense_list->count() > 0)
                                    @foreach($data->epxected_expense_list as $expense_list)
                                    <tr>
                                        <input type="hidden" name="expected_expense_list_id[]" value="{{@$expense_list->id}}">
                                        <td>
                                            <input type="text" name="expected_expense_name[]" id="expected_expense_name" class="form-control expected_expense_name" 
                                            value="{{$expense_list->expected_expense_name}}">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_july_expense[]" id="expected_july_expense" class="form-control expected_july_expense" 
                                            value="{{$expense_list->expected_july_expense}}">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_aug_expense[]" id="expected_aug_expense" class="form-control expected_aug_expense" 
                                            value="{{$expense_list->expected_aug_expense}}">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_sept_expense[]" id="expected_sept_expense" class="form-control expected_sept_expense" 
                                            value="{{$expense_list->expected_sept_expense}}">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_oct_expense[]" id="expected_oct_expense" class="form-control expected_oct_expense" 
                                            value="{{$expense_list->expected_oct_expense}}">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_nov_expense[]" id="expected_nov_expense" class="form-control expected_nov_expense" 
                                            value="{{$expense_list->expected_nov_expense}}">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_desc_expense[]" id="expected_desc_expense" class="form-control expected_desc_expense" 
                                            value="{{$expense_list->expected_desc_expense}}">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_jan_expense[]" id="expected_jan_expense" class="form-control expected_jan_expense" 
                                            value="{{$expense_list->expected_jan_expense}}">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_feb_expense[]" id="expected_feb_expense" class="form-control expected_feb_expense" 
                                            value="{{$expense_list->expected_feb_expense}}">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_mar_expense[]" id="expected_mar_expense" class="form-control expected_mar_expense" 
                                            value="{{$expense_list->expected_mar_expense}}">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_apr_expense[]" id="expected_apr_expense" class="form-control expected_apr_expense" 
                                            value="{{$expense_list->expected_apr_expense}}">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_may_expense[]" id="expected_may_expense" class="form-control expected_may_expense" 
                                            value="{{$expense_list->expected_may_expense}}">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_june_expense[]" id="expected_june_expense" class="form-control expected_june_expense" 
                                            value="{{$expense_list->expected_june_expense}}">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_annual_expense[]" id="expected_annual_expense" class="form-control expected_annual_expense" 
                                            value="{{$expense_list->expected_annual_expense}}">
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a href="javascript:;" data-id="" class="btn btn-danger btn-xs removeRow" id="removeRow"><i class="fa fa-times"></i></a> 
                                        </td>
                                    </tr>
                                    <input type="hidden" name="another_id[]" value="{{@$expense_list->id}}">
                                    @endforeach
                                @else
                                    <tr>
                                        <td>
                                            <input type="text" name="expected_expense_name[]" id="expected_expense_name" class="form-control expected_expense_name" 
                                            value="">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_july_expense[]" id="expected_july_expense" class="form-control expected_july_expense" 
                                            value="">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_aug_expense[]" id="expected_aug_expense" class="form-control expected_aug_expense" 
                                            value="">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_sept_expense[]" id="expected_sept_expense" class="form-control expected_sept_expense" 
                                            value="">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_oct_expense[]" id="expected_oct_expense" class="form-control expected_oct_expense" 
                                            value="">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_nov_expense[]" id="expected_nov_expense" class="form-control expected_nov_expense" 
                                            value="">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_desc_expense[]" id="expected_desc_expense" class="form-control expected_desc_expense" 
                                            value="">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_jan_expense[]" id="expected_jan_expense" class="form-control expected_jan_expense" 
                                            value="">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_feb_expense[]" id="expected_feb_expense" class="form-control expected_feb_expense" 
                                            value="">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_mar_expense[]" id="expected_mar_expense" class="form-control expected_mar_expense" 
                                            value="">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_apr_expense[]" id="expected_apr_expense" class="form-control expected_apr_expense" 
                                            value="">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_may_expense[]" id="expected_may_expense" class="form-control expected_may_expense" 
                                            value="">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_june_expense[]" id="expected_june_expense" class="form-control expected_june_expense" 
                                            value="">
                                        </td>
                                        <td>
                                            <input type="text" name="expected_annual_expense[]" id="expected_annual_expense" class="form-control expected_annual_expense" 
                                            value="">
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a href="javascript:;" data-id="" class="btn btn-danger btn-xs removeRow" id="removeRow"><i class="fa fa-times"></i></a> 
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <br>
                        <button id="add_new_row" type="button" class="btn btn-secondary btn-sm">
                            <i class="fa fa-plus"></i> Add New Expense
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="{{route('expected-expenses')}}" class="btn btn-dark">Cancel</a>
    </div>
</form>
<table style="display:none;" id="hiddenExpectedExpenseListTable">
    <thead>
        <tr>
            <th width="14%"></th>
            <th width="6.5%">July</th>
            <th width="6.5%">Aug</th>
            <th width="6.5%">Sept</th>
            <th width="6.5%">Oct</th>
            <th width="6.5%">Nov</th>
            <th width="6.5%">Des</th>
            <th width="6.5%">Jan</th>
            <th width="6.5%">Feb</th>
            <th width="6.5%">Mar</th>
            <th width="6.5%">Apr</th>
            <th width="6.5%">May</th>
            <th width="6.5%">Jun</th>
            <th width="7%">Annual</th>
            <th width="1%"></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <input type="text" name="expected_expense_name[]" id="expected_expense_name" class="form-control expected_expense_name" 
                value="">
            </td>
            <td>
                <input type="text" name="expected_july_expense[]" id="expected_july_expense" class="form-control expected_july_expense" 
                value="">
            </td>
            <td>
                <input type="text" name="expected_aug_expense[]" id="expected_aug_expense" class="form-control expected_aug_expense" 
                value="">
            </td>
            <td>
                <input type="text" name="expected_sept_expense[]" id="expected_sept_expense" class="form-control expected_sept_expense" 
                value="">
            </td>
            <td>
                <input type="text" name="expected_oct_expense[]" id="expected_oct_expense" class="form-control expected_oct_expense" 
                value="">
            </td>
            <td>
                <input type="text" name="expected_nov_expense[]" id="expected_nov_expense" class="form-control expected_nov_expense" 
                value="">
            </td>
            <td>
                <input type="text" name="expected_desc_expense[]" id="expected_desc_expense" class="form-control expected_desc_expense" 
                value="">
            </td>
            <td>
                <input type="text" name="expected_jan_expense[]" id="expected_jan_expense" class="form-control expected_jan_expense" 
                value="">
            </td>
            <td>
                <input type="text" name="expected_feb_expense[]" id="expected_feb_expense" class="form-control expected_feb_expense" 
                value="">
            </td>
            <td>
                <input type="text" name="expected_mar_expense[]" id="expected_mar_expense" class="form-control expected_mar_expense" 
                value="">
            </td>
            <td>
                <input type="text" name="expected_apr_expense[]" id="expected_apr_expense" class="form-control expected_apr_expense" 
                value="">
            </td>
            <td>
                <input type="text" name="expected_may_expense[]" id="expected_may_expense" class="form-control expected_may_expense" 
                value="">
            </td>
            <td>
                <input type="text" name="expected_june_expense[]" id="expected_june_expense" class="form-control expected_june_expense" 
                value="">
            </td>
            <td>
                <input type="text" name="expected_annual_expense[]" id="expected_annual_expense" class="form-control expected_annual_expense" 
                value="">
            </td>
            <td>
                <a href="javascript:;" data-id="" class="btn btn-danger btn-xs removeRow" id="removeRow"><i class="fa fa-times"></i></a> 
            </td>
        </tr>
    </tbody>
</table>
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/pages/expected-expenses-add.js')}}"></script>
@endsection