<?php $page="add-clients";?>
@extends('layout.mainlayout')
@section('css')
    <link href="{{ URL::asset('public/assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
@component('components.breadcrumb')                
    @slot('title') {{ $data->id > 0 ? 'Edit' : 'Add' }} Client @endslot
    @slot('li_1') Dashboard @endslot
    @slot('li_2') Clients @endslot
    @slot('li_3') {{ $data->id > 0 ? 'Edit' : 'Add' }} Client @endslot
@endcomponent

@include('layout.flash-message')
<form action="{{route('clients.add.action')}}" method="post" id="addEditClient">
    @csrf
    <input type="hidden" name="id" id="id" value="{{$data->id}}">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Client Details</h4>
                </div>
                <div class="card-body">
                    <h4 class="card-title">Personal Information</h4>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Client Number</label>
                                <div class="col-lg-9">
                                    <input type="text" name="client_number" id="client_number" class="form-control" value="{{$data->client_number}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div id="client_type_business" >
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Business Name</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="client_business_name" id="client_business_name" class="form-control" value="{{$data->client_business_name}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">First Name</label>
                                <div class="col-lg-9">
                                    <input type="text" name="client_first_name" id="client_first_name" class="form-control" value="{{$data->client_first_name}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="client_type_business_row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Last Name</label>
                                <div class="col-lg-9">
                                    <input type="text" name="client_last_name" id="client_last_name" class="form-control" value="{{$data->client_last_name}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Mobile</label>
                                <div class="col-lg-9">
                                    <input type="text" name="client_mobile" id="client_mobile" class="form-control" value="{{$data->client_mobile}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="card-title">Billing Address</h4>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Street Address 1</label>
                                <div class="col-lg-9">
                                    <input type="text" name="client_street_address_1" id="client_street_address_1" class="form-control"
                                    value="{{$data->client_street_address_1}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">City</label>
                                <div class="col-lg-9">
                                    <input type="text" name="client_city" id="client_city" class="form-control" value="{{$data->client_city}}">
                                </div>
                            </div>
                            <?php 
                                if($data->add_shipping_address == 'same_as_billing') {
                                    $checked = "checked";
                                }else {
                                    $checked =  "";
                                }
                            ?>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">&nbsp</label>
                                <div class="col-lg-9">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="add_shipping_address" id="add_shipping_address" {{$checked}}
                                            > 
                                            Shipping Address (Same as Billing Address)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">State</label>
                                <div class="col-lg-9">
                                    <input type="text" name="client_state" id="client_state" class="form-control" value="{{$data->client_state}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Postal Code</label>
                                <div class="col-lg-9">
                                    <input type="text" name="client_postalcode" id="client_postalcode" class="form-control" value="{{$data->client_postalcode}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="secondary_address">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Street Address 1</label>
                                <div class="col-lg-9">
                                    <input type="text" name="street_address_1" id="street_address_1" class="form-control"
                                    value="{{$data->shipping_street_address_1}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">City</label>
                                <div class="col-lg-9">
                                    <input type="text" name="city" id="city" class="form-control"
                                    value="{{$data->shipping_city}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">State</label>
                                <div class="col-lg-9">
                                    <input type="text" name="state" id="state" class="form-control"
                                    value="{{$data->shipping_state}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Postal Code</label>
                                <div class="col-lg-9">
                                    <input type="text" name="postalcode" id="postalcode" class="form-control"
                                    value="{{$data->shipping_postalcode}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Client Tags</label>
                                <div class="col-lg-9">
                                    <input type="text" name="client_tags" id="client_tags" class="form-control" value="{{$data->client_tags}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="is_contact_div" id="is_contact_div" value="1">
                    <br><br>
                    <div class="row" id="add_contact_section" style="display:none;">
                        <div class="col-xl-6 d-flex">
                            <div class="card flex-fill">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Contacts List</h4>
                                </div>
                                <div class="card-body">
                                    @if($data->contacts->count() > 0)
                                        @foreach($data->contacts as $contact)
                                        <div class="add-more-contact">
                                            <input type="hidden" name="contacts_id[]" value="{{@$contact->id}}">
                                            <div class="row pull-right remove_contact_form">
                                                <a href="javascript:;" data-id="{{$contact->id}}" class="btn btn-danger btn-xs" id="remove_contact_form"><i class="fa fa-times"></i></a> 
                                            </div>
                                            <div class="row" style="margin-top: 5px">                                       
                                                <label class="col-lg-3 col-form-label">Name</label>
                                                <div class="col-lg-9">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <input type="text" name="contact_first_name[]" placeholder="First Name" class="form-control" value="{{$contact->contact_first_name}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <input type="text" name="contact_last_name[]" placeholder="Last Name" class="form-control" value="{{$contact->contact_last_name}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Email</label>
                                                <div class="col-lg-9">
                                                    <input type="email" name="contact_email[]" class="form-control" 
                                                    value="{{$contact->contact_email}}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="col-lg-3 col-form-label">Phone</label>
                                                <div class="col-lg-9">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <input type="text" name="contact_telephone[]" placeholder="Telephone" class="form-control" value="{{$contact->contact_telephone}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <input type="text" name="contact_mobile[]" placeholder="Mobile" class="form-control" value="{{$contact->contact_mobile}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="another_id[]" value="{{@$contact->id}}">
                                        @endforeach
                                    @else
                                    <div class="add-more-contact">
                                        <div class="row pull-right remove_contact_form" style="display: none;">
                                            <a href="javascript:;" class="btn btn-danger btn-xs" id="remove_contact_form"><i class="fa fa-times"></i></a> 
                                        </div>
                                        <div class="row" style="margin-top: 5px">                                       
                                            <label class="col-lg-3 col-form-label">Name</label>
                                            <div class="col-lg-9">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="text" name="contact_first_name[]" placeholder="First Name" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="text" name="contact_last_name[]" placeholder="Last Name" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Email</label>
                                            <div class="col-lg-9">
                                                <input type="email" name="contact_email[]" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-lg-3 col-form-label">Phone</label>
                                            <div class="col-lg-9">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="text" name="contact_telephone[]" placeholder="Telephone" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="text" name="contact_mobile[]" placeholder="Mobile" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="add_contact_result"></div>
                                    <br>
                                    <button id="add_more_contacts" type="button" class="btn btn-secondary btn-sm">
                                        <i class="fa fa-plus"></i> Add More
                                    </button>
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
                    <h4 class="card-title mb-0">Account Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Accounts Email</label>
                                <div class="col-lg-9">
                                    <input type="text" name="client_email" id="client_email" class="form-control" value="{{$data->client_email}}">
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
                        <div class="col-xl-12">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Notes</label>
                                <div class="col-lg-12">
                                    <textarea name="client_notes" id="client_notes" class="ckeditor form-control" rows="5">{!! $data->client_notes !!}</textarea>
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
        <a href="{{route('clients')}}" class="btn btn-dark">Cancel</a>
    </div>
</form>
    @endsection
@section('script')
<script src="{{ URL::asset('public/assets/libs/ckeditor/ckeditor.js')}}"></script>
<script src="{{ URL::asset('public/assets/js/pages/clients-add.js')}}"></script>
@endsection