<?php $page="products";?>
@extends('layout.mainlayout')
@section('css')
    <link rel="stylesheet" href="{{URL::asset('public/assets/libs/magnific-popup/magnific-popup.css')}}"/> 
@endsection
@section('content')
    @component('components.breadcrumb')                
        @slot('title') Products @endslot
        @slot('li_1') Dashboard @endslot
        @slot('li_2') Products @endslot
    @endcomponent
    @include('layout.flash-message')
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="form-group form-focus datatableButtons">
            <a class="btn btn-primary exportbtn-custom" href="{{route('products.export-products')}}">
                <i class="las la-file-export"></i>
                Export Products
            </a>
            <a class="btn btn-primary exportbtn-custom" data-bs-toggle="modal" data-bs-target="#import_products">
                <i class="las la-file-import"></i>
                Import Products
            </a>
            @can('product-delete')
                <button type="button" class="btn btn-danger exportbtn-custom" id="btnAllDelete"><i class="fa fa-trash-o"></i> Delete</button>
            @endcan
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table datatable" id="productDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th></th>
                        <th>Product Name</th>
                        <th>Product Category</th>
                        <th>Product Price</th>
                        <th>Purchase Price</th>
                        <th>Margin</th>
                        <th>Product Tax</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $key => $v)
                    <tr id="productrow_{{$v->id}}">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="chk{{$v->id}}" class="custom-control-input cb-element" value="{{$v->id}}" aria-invalid="false"> 
                                <label for="chk{{$v->id}}"></label>
                            </div>
                        </td>
                        <td>
                            <h2 class="table-avatar">
                                @if($v->product_image)
                                <a href="{{URL::asset('public/uploads/products/'.$v->id.'/'.$v->product_image)}}" class="avatar brand-custom image-link">
                                    <img src="{{URL::asset('public/uploads/products/'.$v->id.'/'.$v->product_image)}}" alt="" style="height: 45px;width: 45px;">
                                </a>
                                @else
                                <a href="{{URL::asset('public/assets/img/profiles/avatar-01.jpg')}}" class="avatar brand-custom image-link">
                                    <img src="{{URL::asset('public/assets/img/profiles/avatar-01.jpg')}}" alt="" style="height: 45px;width: 45px;">
                                </a>
                                @endif
                            </h2>
                        </td>
                        <td>{{ $v->product_name }}</td>
                        <td>{{ categoryName($v->category_id) }}</td>
                        <td>{{ getPrice($v->product_price) }}</td>
                        <td>{{ getPrice($v->product_purchase_price) }}</td>
                        <td>{{ getPrice($v->product_margin) }}</td>
                        <td>{{ $v->product_tax }}</td>
                        <td>
                            @if($v->is_status == 1)
                                <span class="btn btn-white btn-sm btn-rounded">
                                    <i class="fa fa-dot-circle-o text-success"></i> Active 
                                </span>
                            @else
                                <span class="btn btn-white btn-sm btn-rounded">
                                    <i class="fa fa-dot-circle-o text-danger"></i> Inactive 
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="dropdown dropdown-action">
                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    @can('product-edit')
                                    <a class="dropdown-item" href="{{route('products.edit', $v->id)}}">
                                        <i class="fa fa-pencil m-r-5"></i> Edit
                                    </a>
                                    @endcan
                                    @can('product-delete')
                                    <a class="dropdown-item deleteProductBtn" href="javascript:void(0)" data-id="{{$v->id}}">
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
<!-- Import Products Model -->
<div class="modal custom-modal fade" id="import_products" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Products</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('products.import-products')}}" id="importProductForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Import File <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="import_products_file" id="import_products_file">
                    </div>
                    <div class="form-group">
                        <a class="active" download="Products - Sheet1.csv"
                            href="{{URL::asset('public/uploads/samples/Products - Sheet1.csv')}}">
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
<script src="{{URL::asset('public/assets/libs/magnific-popup/jquery.magnific-popup.js')}}"></script>
<script src="{{ URL::asset('public/assets/js/pages/products.js')}}"></script>
@endsection