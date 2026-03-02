<?php $page="add-invoice-resources";?>
@extends('layout.mainlayout')
@section('css')
    <link rel="stylesheet" href="{{URL::asset('public/assets/libs/magnific-popup/magnific-popup.css')}}"/> 
@endsection
@section('content')
@component('components.breadcrumb')                
    @slot('title') {{ $data->id > 0 ? 'Edit' : 'Add' }} Resource @endslot
    @slot('li_1') Dashboard @endslot
    @slot('li_2') Resources @endslot
    @slot('li_3') {{ $data->id > 0 ? 'Edit' : 'Add' }} Resource @endslot
@endcomponent

@include('layout.flash-message')
<form action="{{route('invoice-resources.add.action')}}" method="post" id="addEditResource" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" id="id" value="{{$data->id}}">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Resource Details</h4>
                </div>
                <div class="card-body">
                    <div class="row" id="client_type_business_row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Resource Name</label>
                                <div class="col-lg-9">
                                    <input type="text" name="resource_name" id="resource_name" class="form-control" value="{{$data->resource_name}}">
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
                                <label class="col-lg-3 col-form-label">Resource Images</label>
                                <div class="col-lg-9">
                                    <input class="form-control" type="file" name="resource_image[]" multiple>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($data->invoice_resource_images->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Resource Images</h4>
                </div>
                <div class="card-body">
                    @foreach($data->invoice_resource_images->chunk(4) as $resources)
                    <div class="row">
                        @foreach($resources as $image)
                        <?php 
                            $extension = explode(".",$image->resource_image);
                        ?>
                        <div class="col-xl-3 col-lg-3 col-md-4 col-12 mb-4" id="imageDiv_{{$image->id}}">
                            <div class="resource-images">
                                @if($extension[1] == 'png' || $extension[1] == 'jpg' || $extension[1] == 'jpeg' || $extension[1] == 'heic')
                                    <a href="{{URL::asset('public/uploads/invoice-resources/'.$image->invoice_resource_id.'/'.$image->resource_image)}}" class="image-link">
                                        <img src="{{URL::asset('public/uploads/invoice-resources/'.$image->invoice_resource_id.'/'.$image->resource_image)}}" 
                                        class="img-fluid">
                                    </a>
                                @elseif($extension[1] == 'pdf')
                                    <a href="{{URL::asset('public/uploads/invoice-resources/'.$image->invoice_resource_id.'/'.$image->resource_image)}}" target="_blank">
                                        <img src="{{URL::asset('public/assets/img/PDF.jpg')}}" class="img-fluid">
                                    </a>
                                @elseif($extension[1] == 'docx' || $extension[1] == 'doc' || $extension[1] == 'odt')
                                    <a download="{{$image->resource_image}}" href="{{URL::asset('public/uploads/invoice-resources/'.$image->invoice_resource_id.'/'.$image->resource_image)}}" target="_blank">
                                        <img src="{{URL::asset('public/assets/img/word.jpg')}}" class="img-fluid">
                                    </a>
                                @elseif($extension[1] == 'xlsx' || $extension[1] == 'csv')
                                    <a download="{{$image->resource_image}}" href="{{URL::asset('public/uploads/invoice-resources/'.$image->invoice_resource_id.'/'.$image->resource_image)}}" target="_blank">
                                        <img src="{{URL::asset('public/assets/img/xl.jpg')}}" class="img-fluid">
                                    </a>
                                @else
                                    <img src="{{URL::asset('public/uploads/invoice-resources/'.$image->invoice_resource_id.'/'.$image->resource_image)}}" 
                                    class="img-fluid">
                                @endif
                                <div class="icon-style">
                                    <i class="fa fa-trash deleteResourceImage" aria-hidden="true" data-id="{{$image->id}}" style="cursor: pointer;"></i>    
                                    <a download="{{$image->resource_image}}" href="{{URL::asset('public/uploads/invoice-resources/'.$image->invoice_resource_id.'/'.$image->resource_image)}}">
                                        <i class="fa fa-download" aria-hidden="true" data-id="{{$image->id}}"></i> 
                                    </a>
                                </div>
                            </div>    
                        </div>      
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="{{route('invoice-resources')}}" class="btn btn-dark">Cancel</a>
    </div>
</form>
@endsection
@section('script')
<script src="{{URL::asset('public/assets/libs/magnific-popup/jquery.magnific-popup.js')}}"></script>
<script src="{{ URL::asset('public/assets/js/pages/resources.js')}}"></script>
<script type="text/javascript">
    $('.image-link').magnificPopup({
        type:'image',
        closeOnContentClick: true,
		mainClass: 'mfp-img-mobile',
		image: {
			verticalFit: true
		}
    });
</script>
@endsection