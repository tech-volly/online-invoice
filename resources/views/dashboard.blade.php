<?php $page = "dashboard"; ?>
@extends('layout.mainlayout')
@section('css')
<!-- Morris css -->
<link href="{{ URL::asset('public/assets/libs/morris/morris.css')}}" rel="stylesheet" type="text/css" />

<style>
    .quarter-section .card .card-body .card-header h3 {
        color: #fff;
    }
    .total-income .card .card-body .card-header h3 {
        color: #fff;
    }
    .pagination{
        margin-top:15px;
        margin-bottom:0px;
    }
    .pagination li.selected {
        background-color: #667eea;
        color: #fff;
    }

</style>
@endsection

@section('content')


<!-- Page Header -->
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Welcome {{Auth::user()->first_name}} {{Auth::user()->last_name}}!</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item active">Dashboard</li>
            </ul>
        </div>
    </div>
</div>

@can('dashboard-list')
<div class="row">
    <div class="col-md-12">
        <div class="card-group m-b-30">
            <div class="card">
                <div class="card-body" style="background-color: #b9ebbf;">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <span class="d-block">Total Sales (Current Month)</span>
                        </div>
                    </div>
                    <h3 class="mb-3">{{ getPrice($data['current_month_sales']) }}</h3>
                </div>
            </div>
            <div class="card">
                <div class="card-body" style="background-color: #eccccf;">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <span class="d-block">Total Expenses (Current Month)</span>
                        </div>
                    </div>
                    <h3 class="mb-3">{{ getPrice($data['current_month_expense']) }}</h3>
                </div>
            </div>
        </div>
    </div>  
</div>

<br /><br />

<div class="row">
    <div class="col-md-12">
        <div class="card-group m-b-30">
            <div class="card">
                <div class="card-body" style="background-color: #b9ebbf;">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <span class="d-block">Total Open Estimate Amount</span>
                        </div>
                    </div>
                    <h3 class="mb-3">{{ getPrice($data['open_quotes_amount']) }}</h3>
                </div>
            </div>
            <div class="card" style="background-color: #eccccf;">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <span class="d-block">Total Unpaid Invoice Amount</span>
                        </div>
                    </div>
                    <h3 class="mb-3">{{ getPrice($data['unpaid_invoice_total']) }}</h3>
                </div>
            </div>
        </div>
    </div>  
</div>

<br /><br />

<div class="row">
    <div class="col-md-12">
        <div class="card-group m-b-30 quarter-section">
            <div class="card">

                <div class="card-body">
                    <div class="card-header" style="background-color: #667eea;">
                        <h3 class="card-title mb-0">First Quarter</h3>
                    </div>
                    <!--                    <div class="d-flex justify-content-between mb-3">
                                            <div>
                                                <span class="d-block">First Quarter</span>
                                            </div>
                                        </div>-->
                    <div class="progress mb-2" style="height: 1px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div>
                        <div style="float: left;">
                            <p class="mb-0">GST Collected<h3 class="mb-3">{{ getPrice(round($data['q1_gst_collected'], 2)) }}</h3></p>

                        </div>
                        <div style="float: right;">
                            <p class="mb-0">GST Paid<h3 class="mb-3">{{ getPrice(round($data['q1_gst_paid'], 2)) }}</h3></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-header" style="background-color: #667eea;">
                        <h3 class="card-title mb-0">Second Quarter</h3>
                    </div>

                    <!--                    <div class="d-flex justify-content-between mb-3">
                                            <div>
                                                <span class="d-block">Second Quarter</span>
                                            </div>
                                        </div>-->
                    <div class="progress mb-2" style="height: 1px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div>
                        <div style="float: left;">
                            <p class="mb-0">GST Collected<h3 class="mb-3">{{ getPrice(round($data['q2_gst_collected'], 2)) }}</h3></p>
                        </div>
                        <div style="float: right;">
                            <p class="mb-0">GST Paid<h3 class="mb-3">{{ getPrice(round($data['q2_gst_paid'], 2)) }}</h3></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-header" style="background-color: #667eea;">
                        <h3 class="card-title mb-0">Third Quarter</h3>
                    </div>
                    <!--                    <div class="d-flex justify-content-between mb-3">
                                            <div>
                                                <span class="d-block">Third Quarter</span>
                                            </div>
                                        </div>-->
                    <div class="progress mb-2" style="height: 1px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div>
                        <div style="float: left;">
                            <p class="mb-0">GST Collected<h3 class="mb-3">{{ getPrice(round($data['q3_gst_collected'], 2)) }}</h3></p>

                        </div>
                        <div style="float: right;">
                            <p class="mb-0">GST Paid<h3 class="mb-3">{{ getPrice(round($data['q3_gst_paid'], 2)) }}</h3></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-header" style="background-color: #667eea;">
                        <h3 class="card-title mb-0">Fourth Quarter</h3>
                    </div>
                    <!--                    <div class="d-flex justify-content-between mb-3">
                                            <div>
                                                <span class="d-block">Fourth Quarter</span>
                                            </div>
                                        </div>-->
                    <div class="progress mb-2" style="height: 1px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div>
                        <div style="float: left;">
                            <p class="mb-0">GST Collected<h3 class="mb-3">{{ getPrice(round($data['q4_gst_collected'], 2)) }}</h3></p>

                        </div>
                        <div style="float: right;">
                            <p class="mb-0">GST Paid<h3 class="mb-3">{{ getPrice(round($data['q4_gst_paid'], 2)) }}</h3></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>

