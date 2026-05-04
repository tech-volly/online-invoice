<?php $page = "clients"; ?>
@extends('layout.mainlayout')
@section('content')
@component('components.breadcrumb')
@slot('title') Clients @endslot
@slot('li_1') Dashboard @endslot
@slot('li_2') Clients @endslot
@endcomponent
@include('layout.flash-message')
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="form-group form-focus datatableButtons">
            <a class="btn btn-primary exportbtn-custom" href="{{route('clients.export-clients')}}">
                <i class="las la-file-export"></i>
                Export Clients
            </a>
            <a class="btn btn-primary exportbtn-custom" data-bs-toggle="modal" data-bs-target="#import_clients">
                <i class="las la-file-import"></i>
                Import Clients
            </a>
            <a class="btn btn-primary exportbtn-custom" data-bs-toggle="modal" data-bs-target="#import_temp_clients">
                <i class="las la-file-import"></i>
                Import Temporary Clients
            </a>
            @can('client-delete')
            <button type="button" class="btn btn-danger exportbtn-custom" id="btnAllDelete"><i class="fa fa-trash-o"></i> Delete</button>
            @endcan
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table datatable" id="clientDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th>Name</th>
                        <th>Invoice Email</th>
                        <th>Quotes Email</th>
                        <th>Statement Email</th>
                        <th>Mobile</th>
                        <th>Reminder Days</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $key => $v)
                    <tr id="clientrow_{{$v->id}}">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="chk{{$v->id}}" class="custom-control-input cb-element" value="{{$v->id}}" aria-invalid="false">
                                <label for="chk{{$v->id}}"></label>
                            </div>
                        </td>
                        <td>{{ $v->client_business_name }}</td>
                        <td>{{ $v->client_email }}</td>
                        <td>{{ $v->client_quotes_email }}</td>
                        <td>{{ $v->client_statement_email }}</td>
                        <td>{{ $v->client_mobile }}</td>
                        <td>{{ $v->reminder_day ?? 15 }}</td>
                        <td>
                            @if($v->is_status == 1)
                            <span class="btn btn-white btn-sm btn-rounded">
                                <i class="fa fa-dot-circle-o text-success"></i> Active
                            </span>
                            <!-- <span class="badge bg-inverse-success">Active</span> -->
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
                                    @can('client-edit')
                                    <a class="dropdown-item" href="{{route('clients.edit', $v->id)}}">
                                        <i class="fa fa-pencil m-r-5"></i> Edit
                                    </a>
                                    @endcan
                                    @can('client-delete')
                                    <a class="dropdown-item deleteClientBtn" href="javascript:void(0)" data-id="{{$v->id}}">
                                        <i class="fa fa-trash-o m-r-5"></i> Delete
                                    </a>
                                    @endcan
                                    <a class="dropdown-item viewNote" href="javascript:void(0)" data-id="{{$v->id}}">
                                        <i class="fa fa-sticky-note-o" aria-hidden="true"></i> Notes
                                    </a>
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
<!-- Import Clients Modal -->
<div class="modal custom-modal fade" id="import_clients" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Clients</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('clients.import-clients')}}" id="importClientForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Import File <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="import_clients_file" id="import_clients_file">
                    </div>
                    <div class="form-group">
                        <a class="active" download="Clients - Sheet1.csv"
                            href="{{URL::asset('public/uploads/samples/Clients - Sheet1.csv')}}">
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
<!-- Import temporary Clients Modal -->
<div class="modal custom-modal fade" id="import_temp_clients" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Temporary Clients</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('clients.import-temp-clients')}}" id="importTempClientForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Import File <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="import_clients_file" id="import_clients_file">
                    </div>
                    <div class="form-group">
                        <a class="active" download="Clients - Sheet1.csv"
                            href="{{URL::asset('public/uploads/samples/Clients - Sheet1.csv')}}">
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
<!-- View Client Note -->
<div class="modal custom-modal fade" id="view_client_notes" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notes</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="client_notes"></p>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/pages/clients.js')}}"></script>
@endsection