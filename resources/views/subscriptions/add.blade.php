<?php $page = "add-subscriptions"; ?>
@extends('layout.mainlayout')
@section('content')
@component('components.breadcrumb')
@slot('title') {{ $data->id > 0 ? 'Edit' : 'Add' }} Subscription @endslot
@slot('li_1') Dashboard @endslot
@slot('li_2') Subscriptions @endslot
@slot('li_3') {{ $data->id > 0 ? 'Edit' : 'Add' }} Subscription @endslot
@endcomponent

@include('layout.flash-message')
<form action="{{route('subscriptions.add.action')}}" method="post" id="addEditSubscription">
    @csrf
    <input type="hidden" name="id" id="id" value="{{$data->id}}">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Subscription Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Subscription Name</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                        name="subscription_name" id="subscription_name"
                                        value="{{$data->subscription_name}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Subscription Cycle</label>
                                <div class="col-lg-9">
                                    <select class="form-control subscription_cycle" name="subscription_cycle" id="subscription_cycle">
                                        <option selected disabled>Select</option>
                                        <option value="daily" {{$data->subscription_cycle == 'daily' ? 'selected' : ''}}>
                                            Daily
                                        </option>
                                        <option value="weekly" {{$data->subscription_cycle == 'weekly' ? 'selected' : ''}}>
                                            Weekly
                                        </option>
                                        <option value="monthly" {{$data->subscription_cycle == 'monthly' ? 'selected' : ''}}>
                                            Monthly
                                        </option>
                                        <option value="quaterly" {{$data->subscription_cycle == 'quaterly' ? 'selected' : ''}}>
                                            Quaterly
                                        </option>
                                        <option value="yearly" {{$data->subscription_cycle == 'yearly' ? 'selected' : ''}}>
                                            Yearly
                                        </option>
                                    </select>
                                    <label id="errorToShowCycle"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-4">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Invoice Date</label>
                                <div class="col-lg-9">
                                    <div class="cal-icon">
                                        <input class="form-control floating subscriptionDatetimepicker" type="text"
                                            name="subscription_start_date" id="subscription_start_date"
                                            value="{{$data->subscription_start_date ? getDateDisplayFormat($data->subscription_start_date) : ''}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                       
                        <div class="col-xl-4">
                            <div class="form-group row">
                                <div class="col-lg-9">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="is_subscription_next_increment" id="is_subscription_next_increment"
                                                {{$data->is_subscription_next_increment == 1 ? 'checked' : ''}}>
                                            &nbsp;&nbsp;(%) increment in next cycle?
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="recurring_next_increment_section" style="display:none;">
                        <div class="col-xl-4">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">(%) increment in next cycle</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                        name="subscription_incremented_percentage" id="subscription_incremented_percentage"
                                        value="{{$data->is_subscription_next_increment ? $data->subscription_incremented_percentage : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Brand</label>
                                <div class="col-lg-9">
                                    <select class="form-control brand_list" name="brand_id" id="brand_id">
                                        <option selected disabled>Select Brand</option>
                                        @foreach($brands as $brand)
                                        <option value="{{$brand->id}}" {{$data->brand_id === $brand->id ? 'selected' : ''}}>
                                            {{$brand->name}}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label id="errorToShowBrandId"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Client</label>
                                <div class="col-lg-9">
                                    <select class="form-control client_list" name="client_id" id="client_id">
                                        <option selected disabled>Select Client</option>
                                        @foreach($clients as $client)
                                        <option value="{{$client->id}}" data-client="{{$client->client_number}}"
                                            {{$data->client_id === $client->id ? 'selected' : ''}}>
                                            {{ $client->client_business_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label id="errorToShow"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Client Number</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="client_number" id="client_number" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-4">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Payment Terms</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="subscription_payment_terms" id="subscription_payment_terms"
                                        value="{{$data->subscription_payment_terms}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Subscription Due Date</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="subscription_due_date" id="subscription_due_date"
                                        value="{{$data->subscription_due_date ? getDateDisplayFormat($data->subscription_due_date) : ''}}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">End Date</label>
                                <div class="col-lg-9">
                                    <div class="cal-icon">
                                        <input class="form-control floating subscriptionDatetimepicker" type="text"
                                            name="subscription_next_date" id="subscription_end_date"
                                            value="{{$data->subscription_next_date ? getDateDisplayFormat($data->subscription_next_date) : ''}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Product Total</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="subscriptionProductTable">
                            <thead>
                                <tr>
                                    <th width="12%">Product</th>
                                    <th width="35%">Description</th>
                                    <th width="8%">Unit Price</th>
                                    <th width="10%">Tax Selection</th>
                                    <th width="7%">Quantity</th>
                                    <th width="9%">Item Total</th>
                                    <th width="9%">GST</th>
                                    <th width="9%">Sub Total</th>
                                    <th width="1%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($data->subscription_payments->count() > 0)
                                @foreach($data->subscription_payments as $subscription_payment)
                                <tr id="productRow_1">
                                    <input type="hidden" name="subscription_payment_id[]" value="{{@$subscription_payment->id}}">
                                    <td>
                                        <select class="form-select product_id" name="product_id" id="product_id">
                                            <option selected disabled>Select Product</option>
                                            @foreach($products as $product)
                                            <option value="{{$product->id}}" {{$subscription_payment->product_id === $product->id ? 'selected' : ''}}>
                                                {{ $product->product_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="hidden_prod_id[]" id="hidden_prod_id">
                                    </td>
                                    <td>
                                        <textarea rows="2" name="product_description" id="product_description" class="form-control">{{$subscription_payment->product_description}}</textarea>
                                        <input type="hidden" name="hidden_product_description[]" id="hidden_product_description">
                                    </td>
                                    <td>
                                        <input type="text" name="product_unit_price" id="product_unit_price" class="form-control product_unit_price"
                                            value="{{$subscription_payment->product_unit_price}}">
                                        <input type="hidden" name="hidden_prod_unit_price[]" id="hidden_prod_unit_price">
                                    </td>
                                    <td>
                                        <select class="form-select tax_selection" name="tax_selection" id="tax_selection">
                                            <option selected disabled>Select Tax</option>
                                            <option value="GST Inclusive" {{$subscription_payment->tax_selection == 'GST Inclusive' ? 'selected' : ''}}>
                                                GST Inclusive
                                            </option>
                                            <option value="GST" {{$subscription_payment->tax_selection == 'GST' ? 'selected' : ''}}>
                                                GST
                                            </option>
                                            <option value="No GST" {{$subscription_payment->tax_selection == 'No GST' ? 'selected' : ''}}>No GST</option>
                                        </select>
                                        <input type="hidden" name="hidden_prod_tax_sel[]" id="hidden_prod_tax_sel">
                                    </td>
                                    <td>
                                        <input type="number" min="1" name="product_quantity" id="product_quantity" class="form-control product_quantity"
                                            value="{{$subscription_payment->product_quantity}}">
                                        <input type="hidden" name="hidden_prod_quantity[]" id="hidden_prod_quantity">
                                    </td>
                                    <td>
                                        <input type="text" name="product_subtotal" id="product_subtotal" class="form-control"
                                            value="$ {{$subscription_payment->product_subtotal}}" readonly>
                                        <input type="hidden" name="hidden_product_subtotal[]" id="hidden_product_subtotal">
                                    </td>
                                    <td>
                                        <input type="text" name="product_gst" id="product_gst" class="form-control product_gst"
                                            value="$ {{$subscription_payment->product_gst}}">
                                        <input type="hidden" name="hidden_prod_gst[]" id="hidden_prod_gst">
                                    </td>
                                    <td>
                                        <input type="text" name="product_grand_total" id="product_grand_total" class="form-control" readonly
                                            value="$ {{$subscription_payment->product_grand_total}}">
                                        <input type="hidden" name="hidden_product_grand_total[]" id="hidden_product_grand_total">
                                    </td>
                                    <td>
                                        <a href="javascript:;" data-id="" class="btn btn-danger btn-xs removeRow" id="removeRow"><i class="fa fa-times"></i></a>
                                    </td>
                                </tr>
                                <input type="hidden" name="another_id[]" value="{{@$subscription_payment->id}}">
                                @endforeach
                                @else
                                <tr id="productRow_1" class="productClass">
                                    <td>
                                        <select class="form-select product_id" name="product_id" id="product_id">
                                            <option selected disabled>Select Product</option>
                                            @foreach($products as $product)
                                            <option value="{{$product->id}}" {{$data->product_id === $product->id ? 'selected' : ''}}>
                                                {{ $product->product_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="hidden_prod_id[]" id="hidden_prod_id">
                                    </td>
                                    <td>
                                        <textarea rows="2" name="product_description" id="product_description" class="form-control"></textarea>
                                        <input type="hidden" name="hidden_product_description[]" id="hidden_product_description">
                                    </td>
                                    <td>
                                        <input type="text" name="product_unit_price" id="product_unit_price" class="form-control product_unit_price"
                                            value="">
                                        <input type="hidden" name="hidden_prod_unit_price[]" id="hidden_prod_unit_price">
                                    </td>
                                    <td>
                                        <select class="form-select tax_selection" name="tax_selection" id="tax_selection">
                                            <option selected disabled>Select Tax</option>
                                            <option value="GST Inclusive">GST Inclusive</option>
                                            <option value="GST">GST</option>
                                            <option value="No GST">No GST</option>
                                        </select>
                                        <input type="hidden" name="hidden_prod_tax_sel[]" id="hidden_prod_tax_sel">
                                    </td>
                                    <td>
                                        <input type="number" min="1" name="product_quantity" id="product_quantity" class="form-control product_quantity" value="">
                                        <input type="hidden" name="hidden_prod_quantity[]" id="hidden_prod_quantity">
                                    </td>
                                    <td>
                                        <input type="text" name="product_subtotal" id="product_subtotal" class="form-control" value="$ 0" readonly>
                                        <input type="hidden" name="hidden_product_subtotal[]" id="hidden_product_subtotal">
                                    </td>
                                    <td>
                                        <input type="text" name="product_gst" id="product_gst" class="form-control product_gst" value="$ 0" readonly>
                                        <input type="hidden" name="hidden_prod_gst[]" id="hidden_prod_gst">
                                    </td>
                                    <td>
                                        <input type="text" name="product_grand_total" id="product_grand_total" class="form-control" readonly
                                            value="$ 0">
                                        <input type="hidden" name="hidden_product_grand_total[]" id="hidden_product_grand_total">
                                    </td>
                                    <td>
                                        <a href="javascript:;" data-id="" class="btn btn-danger btn-xs removeRow" id="removeRow"><i class="fa fa-times"></i></a>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        <table class="table table-bordered subscription-table" id="subscriptionTotalTable">
                            <tr class="subscriptionTotal">
                                <td width="5%">Items Total</td>
                                <td width="5%" id="itemTotal"></td>
                            </tr>
                            <tr>
                                <td width="5%">GST</td>
                                <td width="5%" id="exclusiveAmt"></td>
                            </tr>
                            <tr>
                                <td width="5%">Grand Total</td>
                                <td width="5%" id="finalTotal"></td>
                            </tr>
                        </table>
                        <input type="hidden" name="subscription_grand_item_total" id="subscription_grand_item_total">
                        <input type="hidden" name="subscription_grand_gst" id="subscription_grand_gst">
                        <input type="hidden" name="product_final_total" id="product_final_total">
                        <input type="hidden" name="product_final_round_off" id="product_final_round_off">
                        <br>
                        <button id="add_new_row" type="button" class="btn btn-secondary btn-sm">
                            <i class="fa fa-plus"></i> Add New Item
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="{{route('subscriptions')}}" class="btn btn-dark">Cancel</a>
    </div>
</form>
<table style="display:none;" id="hiddenTable">
    <tr class="productClass" id="lastRowOfProduct">
        <td>
            <select class="form-select product_id" name="product_id" id="product_id">
                <option selected disabled>Select Product</option>
                @foreach($products as $product)
                <option value="{{$product->id}}" {{$data->product_id === $product->id ? 'selected' : ''}}>
                    {{ $product->product_name }}
                </option>
                @endforeach
            </select>
            <input type="hidden" name="hidden_prod_id[]" id="hidden_prod_id">
        </td>
        <td>
            <textarea rows="2" name="product_description" id="product_description" class="form-control"></textarea>
            <input type="hidden" name="hidden_product_description[]" id="hidden_product_description">
        </td>
        <td>
            <input type="text" name="product_unit_price" id="product_unit_price" class="form-control product_unit_price"
                value="">
            <input type="hidden" name="hidden_prod_unit_price[]" id="hidden_prod_unit_price">
        </td>
        <td>
            <select class="form-select tax_selection" name="tax_selection" id="tax_selection">
                <option selected disabled>Select Tax</option>
                <option value="GST Inclusive">GST Inclusive</option>
                <option value="GST">GST</option>
                <option value="No GST">No GST</option>
            </select>
            <input type="hidden" name="hidden_prod_tax_sel[]" id="hidden_prod_tax_sel">
        </td>
        <td>
            <input type="number" min="1" name="product_quantity" id="product_quantity" class="form-control product_quantity" value="">
            <input type="hidden" name="hidden_prod_quantity[]" id="hidden_prod_quantity">
        </td>
        <td>
            <input type="text" name="product_subtotal" id="product_subtotal" class="form-control" value="$ 0" readonly>
            <input type="hidden" name="hidden_product_subtotal[]" id="hidden_product_subtotal">
        </td>
        <td>
            <input type="text" name="product_gst" id="product_gst" class="form-control product_gst" value="$ 0">
            <input type="hidden" name="hidden_prod_gst[]" id="hidden_prod_gst">
        </td>
        <td>
            <input type="text" name="product_grand_total" id="product_grand_total" class="form-control" readonly
                value="$ 0">
            <input type="hidden" name="hidden_product_grand_total[]" id="hidden_product_grand_total">
        </td>
        <td>
            <a href="javascript:;" data-id="" class="btn btn-danger btn-xs removeRow" id="removeRow"><i class="fa fa-times"></i></a>
        </td>
    </tr>
</table>
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/pages/subscription-payment.js')}}"></script>
@endsection