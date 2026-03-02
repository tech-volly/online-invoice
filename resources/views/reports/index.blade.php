<?php $page = "reports"; ?>
@extends('layout.mainlayout')
@section('content')    
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Reports</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Reports</li>
            </ul>
        </div>
    </div>
</div>    
@include('layout.flash-message')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Quarter Comparison Report</h4>
            </div>
            <div class="card-body">
                <div class="row filter-row">
                    <div class="col-sm-6 col-md-3"> 
                        <div class="form-group form-focus select-focus">
                            <select class="form-control select_year_value floating" id="year_value"> 
                                <option selected disabled>Select Year</option>
                                <option value="2020">2020 - 2021</option>
                                <option value="2021">2021 - 2022</option>
                                <option value="2022">2022 - 2023</option>
                                <option value="2023">2023 - 2024</option>
                                <option value="2024">2024 - 2025</option>
                                <option value="2025">2025 - 2026</option>
                                <option value="2026">2026 - 2027</option>
                                <option value="2027">2027 - 2028</option>
                                <option value="2028">2028 - 2029</option>
                                <option value="2029">2029 - 2030</option>
                                <option value="2030">2030 - 2031</option>
                            </select>
                            <label class="focus-label">Year</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3"></div>
                    <div class="col-sm-6 col-md-3">
                        <a href="{{route('reports.quarter-comparison')}}" class="btn btn-primary report-btn" id="download_quater_comparison"> 
                            <i class="fa fa-download"></i> Download 
                        </a>  
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
                <h4 class="card-title mb-0">Quarter Report</h4>
            </div>
            <div class="card-body">
                <div class="row filter-row">
                    <div class="col-sm-6 col-md-3"> 
                        <div class="form-group form-focus select-focus">
                            <select class="form-control select_year_value floating" id="select_quarter"> 
                                <option selected disabled>Select Quarter</option>
                                <option value="quarter1">Quarter 1</option>
                                <option value="quarter2">Quarter 2</option>
                                <option value="quarter3">Quarter 3</option>
                                <option value="quarter4">Quarter 4</option>
                            </select>
                            <label class="focus-label">Quarter</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3"> 
                        <div class="form-group form-focus select-focus">
                            <select class="form-control select_year_value floating" id="select_quarter_year"> 
                                <option selected disabled>Select Year</option>
                                <option value="2020">2020 - 2021</option>
                                <option value="2021">2021 - 2022</option>
                                <option value="2022">2022 - 2023</option>
                                <option value="2023">2023 - 2024</option>
                                <option value="2024">2024 - 2025</option>
                                <option value="2025">2025 - 2026</option>
                                <option value="2026">2026 - 2027</option>
                                <option value="2027">2027 - 2028</option>
                                <option value="2028">2028 - 2029</option>
                                <option value="2029">2029 - 2030</option>
                                <option value="2030">2030 - 2031</option>
                            </select>
                            <label class="focus-label">Year</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <a href="{{route('reports.quarter-report')}}" class="btn btn-primary report-btn" id="download_quater_report"> 
                            <i class="fa fa-download"></i> Download  
                        </a>  
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
                <h4 class="card-title mb-0">P&L Report</h4>
            </div>
            <div class="card-body">
                <div class="row filter-row">
                    <div class="col-sm-6 col-md-3"> 
                        <div class="form-group form-focus">
                            <div class="cal-icon">
                                <input class="form-control floating datetimepicker" type="text" id="from_date">
                            </div>
                            <label class="focus-label">From</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3"> 
                        <div class="form-group form-focus">
                            <div class="cal-icon">
                                <input class="form-control floating datetimepicker" type="text" id="to_date">
                            </div>
                            <label class="focus-label">To</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <a href="{{route('reports.pnl-report')}}" class="btn btn-primary report-btn" id="download_pnl_report"> 
                            <i class="fa fa-download"></i> Download  
                        </a>  
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
                <h4 class="card-title mb-0">Income Forecast Report</h4>
            </div>
            <div class="card-body">
                <div class="row filter-row">
                    <div class="col-sm-6 col-md-3"> 
                        <div class="form-group form-focus">
                            <div class="cal-icon">
                                <input class="form-control floating forecastDateTimePicker" type="text" id="forecast_to_date">
                            </div>
                            <label class="focus-label">To</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3"></div>
                    <div class="col-sm-6 col-md-3">
                        <a href="{{route('reports.forecast-report')}}" class="btn btn-primary report-btn" id="download_forecast_report"> 
                            <i class="fa fa-download"></i> Download  
                        </a>  
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
                <h4 class="card-title mb-0">Cashflow Report</h4>
            </div>
            <div class="card-body">
                <div class="row filter-row">
                    <div class="col-sm-6 col-md-3"> 
                        <div class="form-group form-focus">
                            <div class="cal-icon">
                                <input class="form-control floating cashflowDateTimePicker" type="text" id="from_year_month">
                            </div>
                            <label class="focus-label">From</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3"> 
                        <div class="form-group form-focus">
                            <div class="cal-icon">
                                <input class="form-control floating cashflowDateTimePicker" type="text" id="to_year_month">
                            </div>
                            <label class="focus-label">To</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <a href="{{route('reports.cashflow-report')}}" class="btn btn-primary report-btn" id="download_cashflow_report"> 
                            <i class="fa fa-download"></i> Download  
                        </a>  
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
                <h4 class="card-title mb-0">Expected Expense Report</h4>
            </div>
            <div class="card-body">
                <div class="row filter-row">
                    <div class="col-sm-6 col-md-3"> 
                        <div class="form-group form-focus select-focus">
                            <select class="form-control select_year_value floating" id="expected_expense_year"> 
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
                            <label class="focus-label">Year</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3"></div>
                    <div class="col-sm-6 col-md-3">
                        <a href="{{route('reports.expected-expense-report')}}" class="btn btn-primary report-btn" id="download_expected_expense"> 
                            <i class="fa fa-download"></i> Download 
                        </a>  
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
                <h4 class="card-title mb-0">Project Report</h4>
            </div>
            <div class="card-body">
                <div class="row filter-row">
                 
                    <div class="col-sm-6 col-md-3"> 
                        <div class="form-group form-focus select-focus">
                            <!-- <div class="cal-icon select-focus" > -->
                                <select class="form-control select_year_value floating" name="project_id" id="project_id">
                                        <option selected disabled>Select Project</option>
                                        @foreach($projects as $project)
                                        <option value="{{$project->id}}">
                                            {{$project->name}}
                                        </option>
                                        @endforeach
                                    </select>
                                <!-- </div> -->
                            <label class="focus-label">Project</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <a href="{{route('reports.project-report')}}" class="btn btn-primary report-btn" id="download_project_report"> 
                            <i class="fa fa-download"></i> Download  
                        </a>  
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
                <h4 class="card-title mb-0">Expense Report</h4>
            </div>
            <div class="card-body">
                <div class="row filter-row">
                    <div class="col-sm-6 col-md-3"> 
                    <label class="focus-label"></label>
                        <div class="form-group form-focus select-focus">
                             <select class="form-control select_year_value floating" id="expense_year"> 
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
                            <label class="focus-label">Year</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3"> 
                    <label class="focus-label">Expense Category</label>
                        <div class="form-group form-focus select-focus expense-category">
                            <select class="form-control select_year_value floating expense-category" id="category_id" name="category_id[]" multiple> 
                                <!-- <option selected disabled>Select Expense Category</option> -->
                                @foreach($expense_categories as $category)
                                        <option value="{{$category->id}}">
                                            {{$category->name}}
                                        </option>
                                @endforeach
                            </select>
                            <!-- <label class="focus-label">Expense Category</label> -->
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <a href="{{route('reports.expense-report')}}" class="btn btn-primary report-btn" id="download_expense_report"> 
                            <i class="fa fa-download"></i> Download  
                        </a>  
                    </div>     
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ URL::asset('public/assets/js/pages/reports.js')}}"></script>
@endsection