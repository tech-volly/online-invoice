<?php $page="opening-balance";?>
@extends('layout.mainlayout')
@section('css')
    <link rel="stylesheet" href="{{url('public/assets/css/dataTables.bootstrap4.min.css')}}"> 
@endsection
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Opening Balance</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Opening Balance</li>
            </ul>
        </div>
        @can('opening-balance-create')
        <div class="col-auto float-end ms-auto">
            <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_opening_balance"><i class="fa fa-plus"></i> Add Opening Balance</a>
        </div>
        @endcan
    </div>
</div>   
@include('layout.flash-message') 
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="form-group form-focus datatableButtons">
            @can('opening-balance-delete')
            <button type="button" class="btn btn-danger exportbtn-custom" id="btnAllDelete"><i class="fa fa-trash-o"></i> Delete</button>
            @endcan
        </div>
    </div>
</div>   
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table mb-0 datatable" id="openingBalanceDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th>Opening Balance Date</th>
                        <th>Opening Balance</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $key => $v)
                    <tr id="openingbalancerow_{{$v->id}}">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="chk{{$v->id}}" class="custom-control-input cb-element" value="{{$v->id}}" aria-invalid="false"> 
                                <label for="chk{{$v->id}}"></label>
                            </div>
                        </td>
                        <td>{{ $v->opening_balance_date }}</td>
                        <td>{{ getPrice($v->opening_balance_value) }}</td>
                        <td class="text-center">
                            <div class="dropdown dropdown-action">
                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    @can('opening-balance-edit')
                                    <a class="dropdown-item editOpeningBalance" href="javascript:void(0)" data-id="{{$v->id}}"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                    @endcan
                                    @can('opening-balance-create')
                                    <a class="dropdown-item deleteOpeningBalanceBtn" href="javascript:void(0)" data-id="{{$v->id}}">
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
<div class="modal custom-modal fade" id="add_opening_balance" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Opening Balance</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('opening-balance.create')}}" id="addOpeningBalanceForm">
                    @csrf
                    <div class="form-group">
                        <label>Year <span class="text-danger">*</span></label>
                        <select class="select" name="opening_balance_date" id="opening_balance_date">
                            <option selected disabled>Select Year</option>
                            <option value="2021 - 2022">2021 - 2022</option>
                            <option value="2022 - 2023">2022 - 2023</option>
                            <option value="2023 - 2024">2023 - 2024</option>
                            <option value="2024 - 2025">2024 - 2025</option>
                            <option value="2025 - 2026">2025 - 2026</option>
                            <option value="2026 - 2027">2026 - 2027</option>
                            <option value="2027 - 2028">2027 - 2028</option>
                            <option value="2028 - 2029">2028 - 2029</option>
                            <option value="2029 - 2030">2029 - 2030</option>
                            <option value="2030 - 2031">2030 - 2031</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Opening Balance <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon2">$</span>
                            <input type="text" class="form-control" placeholder="0.00" aria-describedby="basic-addon2"
                            name="opening_balance_value" id="opening_balance_value">
                        </div>
                        <label id="errorToShow"></label>
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
<div class="modal custom-modal fade" id="edit_opening_balance" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Opening Balance</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('opening-balance.update')}}" id="editOpeningBalanceForm">
                    @csrf
                    <input type="hidden" name="opening_balance_id" value="" id="opening_balance_id">
                    <div class="form-group">
                        <label>Date <span class="text-danger">*</span></label>
                        <select class="select" name="opening_balance_date" id="opening_balance_date_update">
                            <option selected disabled>Select Year</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Opening Balance <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon2">$</span>
                            <input type="text" class="form-control" placeholder="0.00" aria-describedby="basic-addon2"
                            name="opening_balance_value" id="opening_balance_value_update">
                        </div>
                        <label id="errorToShowUpdate"></label>
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
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/pages/masters/opening-balance.js')}}"></script>
@endsection