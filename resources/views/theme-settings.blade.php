
<?php $page="theme-settings";?>
@extends('layout.mainlayout')
@section('content') 
            
            
        
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                        
                            <!-- Page Header -->
                            <div class="page-header">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h3 class="page-title">Theme Settings</h3>
                                    </div>
                                </div>
                            </div>
                            <!-- /Page Header -->
                        
                            <form>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Website Name</label>
                                    <div class="col-lg-9">
                                        <input name="website_name" class="form-control" value="HDS Financials" type="text">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Light Logo</label>
                                    <div class="col-lg-7">
                                        <input type="file" class="form-control">
                                        <span class="form-text text-muted">Recommended image size is 40px x 40px</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="img-thumbnail float-end"><img src="{{ URL::asset('public/assets/img/logo2.png')}}?28062022" alt="" width="40" height="40"></div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Favicon</label>
                                    <div class="col-lg-7">
                                        <input type="file" class="form-control">
                                        <span class="form-text text-muted">Recommended image size is 16px x 16px</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="settings-image img-thumbnail float-end"><img src="{{ URL::asset('public/assets/img/logo2.png')}}?28062022" class="img-fluid" width="16" height="16" alt=""></div>
                                    </div>
                                </div>
                                <div class="submit-section">
                                    <button class="btn btn-primary submit-btn">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
    @endsection