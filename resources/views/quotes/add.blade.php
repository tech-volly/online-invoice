<?php $page="add-quotes";?>
@extends('layout.mainlayout')
@section('content')
@component('components.breadcrumb')                
    @slot('title') {{ $data->id > 0 ? 'Edit' : 'Add' }} Quote @endslot
    @slot('li_1') Dashboard @endslot
    @slot('li_2') Quotes @endslot
    @slot('li_3') {{ $data->id > 0 ? 'Edit' : 'Add' }} Quote @endslot
@endcomponent

@include('layout.flash-message')
<form action="{{route('quotes.add.action')}}" method="post" id="addEditQuote">
    @csrf
    <input type="hidden" name="id" id="id" value="{{$data->id}}">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Quote Details</h4>
                </div>
                <div class="card-body">
                    @if($data->id > 0)
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Quote Number</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                    name="quote_number" id="quote_number" value="{{$data->quote_number}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Client</label>
                                <div class="col-lg-9">
                                    <select class="form-control client_list" name="client_id" id="client_id">
                                        <option selected disabled>Select Client</option>
                                        @foreach($clients as $client)
                                            <option value="{{$client->id}}"  data-client="{{$client->client_number}}"
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
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Quote Date</label>
                                <div class="col-lg-9">
                                    <div class="cal-icon">
										<input class="form-control floating quoteDatetimepicker" type="text" 
                                        name="quote_date" id="quote_date" value="{{$data->quote_date ? getDateDisplayFormat($data->quote_date) : ''}}">
									</div>
                                </div>
                            </div>
                        </div> 
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
                                <label class="col-lg-3 col-form-label">Discount (%)</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="quote_discount" id="quote_discount" 
                                    value="{{$data->quote_discount}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Quote Status</label>
                                <div class="col-lg-9">
                                    <select class="form-control payment_status_list" name="quote_payment_status" id="quote_payment_status">
                                        <option>Select Payment Status</option>
                                        <option value="Open" {{ $data->id == 0 ? 'selected' : ($data->quote_payment_status == 'Open' ? "selected" : '')  }}>
                                            Open
                                        </option>
                                        <option value="Approved" {{$data->quote_payment_status == 'Approved' ? "selected" : ''}}>
                                            Approved
                                        </option>
                                        <option value="Declined" {{$data->quote_payment_status == 'Declined' ? "selected" : ''}}>
                                            Declined
                                        </option>
                                    </select>
                                    <label id="errorToShowPaymentStatusId"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Email</label>
                                <div class="col-lg-9">
                                    <textarea rows="7" cols="5" class="form-control" name="quote_emails" id="quote_emails">{{$data->quote_emails}}</textarea>
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
                        <table class="table table-bordered mb-0" id="quoteProductTable">
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
                                @if($data->quote_payments->count() > 0)
                                    @foreach($data->quote_payments as $quote_payment)
                                    <tr id="productRow_1">
                                        <input type="hidden" name="quote_payment_id[]" value="{{@$quote_payment->id}}">
                                        <td>
                                            <select class="form-select product_id" name="product_id" id="product_id">
                                                <option selected disabled>Select Product</option>
                                                @foreach($products as $product)
                                                    <option value="{{$product->id}}" {{$quote_payment->product_id === $product->id ? 'selected' : ''}}>
                                                        {{ $product->product_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="hidden_prod_id[]" id="hidden_prod_id">
                                        </td>
                                        <td>
                                            <textarea rows="2" name="product_description" id="product_description" class="form-control">{{$quote_payment->product_description}}</textarea>
                                            <input type="hidden" name="hidden_product_description[]" id="hidden_product_description">
                                        </td>
                                        <td>
                                            <input type="text" name="product_unit_price" id="product_unit_price" class="form-control product_unit_price" 
                                            value="{{$quote_payment->product_unit_price}}">
                                            <input type="hidden" name="hidden_prod_unit_price[]" id="hidden_prod_unit_price">
                                        </td>
                                        <td>
                                            <select class="form-select tax_selection" name="tax_selection" id="tax_selection">
                                                <option selected disabled>Select Tax</option>
                                                <option value="GST Inclusive" {{$quote_payment->tax_selection == 'GST Inclusive' ? 'selected' : ''}}>
                                                    GST Inclusive
                                                </option>
                                                <option value="GST" {{$quote_payment->tax_selection == 'GST' ? 'selected' : ''}}>
                                                    GST
                                                </option>
                                                <option value="No GST" {{$quote_payment->tax_selection == 'No GST' ? 'selected' : ''}}>No GST</option>
                                            </select>
                                            <input type="hidden" name="hidden_prod_tax_sel[]" id="hidden_prod_tax_sel">
                                        </td>
                                        <td>
                                            <input type="number" min="1" name="product_quantity" id="product_quantity" class="form-control product_quantity" 
                                            value="{{$quote_payment->product_quantity}}">
                                            <input type="hidden" name="hidden_prod_quantity[]" id="hidden_prod_quantity">
                                        </td>
                                        <td>
                                            <input type="text" name="product_subtotal" id="product_subtotal" class="form-control" 
                                            value="$ {{$quote_payment->product_subtotal}}" readonly data-discount=0>
                                            <input type="hidden" name="hidden_product_subtotal[]" id="hidden_product_subtotal">
                                        </td>
                                        <td>
                                            <input type="text" name="product_gst" id="product_gst" class="form-control product_gst"
                                            value="$ {{$quote_payment->product_gst}}" data-gst=0>
                                            <input type="hidden" name="hidden_prod_gst[]" id="hidden_prod_gst">
                                        </td>
                                        <td>
                                            <input type="text" name="product_grand_total" id="product_grand_total" class="form-control" readonly
                                            value="$ {{$quote_payment->product_grand_total}}">
                                            <input type="hidden" name="hidden_product_grand_total[]" id="hidden_product_grand_total">
                                        </td>
                                        <td>
                                            <a href="javascript:;" data-id="" class="btn btn-danger btn-xs removeRow" id="removeRow"><i class="fa fa-times"></i></a> 
                                        </td>
                                    </tr>
                                    <input type="hidden" name="another_id[]" value="{{@$quote_payment->id}}">
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
                                        <input type="text" name="product_subtotal" id="product_subtotal" class="form-control" value="$ 0" readonly
                                        data-discount=0>
                                        <input type="hidden" name="hidden_product_subtotal[]" id="hidden_product_subtotal">
                                    </td>
                                    <td>
                                        <input type="text" name="product_gst" id="product_gst" class="form-control product_gst" value="$ 0" readonly
                                        data-gst=0>
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
                        <table class="table table-bordered quote-table" id="quoteTotalTable">
                            <tr class="quoteTotal">
                                <td width="5%">Items Total</td>
                                <td width="5%" id="itemTotal"></td>
                            </tr>
                            <tr id="discountDiv" style="display:none;">
                                <td width="5%" id="discountLabel"></td>
                                <td width="5%" id="discountAmount"></td>
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
                        <input type="hidden" name="quote_item_total" id="quote_item_total">
                        <input type="hidden" name="quote_grand_gst" id="quote_grand_gst">
                        <input type="hidden" name="quote_grand_total" id="quote_grand_total">
                        <input type="hidden" name="product_final_discount_amt" id="product_final_discount_amt">
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
    <div class="row">
        <div class="col-xl-12">
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Quote Notes</label>
                <div class="col-lg-12">
                    <textarea name="quote_master_notes" id="quote_master_notes" class="ckeditor form-control" rows="5">
                        {!! $data->id > 0 ? ($data->quote_master_notes ? $data->quote_master_notes : $estimate_setting->estimate_footer_notes) : $estimate_setting->estimate_footer_notes !!}
                    </textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="{{route('quotes')}}" class="btn btn-dark">Cancel</a>
    </div>
</form>
<table style="display:none;" id="hiddenTable">
    <tr class="productClass"  id="lastRowOfProduct">
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
            <input type="text" name="product_unit_price" id="product_unit_price" class="form-control" 
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
            <input type="text" name="product_subtotal" id="product_subtotal" class="form-control" value="$ 0" readonly data-discount=0>
            <input type="hidden" name="hidden_product_subtotal[]" id="hidden_product_subtotal">
        </td>
        <td>
            <input type="text" name="product_gst" id="product_gst" class="form-control product_gst" value="$ 0" data-gst=0>
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
<script src="{{ URL::asset('public/assets/js/pages/quote-payment.js')}}"></script>
<script src="{{ URL::asset('public/assets/libs/ckeditor/ckeditor.js')}}"></script>
<script type="text/javascript">
    //CKEDITOR
    ClassicEditor.create( document.querySelector( '.ckeditor' ), {
        height : '350px',
        toolbar: {
            items: ['heading', 'bold', 'italic', '|', 'undo', 'redo', '|', 'numberedList', 'bulletedList', 'blockQuote' ]
        },
        removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload', 'MediaEmbed', 'Table', 'TableToolbar']
    }).catch( error => {
        console.error( error );
    });
</script>
@endsection