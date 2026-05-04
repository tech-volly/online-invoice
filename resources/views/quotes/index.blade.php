<?php $page="quotes";?>
@extends('layout.mainlayout')
@section('content')
    @component('components.breadcrumb')                
        @slot('title') Quotes @endslot
        @slot('li_1') Dashboard @endslot
        @slot('li_2') Quotes @endslot
    @endcomponent
    @include('layout.flash-message')
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="form-group form-focus datatableButtons">
            <a class="btn btn-primary exportbtn-custom" href="{{route('quotes.export-quotes')}}">
                <i class="las la-file-export"></i>
                Export Quotes
            </a>
            @can('quote-delete')
            <button type="button" class="btn btn-danger exportbtn-custom" id="btnAllDelete"><i class="fa fa-trash-o"></i> Delete</button>
            @endcan
        </div>
    </div>
</div>
<div class="row filter-row">
    <div class="col-sm-6 col-md-3">  
        <div class="form-group form-focus select-focus">
            <select class="form-control quote_options floating" id="quoteStatus"> 
                <option selected disabled>Select</option>
                <option value="Open">Open</option>
                <option value="Approved">Approved</option>
                <option value="Declined">Declined</option>
            </select>
            <label class="focus-label">Quote Status</label>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">  
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
    <div class="col-sm-6 col-md-3">
        <a href="{{route('quotes')}}" class="btn btn-primary report-btn" id="searchQuoteResult"> 
            <i class="las la-search"></i> Search 
        </a>
        <a href="{{route('quotes')}}" class="btn btn-primary report-btn" id="resetQuoteResult"> 
            <i class="las la-sync"></i> Reset 
        </a> 
        <a href="{{route('quotes.export-quotes-filter')}}" class="btn btn-primary report-btn" id="exportQuotesByOptions"> 
            <i class="las la-file-export"></i> Export 
        </a>   
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table datatable" id="quoteDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th>Quote Number</th>
                        <th>Client</th>
                        <th>Quote Date</th>
                        <th>Amount</th>
                        <th>Quote Status</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>     
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/pages/quotes.js')}}"></script>
@endsection