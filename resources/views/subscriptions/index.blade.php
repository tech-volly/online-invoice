<?php $page="clients";?>
@extends('layout.mainlayout')
@section('content')
    @component('components.breadcrumb')                
        @slot('title') Subscriptions @endslot
        @slot('li_1') Dashboard @endslot
        @slot('li_2') Subscriptions @endslot
    @endcomponent
    @include('layout.flash-message')
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="form-group form-focus datatableButtons">
            <a class="btn btn-primary exportbtn-custom" href="{{route('subscriptions.export-subscriptions')}}">
                <i class="las la-file-export"></i>
                Export Subscriptions
            </a>
            @can('subscription-delete')
            <button type="button" class="btn btn-danger exportbtn-custom" id="btnAllDelete"><i class="fa fa-trash-o"></i> Delete</button>
            @endcan
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table datatable" id="subscriptionDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th>Name</th>
                        <th>Client Name</th>
                        <th>Cycle</th>
                        <th>Start Date</th>
                        <th>Next Date</th>
                        <th>Generated</th>
                        <th>% Increase</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $key => $v)
                    <tr id="subscriptionrow_{{$v->id}}">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="chk{{$v->id}}" class="custom-control-input cb-element" value="{{$v->id}}" aria-invalid="false"> 
                                <label for="chk{{$v->id}}"></label>
                            </div>
                        </td>
                        <td>{{$v->subscription_name}}</td>
                        <td>{{$v->client->client_business_name}}</td>
                        <td>{{Str::ucfirst($v->subscription_cycle)}}</td>
                        <td>{{getFormatedDate($v->subscription_start_date)}}</td>
                        <td>{{getFormatedDate($v->subscription_next_date)}}</td>
                        <td>{{getGeneratedSubscriptionCount($v->id)}}</td>
                        <td>{{$v->is_subscription_next_increment ? $v->subscription_incremented_percentage : 'N/A'}}</td>
                        <td>{{getPrice(getNextAmountForSubscription($v->id))}}</td>
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
                                    @can('subscription-edit')
                                    <a class="dropdown-item" href="{{route('subscriptions.edit', $v->id)}}">
                                        <i class="fa fa-pencil m-r-5"></i> Edit
                                    </a>
                                    @endcan
                                    @can('subscription-delete')
                                    <a class="dropdown-item deleteSubscriptionBtn" href="javascript:void(0)" data-id="{{$v->id}}">
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
<script src="{{ URL::asset('public/assets/js/pages/subscriptions.js')}}"></script>
@endsection