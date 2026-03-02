<?php $page="add-expenses";?>
@extends('layout.mainlayout')
@section('css')
    <link rel="stylesheet" href="{{URL::asset('public/assets/libs/magnific-popup/magnific-popup.css')}}"/> 
@endsection
@section('content')
@component('components.breadcrumb')                
    @slot('title') {{ $data->id > 0 ? 'Edit' : 'Add' }} Expense @endslot
    @slot('li_1') Dashboard @endslot
    @slot('li_2') Expenses @endslot
    @slot('li_3') {{ $data->id > 0 ? 'Edit' : 'Add' }} Expense @endslot
@endcomponent

@include('layout.flash-message')
<form action="{{route('expenses.add.action')}}" method="post" id="addEditExpense" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" id="id" value="{{$data->id}}">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Expense Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Supplier Invoice Number</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                    name="supplier_invoice_number" id="supplier_invoice_number" value="{{$data->supplier_invoice_number}}">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Payment Method</label>
                                <div class="col-lg-9">
                                    <select class="form-control payment_method_list" name="payment_method_id" id="payment_method_id">
                                        <option>Select Payment Method</option>
                                        @foreach($payment_methods as $payment_method)
                                        <option value="{{$payment_method->id}}" {{$data->payment_method_id === $payment_method->id ? "selected" : ''}}>
                                            {{$payment_method->payment_method_name}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Supplier</label>
                                <div class="col-lg-9">
                                    <select class="form-control supplier_list" name="supplier_id" id="supplier_id">
                                        <option selected disabled>Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{$supplier->id}}" {{$data->supplier_id === $supplier->id ? "selected" : ''}}>
                                            {{$supplier->supplier_business_name}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Supplier Categories</label>
                                <div class="col-lg-9">
                                    <input type="hidden" id="suppilier_sel_cat" value="{{$data->supplier_expense_category ? $data->supplier_expense_category : '' }}">
                                    <select class="form-control supplier_category_list" name="supplier_expense_category" id="supplier_expense_category">
                                        <option>Select Category</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Tax</label>
                                <div class="col-lg-9">
                                    <select class="form-control tax_selection" name="expense_tax" id="expense_tax">
                                        <option>Select Tax</option>
                                        <option value="GST Inclusive" {{ $data->id == 0 ? 'selected' : ($data->expense_tax === 'GST Inclusive' ? "selected" : '')}}>
                                            GST Inclusive
                                        </option>
                                        <option value="No GST" {{$data->expense_tax === 'No GST' ? "selected" : ''}}>
                                            No GST
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Amount</label>
                                <div class="col-lg-9">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon2">$</span>
                                        <input type="text" class="form-control" placeholder="0.00" aria-describedby="basic-addon2"
                                        name="expense_amount" id="expense_amount" value="{{$data->expense_amount}}">
                                    </div>
                                    <label id="errorToShow"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Payment Date</label>
                                <div class="col-lg-9">
                                    <div class="cal-icon">
										<input class="form-control floating datetimepicker" type="text" 
                                        name="expense_date" id="expense_date" value="{{$data->expense_date ? getDateDisplayFormat($data->expense_date) : ''}}">
									</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Description</label>
                                <div class="col-lg-9">
                                    <textarea rows="5" cols="5" class="form-control" name="expense_description" id="expense_description" placeholder="Description">{{$data->expense_description}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Project</label>
                                <div class="col-lg-9">
                                    <select class="form-control project_list" name="project_id" id="project_id">
                                        <option >Select Project</option>
                                        @foreach($projects as $project)
                                        <option value="{{$project->id}}" {{$data->project_id === $project->id ? "selected" : ''}}>
                                            {{$project->name}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Description</label>
                                <div class="col-lg-9">
                                    <textarea rows="5" cols="5" class="form-control" name="expense_description" id="expense_description" placeholder="Description">{{$data->expense_description}}</textarea>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Attached Receipt</label>
                                <div class="col-lg-9">
									<input class="form-control" type="file" name="expense_attached_receipt" id="expense_attached_receipt" 
                                    value="{{$data->expense_attached_receipt}}">
                                    <input type="hidden" name="expense_attached_receipt_old" id="expense_attached_receipt_old" value="{{$data->expense_attached_receipt}}">
                                </div>
                            </div>
                        </div>
                        @if($data->expense_attached_receipt)
                        <?php 
                            $extension = explode(".",$data->expense_attached_receipt);
                        ?>
                        <div class="col-xl-6" id="attachedImageDiv">
                            <div class="row">
                                @if($extension[1] == 'png' || $extension[1] == 'jpg' || $extension[1] == 'jpeg' || $extension[1] == 'heic')
                                <div class="col-lg-3">
                                    <a href="{{URL::asset('public/uploads/expenses/'.$data->id.'/'.$data->expense_attached_receipt)}}" class="image-link">
                                        <img src="{{URL::asset('public/uploads/expenses/'.$data->id.'/'.$data->expense_attached_receipt)}}" style="height: 100px;"/>
                                    </a>
                                </div>
                                @elseif($extension[1] == 'pdf')
                                <div class="col-lg-3">
                                    <a href="{{URL::asset('public/uploads/expenses/'.$data->id.'/'.$data->expense_attached_receipt)}}" class="btn btn-primary"
                                        target="_blank">
                                        <i class="fa fa-file-pdf-o" style="color:white;"></i> View PDF
                                    </a>
                                </div>
                                @else
                                <div class="col-lg-3">
                                    <a href="{{URL::asset('public/uploads/expenses/'.$data->id.'/'.$data->expense_attached_receipt)}}" class="btn btn-primary"
                                        download="{{$data->expense_attached_receipt}}">
                                        <i class="fa fa-download" style="color:white;"></i> Download
                                    </a>
                                </div>
                                @endif
                                <div class="col-md-2">
                                    <a class="remove_ex_exp_approver btn rounded border text-danger removeAttachedImage" data-id="{{$data->id}}">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="{{route('expenses')}}" class="btn btn-dark">Cancel</a>
    </div>
</form>
@endsection
@section('script')
<script src="{{URL::asset('public/assets/libs/magnific-popup/jquery.magnific-popup.js')}}"></script>
<script src="{{ URL::asset('public/assets/js/pages/expense-add.js')}}"></script>
@endsection