<br /><br />

<div class="row">
    <div class="col-md-12 d-flex">
        <div class="card card-table flex-fill">
            <div class="card-header" style="background-color: #eccccf;">
                <h3 class="card-title mb-0">Unpaid Invoices</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">  
                    <table class="table custom-table table-nowrap mb-0 datatable" id="unPaidInvoices">
                        <thead>
                            <tr>
                                <th>Invoice ID</th>
                                <th>Client</th>
                                <th class="text-center">Notes</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($data['unpaind_invoices']->count() > 0)
                            @foreach($data['unpaind_invoices'] as $key => $value)
                            <tr>
                                <td><a href="{{route('invoices.edit', $value->id)}}">{{$value->invoice_number}}</a></td>
                                <td>
                                    <h2>{{$value->client->client_business_name}}</h2>
                                </td>
                                <td class="text-center">
                                    <a class="dropdown-item viewNote" href="javascript:void(0)" data-id="{{$value->id}}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                </td>
                                <td>{{getFormatedDate($value->invoice_due_date)}}</td>
                                <td>{{getPrice($value->invoice_grand_total)}}</td>
                                <td class="text-center">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{route('invoices.download-invoice', $value->id)}}">
                                                <i class="fa fa-download"></i> Download
                                            </a>
                                            <a class="dropdown-item sendEmail" href="javascript:;" data-id="{{$value->id}}">
                                                <i class="fa fa-paper-plane"></i> Send Email
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr class="table-danger text-center">
                                <td colspan="6">
                                    No Data Available
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<br /><br />

