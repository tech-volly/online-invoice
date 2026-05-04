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
                                <td>No Data Available</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
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

<!-- Sales vs Expenses Trend Chart Section -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #667eea;">
                <h3 class="card-title mb-0">Sales vs Expenses Trend Analysis</h3>
            </div>
            <div class="card-body">
                <canvas id="salesVsExpensesTrendChart" style="height: 350px;"></canvas>
            </div>
        </div>
    </div>
</div>

<br /><br />

<!-- Quarterly Comparison Chart Section -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #667eea;">
                <h3 class="card-title mb-0">Quarterly Comparison - Current Year vs Previous 2 Years</h3>
            </div>
            <div class="card-body">
                <canvas id="quarterlyComparisonChart" style="height: 350px;"></canvas>
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

<!-- Client Revenue Section -->
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12 text-center total-income">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header" style="background-color: #667eea;">
                            <h3 class="card-title mb-0">Top Clients by Revenue (FY {{ $data['current_year'] }}-{{ $data['current_year'] + 1 }})</h3>
                            <div class="card-header-actions">
                                <!-- <button class="btn btn-sm btn-success" onclick="exportClientRevenue('xlsx')">
                                    <i class="fa fa-download"></i> Excel
                                </button> -->
                                <button class="btn btn-sm btn-danger" onclick="exportClientRevenue('pdf')">
                                    <i class="fa fa-file-pdf-o"></i> PDF
                                </button>
                            </div>
                        </div>
                        <div class="chart-container" style="position: relative; height: 400px; margin-bottom: 20px;">
                            <canvas id="clientRevenueChart"></canvas>
                        </div>

                        <!-- Revenue Summary Cards -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                    <div class="card-body text-center">
                                        <h5>Current Year Revenue</h5>
                                        <h3>${{ number_format($data['total_current_revenue'], 2) }}</h3>
                                        <small>FY {{ $data['current_year'] }}-{{ $data['current_year'] + 1 }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card" style="background: linear-gradient(135deg, #4b9c7e 0%, #2f6b52 100%); color: white;">
                                    <div class="card-body text-center">
                                        <h5>Previous Year Revenue</h5>
                                        <h3>${{ number_format($data['total_previous_revenue'], 2) }}</h3>
                                        <small>FY {{ $data['previous_year'] }}-{{ $data['current_year'] }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                                    <div class="card-body text-center">
                                        <h5>Year-over-Year Change</h5>
                                        <h3>${{ number_format($data['total_revenue_difference'], 2) }}</h3>
                                        @if($data['total_revenue_difference'] > 0)
                                            <span class="badge badge-success">
                                                <i class="fa fa-arrow-up"></i>
                                                {{ number_format(($data['total_revenue_difference'] / $data['total_previous_revenue'] * 100), 2) }}%
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fa fa-arrow-down"></i>
                                                {{ number_format(abs($data['total_revenue_difference'] / $data['total_previous_revenue'] * 100), 2) }}%
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Client Revenue Table -->
                        <!-- <div class="table-responsive">
                            <table class="table table-hover">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th>Client #</th>
                                        <th>Client Name</th>
                                        <th class="text-right">Current Year</th>
                                        <th class="text-right">Previous Year</th>
                                        <th class="text-right">Difference</th>
                                        <th class="text-right">Change %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data['client_revenue_data'] as $client)
                                        <tr>
                                            <td>{{ $client['client_number'] }}</td>
                                            <td>{{ $client['client_name'] }}</td>
                                            <td class="text-right font-weight-bold">${{ number_format($client['current_revenue'], 2) }}</td>
                                            <td class="text-right">${{ number_format($client['previous_revenue'], 2) }}</td>
                                            <td class="text-right">
                                                @if($client['difference'] > 0)
                                                    <span class="badge badge-success"><i class="fa fa-arrow-up"></i> ${{ number_format($client['difference'], 2) }}</span>
                                                @elseif($client['difference'] < 0)
                                                    <span class="badge badge-danger"><i class="fa fa-arrow-down"></i> ${{ number_format(abs($client['difference']), 2) }}</span>
                                                @else
                                                    <span class="badge badge-secondary">No Change</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                @if($client['percentage_change'] > 0)
                                                    <span class="badge badge-success">+{{ $client['percentage_change'] }}%</span>
                                                @elseif($client['percentage_change'] < 0)
                                                    <span class="badge badge-danger">{{ $client['percentage_change'] }}%</span>
                                                @else
                                                    <span class="badge badge-secondary">0%</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                No client revenue data available
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div> -->
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
                                <td>No Data Available</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
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
                                <td>No Data Available</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
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
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<!-- Morris JS -->
<script src="{{ URL::asset('public/assets/libs/morris/morris.min.js')}}"></script>
<script src="{{ URL::asset('public/assets/libs/raphael/raphael.min.js')}}"></script>
<script src="{{ URL::asset('public/assets/js/pages/dashboard.js')}}"></script>
<script type="text/javascript">
// Export Client Revenue Report - Global function
function exportClientRevenue(format) {
    var currentYear = '{{ $data["current_year"] }}';
    var previousYear = '{{ $data["previous_year"] }}';

    var url = '{{ route("dashboard.client-revenue.export") }}?year=' + currentYear + '&compare_year=' + previousYear + '&format=' + format;
    window.location = url;
}

$(document).ready(function () {
    // Quarterly Comparison Chart
    var quarterlyData = {!! $quarterly_comparison_json !!};
    var fyLabel = "{{ $fy_label }}";
    var fyLabel1 = "{{ $fy_label_1 }}";
    var fyLabel2 = "{{ $fy_label_2 }}";
    
    var quarters = quarterlyData.map(item => item.quarter);
    var currentYearData = quarterlyData.map(item => item.current);
    var previousYear1Data = quarterlyData.map(item => item.previous_1);
    var previousYear2Data = quarterlyData.map(item => item.previous_2);
    
    var ctx = document.getElementById('quarterlyComparisonChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: quarters,
                // datasets: [
                //      {
                //         label: fyLabel2,
                //         data: previousYear2Data,
                //         backgroundColor: '#f59e0b',
                //         borderColor: '#f59e0b',
                //         borderWidth: 1
                //     },
                //     {
                //         label: fyLabel1,
                //         data: previousYear1Data,
                //         backgroundColor: '#764ba2',
                //         borderColor: '#764ba2',
                //         borderWidth: 1
                //     },
                //     {
                //         label: fyLabel,
                //         data: currentYearData,
                //         backgroundColor: '#667eea',
                //         borderColor: '#667eea',
                //         borderWidth: 1
                //     }
                   
                // ]
                datasets: [
                    {
                        label: fyLabel2,
                        data: previousYear2Data,
                        backgroundColor: previousYear2Data.map(v => v >= 0 ? '#f59e0b' : '#ef4444'),
                        borderColor:     previousYear2Data.map(v => v >= 0 ? '#f59e0b' : '#ef4444'),
                        borderWidth: 1
                    },
                    {
                        label: fyLabel1,
                        data: previousYear1Data,
                        backgroundColor: previousYear1Data.map(v => v >= 0 ? '#764ba2' : '#ef4444'),
                        borderColor:     previousYear1Data.map(v => v >= 0 ? '#764ba2' : '#ef4444'),
                        borderWidth: 1
                    },
                    {
                        label: fyLabel,
                        data: currentYearData,
                        backgroundColor: currentYearData.map(v => v >= 0 ? '#667eea' : '#ef4444'),
                        borderColor:     currentYearData.map(v => v >= 0 ? '#667eea' : '#ef4444'),
                        borderWidth: 1
                    }
                ]
            },
            // options: {
            //     responsive: true,
            //     maintainAspectRatio: true,
            //     plugins: {
            //         legend: {
            //             display: true,
            //             position: 'top',
            //             labels: {
            //                 padding: 15,
            //                 font: {
            //                     size: 12,
            //                     weight: '500'
            //                 }
            //             }
            //         },
            //         tooltip: {
            //             callbacks: {
            //                 label: function(context) {
            //                     var label = context.dataset.label || '';
            //                     if (label) {
            //                         label += ': ';
            //                     }
            //                     label += '$' + context.parsed.y.toLocaleString();
            //                     return label;
            //                 }
            //             }
            //         }
            //     },
            //     scales: {
            //         y: {
            //             beginAtZero: true,
            //             ticks: {
            //                 callback: function(value) {
            //                     return '$' + value.toLocaleString();
            //                 }
            //             }
            //         }
            //     }
            // }
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { padding: 15, font: { size: 12, weight: '500' } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var val = context.parsed.y;
                                var sign = val >= 0 ? '+' : '';
                                return context.dataset.label + ' Profit: ' + sign + '$' + val.toLocaleString('en-US', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,   // allow negative values to show below 0
                        ticks: {
                            callback: function(value) {
                                return (value >= 0 ? '+' : '') + '$' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: function(context) {
                                // highlight the zero line
                                return context.tick.value === 0 ? 'rgba(0,0,0,0.3)' : 'rgba(0,0,0,0.05)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Sales vs Expenses Trend Chart
    var trendData = {!! $sales_vs_expenses_trend !!};
    var trendCtx = document.getElementById('salesVsExpensesTrendChart');
    if (trendCtx && trendData && trendData.length > 0) {
        var trendMonths = trendData.map(item => item.month);
        var trendSales = trendData.map(item => parseFloat(item.sales) || 0);
        var trendExpenses = trendData.map(item => parseFloat(item.expenses) || 0);
        var trendProfit = trendData.map(item => parseFloat(item.profit) || 0);
        
        new Chart(trendCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: trendMonths,
                datasets: [
                    {
                        label: 'Total Sales',
                        data: trendSales,
                         borderColor: '#28a745', // Green
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#667eea',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Total Expenses',
                        data: trendExpenses,
                        borderColor: '#dc3545', // Red
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#764ba2',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    // {
                    //     label: 'Profit Margin',
                    //     data: trendProfit,
                    //     borderColor: '#10b981',
                    //     backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    //     borderWidth: 2,
                    //     fill: true,
                    //     tension: 0.4,
                    //     pointRadius: 4,
                    //     pointBackgroundColor: '#10b981',
                    //     pointBorderColor: '#fff',
                    //     pointBorderWidth: 2
                    // }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '$' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                            }
                        }
                    }
                }
            }
        });
    }

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

    // Client Revenue Chart
    @if(isset($clientRevenueChartData))
    var clientRevenueCtx = document.getElementById('clientRevenueChart');
    if (clientRevenueCtx) {
        var clientRevenueData = {!! json_encode($clientRevenueChartData) !!};

        new Chart(clientRevenueCtx.getContext('2d'), {
            type: 'bar',
            data: clientRevenueData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Top 10 Clients by Revenue Comparison',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        bodyFont: {
                            size: 12
                        },
                        callbacks: {
                            label: function(context) {
                                return context.datasetIndex + 1 + ' FY: $' + context.parsed.y.toFixed(2);
                            },
                            afterLabel: function(context) {
                                if (clientRevenueData.datasets.length > 1 && context.datasetIndex === 0 && clientRevenueData.datasets[1]) {
                                    var dataset2Value = clientRevenueData.datasets[1].data[context.dataIndex];
                                    var difference = context.parsed.y - dataset2Value;
                                    var percentChange = dataset2Value > 0 ? ((difference / dataset2Value) * 100) : 0;

                                    return 'Change: $' + difference.toFixed(2) + ' (' + percentChange.toFixed(2) + '%)';
                                }
                                return '';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return '$' + (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return '$' + (value / 1000).toFixed(1) + 'K';
                                }
                                return '$' + value.toFixed(0);
                            }
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                }
            }
        });
    }
    @endif

});
</script>
@endsection