<?php $page="brands";?>
@extends('layout.mainlayout')
@section('css')
    <link rel="stylesheet" href="{{URL::asset('public/assets/libs/magnific-popup/magnific-popup.css')}}"/>
@endsection
@section('content')
    @component('components.breadcrumb')                
        @slot('title') Brands @endslot
        @slot('li_1') Dashboard @endslot
        @slot('li_2') Brands @endslot
    @endcomponent
    @include('layout.flash-message')
@can('brand-delete')
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
            <table class="table table-striped custom-table datatable" id="brandDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th>Logo</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $key => $v)
                    <tr id="brandrow_{{$v->id}}">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="chk{{$v->id}}" class="custom-control-input cb-element" value="{{$v->id}}" aria-invalid="false"> 
                                <label for="chk{{$v->id}}"></label>
                            </div>
                        </td>
                        <td>
                            <h2 class="table-avatar">
                                <a href="{{URL::asset('public/uploads/brands/'.$v->id.'/'.$v->image)}}" class="avatar brand-custom image-link">
                                    <img src="{{URL::asset('public/uploads/brands/'.$v->id.'/'.$v->image)}}" alt="" style="height: 45px;width: 45px;">
                                </a>
                            </h2>
                        </td>
                        <td>{{$v->name}}</td>
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
                                    @can('brand-edit')
                                    <a class="dropdown-item" href="{{route('brands.edit', $v->id)}}"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                    @endcan
                                    @can('brand-delete')
                                    <a class="dropdown-item deleteBrandBtn" href="javascript:void(0)" data-id="{{$v->id}}">
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
@endsection
@section('script')
<script src="{{URL::asset('public/assets/libs/magnific-popup/jquery.magnific-popup.js')}}"></script>
<script src="{{ URL::asset('public/assets/js/pages/masters/brands.js')}}"></script>
@endsection