<div class="row">
    <div class="col-md-12">
        <div class="card-group m-b-30 quarter-section">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 card-header" style="background-color: #000;">
                        <div>
                            <h3 class="d-block mb-0">First Quarter</h3>
                        </div>
                        <div>
                            <span class="text-{{ $data['q1_profit_per'] >= 0 ? 'success' : 'danger' }}">{{ $data['q1_profit_per'] }}%</span>
                        </div>
                    </div>
                    <div class="progress mb-2" style="height: 1px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-0">Income<h3 class="mb-3">{{ getPrice(round($data['q1_income'], 2)) }}</h3></p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0">Expense<h3 class="mb-3">{{ getPrice(round($data['q1_expense'], 2)) }}</h3></p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0">Profit<h3 class="mb-3">{{ getPrice(round($data['q1_profit'], 2)) }}</h3></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 card-header" style="background-color: #000;">
                        <div>
                            <h3 class="d-block mb-0">Second Quarter</h3>
                        </div>
                        <div>
                            <span class="text-{{ $data['q2_profit_per'] >= 0 ? 'success' : 'danger' }}">{{ $data['q2_profit_per'] }}%</span>
                        </div>
                    </div>
                    <div class="progress mb-2" style="height: 1px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-0">Income<h3 class="mb-3">{{ getPrice(round($data['q2_income'], 2)) }}</h3></p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0">Expense<h3 class="mb-3">{{ getPrice(round($data['q2_expense'], 2)) }}</h3></p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0">Profit<h3 class="mb-3">{{ getPrice(round($data['q2_profit'], 2)) }}</h3></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card-group m-b-30 quarter-section">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 card-header" style="background-color: #000;">
                        <div>
                            <h3 class="d-block mb-0">Third Quarter</h3>
                        </div>
                        <div>
                            <span class="text-{{ $data['q3_profit_per'] >= 0 ? 'success' : 'danger' }}">{{ $data['q3_profit_per'] }}%</span>
                        </div>
                    </div>
                    <div class="progress mb-2" style="height: 1px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-0">Income<h3 class="mb-3">{{ getPrice(round($data['q3_income'], 2)) }}</h3></p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0">Expense<h3 class="mb-3">{{ getPrice(round($data['q3_expense'], 2)) }}</h3></p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0">Profit<h3 class="mb-3">{{ getPrice(round($data['q3_profit'], 2)) }}</h3></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 card-header" style="background-color: #000;">
                        <div>
                            <h3 class="d-block mb-0">Fourth Quarter</h3>
                        </div>
                        <div>
                            <span class="text-{{ $data['q4_profit_per'] >= 0 ? 'success' : 'danger' }}">{{ $data['q4_profit_per'] }}%</span>
                        </div>
                    </div>
                    <div class="progress mb-2" style="height: 1px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-0">Income<h3 class="mb-3">{{ getPrice(round($data['q4_income'], 2)) }}</h3></p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0">Expense<h3 class="mb-3">{{ getPrice(round($data['q4_expense'], 2)) }}</h3></p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0">Profit<h3 class="mb-3">{{ getPrice(round($data['q4_profit'], 2)) }}</h3></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>

<br /><br />

<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12 text-center total-income">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header" style="background-color: #667eea;">
                            <h3 class="card-title mb-0">Total Income & Expense ({{$data['expected_fin_year']}})</h3>
                        </div>
                        <div id="bar-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<br /><br />

<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12 text-center total-income">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header" style="background-color: #667eea;">
                            <h3 class="card-title mb-0">Expected Income & Expense ({{$data['expected_fin_year']}})
                                {{getPrice(round($data['expected_inc_exp_diff'], 2))}}<i class="las la-arrow-{{$data['expected_inc_exp_diff'] > 0 ? 'up' : 'down'}}"></i>
                            </h3>
                        </div>
                        <div id="expected-bar-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<br /><br />

<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12 text-center total-income">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header" style="background-color: #667eea;">
                            <h3 class="card-title mb-0">Yearly Income & Expense
                                <!-- {{getPrice(round($data['expected_inc_exp_diff'], 2))}}<i class="las la-arrow-{{$data['expected_inc_exp_diff'] > 0 ? 'up' : 'down'}}"></i> -->
                            </h3>
                        </div>
                        <div id="yearly-bar-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="row">
        
            <div class="col-md-12 text-center total-income">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header" style="background-color: #667eea;">
                            <h3 class="card-title mb-0">Projects Income & expense</h3>
                        </div>
                        <div id="proj-bar-charts"></div>
                        <nav aria-label="Page navigation example" style={dis}>
                             <ul class="pagination pagination-select justify-content-center">
                                @foreach( json_decode($project_expense_and_invoice) as $index )
                                    <li class="page-item page-link @if($loop->first) selected @endif"  value="{{ $loop->index }}"  >  {{ $loop->index +1 }}</li>
                                 @endforeach
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card-group m-b-30 quarter-section">
            <div class="card">
                <div class="card-body">
                    <div class="card-header" style="background-color: #667eea;">
                        <h3 class="card-title mb-0">Recurring Invoices Total</h3>
                    </div>
                    <!--                    <div class="d-flex justify-content-between mb-3">
                                            <div>
                                                <span class="d-block">Recurring Invoices Total</span>
                                            </div>
                                        </div>-->
                    <div class="progress mb-2" style="height: 1px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <h3 class="mb-3">{{ getPrice(round($data['recurring_invoice_total'], 2)) }}</h3>
                </div>
            </div>
        </div>
    </div>  
