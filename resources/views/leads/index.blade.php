<?php $page="leads";?>
@extends('layout.mainlayout')
@section('content')
    @component('components.breadcrumb')                
        @slot('title') Leads @endslot
        @slot('li_1') Dashboard @endslot
        @slot('li_2') Leads @endslot
    @endcomponent
    @include('layout.flash-message')
@can('lead-delete')
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="form-group form-focus datatableButtons">
            <a class="btn btn-primary exportbtn-custom" href="{{route('leads.export-leads')}}">
                <i class="las la-file-export"></i>
                Export Leads
            </a>
            <button type="button" class="btn btn-danger exportbtn-custom" id="btnAllDelete"><i class="fa fa-trash-o"></i> Delete</button>
        </div>
    </div>
</div>
@endcan
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table datatable" id="leadDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th>Name</th>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Discussion Date</th>
                        <th>FollowUp</th>
                        <th>Enquiry Date</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $key => $v)
                    <tr id="leadrow_{{$v->id}}">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="chk{{$v->id}}" class="custom-control-input cb-element" value="{{$v->id}}" aria-invalid="false"> 
                                <label for="chk{{$v->id}}"></label>
                            </div>
                        </td>
                        <td>{{ $v->client_first_name }} {{ $v->client_last_name }}</td>
                        <td>{{ $v->company_name }}</td>
                        <td>{{ $v->client_email }}</td>
                        <td>{{ $v->client_mobile }}</td>
                        <td>{{ getLeadDiscussionDate($v->id) }}</td>
                        <td>{{ getLeadFollowUpDetails($v->id) }}</td>
                        <td>{{ getFormatedDate($v->created_at) }}</td>
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
                                    <a class="dropdown-item viewfollowUpDetails" href="javascript:;" data-id="{{$v->id}}" data-toggle="modal" data-target="#leadFollowUpDetails" ><i class="fa fa-eye m-r-5"></i>
                                        FollowUp Details
                                    </a>
                                    @can('lead-edit')
                                    <a class="dropdown-item" href="{{route('leads.edit', $v->id)}}">
                                        <i class="fa fa-pencil m-r-5"></i> Edit
                                    </a>
                                    @endcan
                                    @can('lead-delete')
                                    <a class="dropdown-item deleteLeadBtn" href="javascript:void(0)" data-id="{{$v->id}}">
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
<div class="modal custom-modal fade" id="view_followup_details" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">FollowUp Details</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="response"></div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/pages/leads.js')}}"></script>
@endsection