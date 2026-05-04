<?php $page="clients";?>
@extends('layout.mainlayout')
@section('content')
    @component('components.breadcrumb')                
        @slot('title') Invoices @endslot
        @slot('li_1') Dashboard @endslot
        @slot('li_2') Invoices @endslot
    @endcomponent
    @include('layout.flash-message')
  <?php //echo "<pre>"; print_r($data); exit; ?>
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="form-group form-focus datatableButtons">
            <a class="btn btn-primary exportbtn-custom" href="{{route('invoices.export-invoices')}}">
                <i class="las la-file-export"></i>
                Export Invoices
            </a>
            <a class="btn btn-primary exportbtn-custom" data-bs-toggle="modal" data-bs-target="#change_status">
                <i class="fa fa-pencil"></i> Change Status
            </a>
            @can('invoice-delete')
            <button type="button" class="btn btn-danger exportbtn-custom" id="btnAllDelete"><i class="fa fa-trash-o"></i> Delete</button>
            @endcan
        </div>
    </div>
</div>
<div class="row filter-row">
    <div class="col-sm-6 col-md-2">  
        <div class="form-group form-focus select-focus">
            <select class="form-control payment_options floating" id="paymentOption"> 
                <option selected disabled>Select</option>
                @foreach($payment_statuses as $payment_status)
                <option value="{{$payment_status->id}}">{{$payment_status->name}}</option>
                @endforeach
            </select>
            <label class="focus-label">Payment Status</label>
        </div>
    </div>
    <div class="col-sm-6 col-md-2">  
        <div class="form-group form-focus select-focus">
            <select class="form-control client_list floating" id="client"> 
                <option selected disabled>Select</option>
                @foreach($clients as $client)
                    <option value="{{$client->id}}">{{ $client->client_business_name }}</option>
                @endforeach
            </select>
            <label class="focus-label">Clients</label>
        </div>
    </div>
    <div class="col-sm-6 col-md-2"> 
        <div class="form-group form-focus">
            <div class="cal-icon">
                <input class="form-control floating invoice_date_range" type="text" id="from_payment_date">
            </div>
            <label class="focus-label">From</label>
        </div>
    </div>
    <div class="col-sm-6 col-md-2"> 
        <div class="form-group form-focus">
            <div class="cal-icon">
                <input class="form-control floating invoice_date_range" type="text" id="to_payment_date">
            </div>
            <label class="focus-label">To</label>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <a href="{{route('invoices')}}" class="btn btn-primary filter-btn" id="searchInvoiceResult"> 
            <i class="las la-search"></i> Search 
        </a>
        <a href="{{route('invoices')}}" class="btn btn-primary filter-btn" id="resetInvoiceResult"> 
            <i class="las la-sync"></i> Reset 
        </a>  
        <a href="{{route('invoices.export-invoice-by-status')}}" class="btn btn-primary filter-btn" id="exportInvoiceByStatus"> 
            <i class="las la-file-export"></i> Export 
        </a>  
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table datatable" id="invoiceDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th>Invoice Number</th>
                        <th>Client</th>
                        <th>Project Name</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Payment Status</th>
                        <th>Payment Date</th>
                        <th>Categories</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                   
                </tbody>
            </table>
            <input type="hidden" id="pidval" >
        </div>
    </div>
</div>   
<!-- Change invoice status -->
<div class="modal custom-modal fade" id="change_status" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Invoice Payment Status</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Change Payment Status</label>
                    <select class="select" name="change_payment_id" id="change_payment_id">
                        <option selected disabled>Select</option>
                        @foreach($payment_statuses as $payment_status)
                        <option value="{{$payment_status->id}}">{{$payment_status->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" id="paymentDate" style="display:none;">
                    <label>Payment Date</label>
                    <div class="cal-icon">
                        <input class="form-control floating invoiceDatetimepicker" type="text" 
                        name="invoice_payment_date" id="invoice_payment_date">
                    </div>
                </div>
                <div class="submit-section">
                    <button type="button" class="btn btn-primary" id="changeStatusBtn">Submit</button>
                    <a type="button" data-bs-dismiss="modal" class="btn btn-dark">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- View invoice notes -->
<div class="modal custom-modal fade" id="view_invoice_notes" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notes</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="invoice_notes"></p>
            </div>
        </div>
    </div>
</div>
<!-- Edit Project for paid status -->
<div class="modal custom-modal fade" id="change_project" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Project</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Edit Project</label>
                    <select class="select" name="change_project_id" id="change_project_id">
                        <option value="" >Select</option>
                        @foreach($projects as $project)
                        <option value="{{$project->id}}">{{$project->name}}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="submit-section">
                    <button type="button" class="btn btn-primary" id="changeProjectBtn">Submit</button>
                    <a type="button" data-bs-dismiss="modal" class="btn btn-dark">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/pages/invoices.js')}}"></script>
@endsection