<?php $page="suppliers";?>
@extends('layout.mainlayout')
@section('css')
    <link rel="stylesheet" href="{{url('public/assets/css/dataTables.bootstrap4.min.css')}}"> 
@endsection
@section('content')
    @component('components.breadcrumb')                
        @slot('title') Suppliers @endslot
        @slot('li_1') Dashboard @endslot
        @slot('li_2') Suppliers @endslot
    @endcomponent
    @include('layout.flash-message')
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="form-group form-focus datatableButtons">
            <a href="{{route('suppliers.export-suppliers')}}" class="btn btn-primary exportbtn-custom">
                <i class="las la-file-export"></i>
                Export Suppliers
            </a>
            <a class="btn btn-primary exportbtn-custom" data-bs-toggle="modal" data-bs-target="#import_suppliers">
                <i class="las la-file-import"></i>
                Import Suppliers
            </a>
            <a class="btn btn-primary exportbtn-custom" data-bs-toggle="modal" data-bs-target="#import_temp_suppliers">
                <i class="las la-file-import"></i>
                Import Temp Suppliers
            </a>
            @can('supplier-delete')
            <button type="button" class="btn btn-danger exportbtn-custom" id="btnAllDelete"><i class="fa fa-trash-o"></i> Delete</button>
            @endcan
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table datatable" id="supplierDataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkall" aria-invalid="false">
                                <label class="custom-control-label" for="checkall"></label>
                            </div>
                        </th>
                        <th>Business Name</th>
                        <th>Contact Name</th>
                        <th>Remittance Email</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        @can('supplier-edit','supplier-delete')
                        <th class="text-center">Action</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $key => $v)
                    <tr id="supplierrow_{{$v->id}}">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="chk{{$v->id}}" class="custom-control-input cb-element" value="{{$v->id}}" aria-invalid="false"> 
                                <label for="chk{{$v->id}}"></label>
                            </div>
                        </td>
                        <td>{{ Str::ucfirst($v->supplier_business_name) }}</td>
                        <td>{{ $v->supplier_first_name }} {{ $v->supplier_last_name }}</td>
                        <td>{{ $v->supplier_email }}</td>
                        <td>{{ $v->supplier_mobile }}</td>
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
                                    @can('supplier-edit')
                                    <a class="dropdown-item" href="{{route('suppliers.edit', $v->id)}}">
                                        <i class="fa fa-pencil m-r-5"></i> Edit
                                    </a>
                                    @endcan
                                    @can('supplier-delete')
                                    <a class="dropdown-item deleteSupplierBtn" href="javascript:void(0)" data-id="{{$v->id}}">
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
<!-- Import Suppliers Model -->
<div class="modal custom-modal fade" id="import_suppliers" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Suppliers</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('suppliers.import-suppliers')}}" id="importSupplierForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Import File <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="import_suppliers_file" id="import_suppliers_file">
                    </div>
                    <div class="form-group">
                        <a class="active" download="Suppliers - Sheet1.csv"
                            href="{{URL::asset('public/uploads/samples/Suppliers - Sheet1.csv')}}">
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
<!-- Import Temporary Suppliers Modal -->
<div class="modal custom-modal fade" id="import_temp_suppliers" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Temp Suppliers</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('suppliers.import-temp-suppliers')}}" id="importTempSupplierForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Import File <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="import_suppliers_file" id="import_suppliers_file">
                    </div>
                    <div class="form-group">
                        <a class="active" download="Suppliers - Sheet1.csv"
                            href="{{URL::asset('public/uploads/samples/Suppliers - Sheet1.csv')}}">
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
<!-- View Supplier Note -->
<div class="modal custom-modal fade" id="view_supplier_notes" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notes</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="supplier_notes"></p>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/pages/suppliers.js')}}"></script>
@endsection