</div>

<br /><br />

<div class="row">
    <div class="col-md-12 d-flex">
        <div class="card card-table flex-fill">
            <div class="card-header" style="background-color: #667eea;">
                <h3 class="card-title mb-0">Recurring Invoices</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">  
                    <table class="table custom-table table-nowrap mb-0 datatable" id="recurringInvoiceTable">
                        <thead>
                            <tr>
                                <th>Subscription Name</th>
                                <th>Client</th>
                                <th>Cycle</th>
                                <th>Start Date</th>
                                <th>Next Date</th>
                                <th>Generated</th>
                                <th>% Increase</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($data['subscriptions']->count() > 0)
                            @foreach($data['subscriptions'] as $key => $value)
                            <tr>
                                <td><a href="{{route('subscriptions.edit', $value->id)}}">{{ $value->subscription_name }}</a></td>
                                <td>
                                    <h2>{{ $value->client->client_business_name }}</h2>
                                </td>
                                <td>{{ Str::ucfirst($value->subscription_cycle) }}</td>
                                <td>{{ getFormatedDate($value->subscription_start_date) }}</td>
                                <td>{{ getFormatedDate($value->subscription_next_date) }}</td>
                                <td>{{ getGeneratedSubscriptionCount($value->id) }}</td>
                                <td>{{ $value->subscription_incremented_percentage ? $value->subscription_incremented_percentage : 'N/A' }}</td>
                                <td>{{ getPrice(getNextAmountForSubscription($value->id)) }}</td>
                            </tr>
                            @endforeach
                            @else
                            <tr class="table-danger text-center">
                                <td colspan="9">
                                    No Data Available
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<br /><br />

<div class="row">
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-usd"></i></span>
                <div class="dash-widget-info">
                    <h3>{{ getPrice(round($data['payroll_cat_exp'], 2)) }}</h3>
                    <span>Payroll</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-usd"></i></span>
                <div class="dash-widget-info">
                    <h3>{{ getPrice(round($data['contractor_payment_cat_exp'], 2)) }}</h3>
                    <span>Contractor Payment</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-usd"></i></span>
                <div class="dash-widget-info">
                    <h3>{{ getPrice(round($data['cogs_cat_exp'], 2)) }}</h3>
                    <span>COGS</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-usd"></i></span>
                <div class="dash-widget-info">
                    <h3>{{ getPrice(round($data['government_cat_exp'], 2)) }}</h3>
                    <span>Government</span>
                </div>
            </div>
        </div>
    </div>
</div>

<br /><br />

<div class="row">
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-usd"></i></span>
                <div class="dash-widget-info">
                    <h3>{{ getPrice(round($data['subscription_cat_exp'], 2)) }}</h3>
                    <span>Subscriptions</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-usd"></i></span>
                <div class="dash-widget-info">
                    <h3>{{ getPrice(round($data['software_dev_cat_exp'], 2)) }}</h3>
                    <span>Software Development</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-usd"></i></span>
                <div class="dash-widget-info">
                    <h3>{{ getPrice(round($data['domestic_cat_exp'], 2)) }}</h3>
                    <span>Domestic (Air Ticket)</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-usd"></i></span>
                <div class="dash-widget-info">
                    <h3>{{ getPrice(round($data['food_fuel_cat_exp'], 2)) }}</h3>
                    <span>Food</span>
                </div>
            </div>
        </div>
    </div>
</div>

<br /><br />

