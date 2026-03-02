<?php $page="resources";?>
@extends('layout.mainlayout')
@section('content')
    @component('components.breadcrumb')                
        @slot('title') Resources @endslot
        @slot('li_1') Dashboard @endslot
        @slot('li_2') Resources @endslot
    @endcomponent
    @include('layout.flash-message')
@can('resource-delete')
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="form-group form-focus datatableButtons">
            <button type="button" class="btn btn-danger exportbtn-custom" id="btnAllDelete"><i class="fa fa-trash-o"></i> Delete</button>
        </div>
    </div>
</div>
@endcan
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table datatable" id="resourceDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th>Name</th>
                        <th>Status</th>
                        <th class="text-center"></th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $key => $v)
                    <tr id="resourcerow_{{$v->id}}">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="chk{{$v->id}}" class="custom-control-input cb-element" value="{{$v->id}}" aria-invalid="false"> 
                                <label for="chk{{$v->id}}"></label>
                            </div>
                        </td>
                        <td>{{ $v->resource_name }}</td>
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
                            <?php 
                                $first_resource = getFirstResource($v->id);
                            ?>
                            @if($first_resource)
                                <?php
                                    $extension = explode(".",$first_resource->resource_image);
                                ?>                
                                <h2 class="table-avatar">
                                    @if($first_resource->resource_image)
                                        @if($extension[1] == 'pdf')
                                            <a target="_blank" href="{{URL::asset('public/uploads/invoice-resources/'.$v->id.'/'.$first_resource->resource_image)}}" class="btn btn-primary">
                                                <i class="fa fa-file-pdf-o m-r-5" style="color:white;"></i>
                                            </a>
                                        @else
                                            <a class="btn btn-primary" download="{{$first_resource->resource_image}}"
                                                href="{{URL::asset('public/uploads/invoice-resources/'.$v->id.'/'.$first_resource->resource_image)}}">
                                                <i class="fa fa-download" style="color:white;"></i>
                                            </a>
                                        @endif
                                    @else
                                    <a href="{{URL::asset('public/assets/img/profiles/avatar-01.jpg')}}" class="avatar brand-custom image-link">
                                        <img src="{{URL::asset('public/assets/img/profiles/avatar-01.jpg')}}" alt="" style="height: 45px;width: 45px;">
                                    </a>
                                    @endif
                                </h2>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="dropdown dropdown-action">
                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    @can('resource-edit')
                                    <a class="dropdown-item" href="{{route('invoice-resources.edit', $v->id)}}">
                                        <i class="fa fa-pencil m-r-5"></i> Edit
                                    </a>
                                    @endcan
                                    @can('resource-delete')
                                    <a class="dropdown-item deleteResourceBtn" href="javascript:void(0)" data-id="{{$v->id}}">
                                        <i class="fa fa-trash-o m-r-5"></i> Delete
                                    </a>
                                    @endcan
                                    <?php 
                                        $path = public_path('uploads/invoice-resources/'.$v->id);
                                    ?>
                                    @if(File::isDirectory($path))
                                    <a class="dropdown-item" href="{{route('invoice-resources.download',$v->id)}}">
                                        <i class="fa fa-download m-r-5"></i> Download zip
                                    </a>
                                    @endif
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
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/pages/resources.js')}}"></script>
@endsection