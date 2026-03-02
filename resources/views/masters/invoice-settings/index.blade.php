<?php $page="invoice-settings";?>
@extends('layout.mainlayout')
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Invoice Settings</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Invoice Settings</li>
            </ul>
        </div>
    </div>
</div>   

@include('layout.flash-message')
<form action="{{route('invoice-settings.save')}}" method="post" id="addEditInvoiceSettings">
    @csrf
    <input type="hidden" name="id" id="id" value="{{$data ? $data->id : ''}}">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Header Settings</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Company Name</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                    name="invoice_header_company_name" id="invoice_header_company_name" value="{{$data ? $data->invoice_header_company_name : ''}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">ABN</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                    name="invoice_header_number" id="invoice_header_number" value="{{$data ? $data->invoice_header_number : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Street Address</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                    name="invoice_header_street_address" id="invoice_header_street_address" value="{{$data ? $data->invoice_header_street_address : ''}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">City</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                    name="invoice_header_city" id="invoice_header_city" value="{{$data ? $data->invoice_header_city : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">State</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                    name="invoice_header_state" id="invoice_header_state" value="{{$data ? $data->invoice_header_state : ''}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Postal Code</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                    name="invoice_header_postalcode" id="invoice_header_postalcode" value="{{$data ? $data->invoice_header_postalcode : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Toll Free Number</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                    name="invoice_header_tollfree" id="invoice_header_tollfree" value="{{$data ? $data->invoice_header_tollfree : ''}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Email</label>
                                <div class="col-lg-9">
                                    <input type="email" class="form-control" aria-describedby="basic-addon2"
                                    name="invoice_header_email" id="invoice_header_email" value="{{$data ? $data->invoice_header_email : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Website</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                    name="invoice_header_website" id="invoice_header_website" value="{{$data ? $data->invoice_header_website : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Footer Settings</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Company Name</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                    name="invoice_footer_company_name" id="invoice_footer_company_name" value="{{$data ? $data->invoice_footer_company_name : ''}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">BSB</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                    name="invoice_footer_bsb_number" id="invoice_footer_bsb_number" value="{{$data ? $data->invoice_footer_bsb_number : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">ACC</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2"
                                    name="invoice_footer_acc_number" id="invoice_footer_acc_number" value="{{$data ? $data->invoice_footer_acc_number : ''}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Remittance</label>
                                <div class="col-lg-9">
                                    <input type="email" class="form-control" aria-describedby="basic-addon2"
                                    name="invoice_footer_email" id="invoice_footer_email" value="{{$data ? $data->invoice_footer_email : ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Notes</label>
                                <div class="col-lg-12">
                                    <textarea name="invoice_footer_notes" id="invoice_footer_notes" class="ckeditor form-control" rows="5">{!! $data->invoice_footer_notes !!}</textarea>
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
        <a href="{{route('dashboard')}}" class="btn btn-dark">Cancel</a>
    </div>
</form>
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/libs/ckeditor/ckeditor.js')}}"></script>
<script type="text/javascript">
    //CKEDITOR
    ClassicEditor.create( document.querySelector( '.ckeditor' ), {
        height : '350px',
        toolbar: {
            items: ['heading', 'bold', 'italic', '|', 'undo', 'redo', '|', 'numberedList', 'bulletedList', 'blockQuote' ]
        },
        removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload', 'MediaEmbed', 'Table', 'TableToolbar']
    }).catch( error => {
        console.error( error );
    });
</script>
@endsection