<div class="row">
    <div class="col-md-12 d-flex">
        <div class="card card-table flex-fill">
            <div class="card-header" style="background-color: #667eea;">
                <h3 class="card-title mb-0">Quotes</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">  
                    <table class="table custom-table table-nowrap mb-0 datatable" id="quotesTable">
                        <thead>
                            <tr>
                                <th>Quote ID</th>
                                <th>Client</th>
                                <th>Quote Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($data['quotes']->count() > 0)
                            @foreach($data['quotes'] as $key => $value)
                            <tr>
                                <td><a href="{{route('quotes.edit', $value->id)}}">{{ $value->quote_number }}</a></td>
                                <td>
                                    <h2>{{ $value->client->client_business_name }}</h2>
                                </td>
                                <td>{{ getFormatedDate($value->quote_date) }}</td>
                                <td>{{getPrice($value->quote_grand_total)}}</td>
                                <td>
                                    @if($value->quote_payment_status == 'Open')
                                    <span class="badge bg-inverse-warning">Open</span>
                                    @elseif($value->quote_payment_status == 'Approved')
                                    <span class="badge bg-inverse-success">Approved</span>
                                    @elseif($value->quote_payment_status == 'Declined')
                                    <span class="badge bg-inverse-danger">Declined</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr class="table-danger text-center">
                                <td colspan="5">
                                    No Data Available
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
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

@endcan
<!-- /Main Wrapper -->              
@endsection
@section('script')
<!-- Morris JS -->
<script src="{{ URL::asset('public/assets/libs/morris/morris.min.js')}}"></script>
<script src="{{ URL::asset('public/assets/libs/raphael/raphael.min.js')}}"></script>
<script src="{{ URL::asset('public/assets/js/pages/dashboard.js')}}"></script>
<script type="text/javascript">
$(document).ready(function () {
    var income_expense = {!! $income_expense_arr !!};
    Morris.Bar({
        element: 'bar-charts',
        data: income_expense,
        xkey: 'y',
        ykeys: ['a', 'b'],
        labels: ['Total Income', 'Total Expense'],
        lineColors: ['#667eea', '#764ba2'],
        lineWidth: '3px',
        barColors: ['#667eea', '#764ba2'],
        resize: true,
        redraw: true
    });

    var expected_in_exp = {!! $expected_inc_exp_arr !!};
    Morris.Bar({
        element: 'expected-bar-charts',
        data: expected_in_exp,
        xkey: 'y',
        ykeys: ['a', 'b'],
        labels: ['Expected Income', 'Expected Expense'],
        lineColors: ['#667eea', '#764ba2'],
        lineWidth: '3px',
        barColors: ['#667eea', '#764ba2'],
        resize: true,
        redraw: true
    });

    //yearly-bar-charts
    
    var yearly_in_exp = {!! $yearly_expense_and_income !!};
    Morris.Bar({
        element: 'yearly-bar-charts',
        data: yearly_in_exp,
        xkey: 'y',
        ykeys: ['a', 'b'],
        labels: ['Expected Income', 'Expected Expense'],
        lineColors: ['#667eea', '#764ba2'],
        lineWidth: '3px',
        barColors: ['#667eea', '#764ba2'],
        resize: true,
        redraw: true
    });

    // Yearly projects bar charts
    var a = {!! $project_expense_and_invoice !!};
    var proj_in_exp = a[0];
    var chartInstance = Morris.Bar({
        element: 'proj-bar-charts',
        data: proj_in_exp,
        xkey: 'y',
        ykeys: ['a', 'b'],
        labels: ['Income', 'Expense'],
        lineColors: ['#667eea', '#764ba2'],
        lineWidth: '3px',
        barColors: ['#667eea', '#764ba2'],
        resize: true,
        redraw: true
    });
    $(".page-link").click(
        function(){
            var selectedIndex=$(this).val();
            var a = {!! $project_expense_and_invoice !!};
            var dataToShow = a[selectedIndex]
             chartInstance.setData(dataToShow);
             $(".page-link").removeClass("selected");

             $(this).addClass("selected")
        }
    )
});
</script>
@endsection