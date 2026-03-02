<?php $page="add-products";?>
@extends('layout.mainlayout')
@section('css')
    <link href="{{ URL::asset('public/assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
@component('components.breadcrumb')                
    @slot('title') {{ $data->id > 0 ? 'Edit' : 'Add' }} Product @endslot
    @slot('li_1') Dashboard @endslot
    @slot('li_2') Products @endslot
    @slot('li_3') {{ $data->id > 0 ? 'Edit' : 'Add' }} Product @endslot
@endcomponent

@include('layout.flash-message')
<form action="{{route('products.add.action')}}" method="post" id="addEditProduct" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" id="id" value="{{$data->id}}">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Product Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Product Name</label>
                                <div class="col-lg-9">
                                    <input type="text" name="product_name" id="product_name" class="form-control" value="{{$data->product_name}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Category</label>
                                <div class="col-lg-9">
                                    <select class="form-control product_category" name="category_id" id="category_id">
                                        <option>Select Category</option>
                                        @foreach($categories as $category)
                                        <option value="{{$category->id}}" {{$data->category_id === $category->id ? "selected" : ''}}>
                                            {{$category->name}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row" id="retailPriceDiv">
                                <label class="col-lg-3 col-form-label">Price</label>
                                <div class="col-lg-9">
                                    <div class="input-group">
                                    <span class="input-group-text" id="basic-addon2">$</span>
                                    <input type="text" name="product_price" id="product_price" class="form-control" value="{{$data->product_price}}">
                                    </div>
                                    <label id="errorToShow"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Tax</label>
                                <div class="col-lg-9">
                                    <select class="form-control tax_list" name="product_tax" id="product_tax">
                                        <option>Select Tax</option>
                                        <option value="GST Inclusive" {{$data->product_tax === 'GST Inclusive' ? "selected" : ''}}>
                                            GST Inclusive
                                        </option>
                                        <option value="GST" {{$data->product_tax === 'GST' ? "selected" : ''}}>
                                            GST
                                        </option>
                                        <option value="No GST" {{$data->product_tax === 'No GST' ? "selected" : ''}}>
                                            No GST
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Purchase Price</label>
                                <div class="col-lg-9">
                                    <div class="input-group">
                                    <span class="input-group-text" id="basic-addon2">$</span>
                                    <input type="text" name="product_purchase_price" id="product_purchase_price" class="form-control" value="{{$data->product_purchase_price}}" step=".01">
                                    </div>
                                    <label id="errorToShow"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Margin</label>
                                <div class="col-lg-9">
                                    <div class="input-group">
                                    <span class="input-group-text" id="basic-addon2">$</span>
                                    <input type="text" name="product_margin" id="product_margin" class="form-control" value="{{$data->product_margin}}" readonly="">
                                    </div>
                                    <label id="errorToShow"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Description</label>
                                <div class="col-lg-9">
                                    <textarea rows="5" cols="5" class="form-control" name="product_description" id="product_description" placeholder="Description">{{$data->product_description}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Product Image</label>
                                <div class="col-lg-9">
									<input class="form-control" type="file" name="product_image" id="product_image" 
                                    value="{{$data->product_image}}" accept="image/*">
                                    <input type="hidden" name="product_image_old" value="{{$data->product_image}}">
                                </div>
                            </div>
                        </div>
                        @if($data->product_image)
                        <div class="col-xl-6" id="productImageDiv">
                            <div class="img-thumbnail rounded float-start">
                                <img src="{{URL::asset('public/uploads/products/'.$data->id.'/'.$data->product_image)}}" style="height: 125px;"/>
                            </div>
                            <a class="remove_ex_exp_approver btn rounded border text-danger removeProductImage" data-id="{{$data->id}}">
                                <i class="fa fa-trash text-danger" aria-hidden="true"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Status</label>
                                <div class="col-lg-9">
                                    <select class="select" name="is_status" id="is_status">
                                        <option>Select Status</option>
                                        <option value="1" {{ $data->id == 0 ? 'selected' : ($data->is_status === 1 ? "selected" : '')  }}>
                                            Active
                                        </option>
                                        <option value="0" {{$data->is_status === 0 ? "selected" : ''}}>
                                            Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">&nbsp;&nbsp;</label>
                                <div class="col-lg-9">
                                    <img id="previewImg" class="preview-image" style="display:none;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="{{route('products')}}" class="btn btn-dark">Cancel</a>
    </div>
</form>
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/pages/product-add.js')}}"></script>
@endsection