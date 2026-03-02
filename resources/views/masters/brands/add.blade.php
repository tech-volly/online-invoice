<?php $page="add-brands";?>
@extends('layout.mainlayout')
@section('content')
@component('components.breadcrumb')                
    @slot('title') {{ $data->id > 0 ? 'Edit' : 'Add' }} Brand @endslot
    @slot('li_1') Dashboard @endslot
    @slot('li_2') Brands @endslot
    @slot('li_3') {{ $data->id > 0 ? 'Edit' : 'Add' }} Brand @endslot
@endcomponent

@include('layout.flash-message')
<form action="{{route('brands.add.action')}}" method="post" id="addEditBrand" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" id="id" value="{{$data->id}}">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Brand Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Brand Name</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                    name="name" id="name" value="{{$data->name}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Status</label>
                                <div class="col-lg-9">
                                    <select class="select" name="is_status" id="is_status">
                                        <option>Select</option>
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
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Brand Logo</label>
                                <div class="col-lg-9">
									<input class="form-control" type="file" name="image" id="image" 
                                    value="{{$data->image}}">
                                    <input type="hidden" name="brand_image_old" id="brand_image_old" value="{{$data->image}}">
                                </div>
                            </div>
                        </div>
                        @if($data->image)
                        <div class="col-xl-6" id="attachedImageDiv">
                            <div class="row">
                                <div class="col-lg-3">
                                    <img src="{{URL::asset('public/uploads/brands/'.$data->id.'/'.$data->image)}}" style="height: 100px;"/>
                                </div>
                                <div class="col-md-2">
                                    <a class="remove_ex_exp_approver btn rounded border text-danger removeBrandImage" data-id="{{$data->id}}">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
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
        <a href="{{route('brands')}}" class="btn btn-dark">Cancel</a>
    </div>
</form>
@endsection
@section('script')
<script src="{{URL::asset('public/assets/libs/magnific-popup/jquery.magnific-popup.js')}}"></script>
<script src="{{ URL::asset('public/assets/js/pages/masters/brands.js')}}"></script>
@endsection