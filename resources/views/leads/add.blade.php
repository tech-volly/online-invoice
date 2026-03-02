<?php $page="add-leads";?>
@extends('layout.mainlayout')
@section('content')
@component('components.breadcrumb')                
    @slot('title') {{ $data->id > 0 ? 'Edit' : 'Add' }} Lead @endslot
    @slot('li_1') Dashboard @endslot
    @slot('li_2') Leads @endslot
    @slot('li_3') {{ $data->id > 0 ? 'Edit' : 'Add' }} Lead @endslot
@endcomponent

@include('layout.flash-message')
<form action="{{route('leads.add.action')}}" method="post" id="addEditLead">
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
                                <label class="col-lg-3 col-form-label">Company Name</label>
                                <div class="col-lg-9">
                                    <input type="text" name="company_name" id="company_name" class="form-control" value="{{$data->company_name}}">
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
                    <div class="row" id="client_type_business_row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">First Name</label>
                                <div class="col-lg-9">
                                    <input type="text" name="client_first_name" id="client_first_name" class="form-control" value="{{$data->client_first_name}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Last Name</label>
                                <div class="col-lg-9">
                                    <input type="text" name="client_last_name" id="client_last_name" class="form-control" value="{{$data->client_last_name}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Email</label>
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
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-6 d-flex">
            <div class="card flex-fill">
                <div class="card-header">
                    <h4 class="card-title mb-0">Follow Up</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Discussion Date</label>
                                <div class="col-lg-9">
                                    <div class="cal-icon">
                                        <input class="form-control floating followDatetimepicker" type="text" 
                                        name="lead_discussion_date" id="lead_discussion_date" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">FollowUp Date</label>
                                <div class="col-lg-9">
                                    <div class="cal-icon">
                                        <input class="form-control floating followDatetimepicker" type="text" 
                                        name="follow_up_datetime" id="follow_up_datetime" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Notes</label>
                                <div class="col-lg-9">
                                    <textarea class="form-control ckeditor" name="followup_notes" id="followup_notes" rows="9" cols="10"></textarea>
                                    <!-- <input class="form-control" type="text"  value=""> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($data->lead_follow_ups->count() > 0)
                    <div class="row">
                        <div class="col-xl-12">
                            <table class="table table-bordered mb-0 mt-2">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Discussion Date</th>
                                        <th>Follow Up</th>
                                        <th>Notes</th>
                                        <th>Created By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_reverse($data->lead_follow_ups->toArray()) as $key => $followup)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{ $followup['lead_discussion_date'] ? getFormatedDate($followup['lead_discussion_date']) : '' }}</td>
                                        <td>{{ getFormatedDate($followup['followup_datetime']) }}</td>
                                        <td>{!! $followup['followup_notes'] !!}</td>
                                        <td>{{ $followup['lead_created_by'] ? $followup['lead_created_by'] : '' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="{{route('leads')}}" class="btn btn-dark">Cancel</a>
    </div>
</form>
    @endsection
@section('script')
<script src="{{ URL::asset('public/assets/libs/ckeditor/ckeditor.js')}}"></script>
<script src="{{ URL::asset('public/assets/js/pages/leads.js')}}"></script>
<script type="text/javascript">
//CKEDITOR
ClassicEditor.create( document.querySelector( '.ckeditor' ), {
    removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload', 'MediaEmbed']
}).catch( error => {
    console.error( error );
});
</script>
@endsection