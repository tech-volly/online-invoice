<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\QuaterReportExport;
use App\Exports\QuarterComparisonExport;
use App\Exports\PNLReportExport;
use App\Exports\ProjectReportExport;
use App\Exports\ExpenseReportExport;
use App\Exports\ForecastReportExport;
use App\Exports\CashFlowReportExport;
use App\Exports\ExpectedExpenseReportExport;
use App\Models\ExpectedExpense;
use App\Models\Expense;
use Excel;
use App\Models\Project;
use App\Models\ExpenseCategory;

class ReportController extends Controller
{
    function __construct() {
        $this->middleware('permission:report-list', ['only' => ['index','quaterComparison', 'quaterReport', 'pnlReport', 'forecastReport', 'cashflowReport']]);
    }

    public function index() {
        $projects = Project::orderBy('name', 'asc')->whereIsStatus(1)->get();
        $expense_categories = ExpenseCategory::orderBy('name', 'asc')->whereIsStatus(1)->get();
        //return view('reports.index');
        return view('reports.index', compact('projects','expense_categories'));
    }

    public function quaterComparison(Request $request) {
        $params = $request->get('year');
        $next_year = $params + 1;
        $report_name = 'Qtr Comparison '.$params.'-'.$next_year.'.xlsx';
        
        return Excel::download(new QuarterComparisonExport($params), $report_name, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function quaterReport(Request $request) {
        $params = $request->all();
        $year = $params['year'];
        $quarter = $params['quarter'];
        $next_year = $year + 1;
        if($quarter == 'quarter1') {
            $quarter_name = 'Quarter 1';
        }
        if($quarter == 'quarter2') {
            $quarter_name = 'Quarter 2';   
        }
        if($quarter == 'quarter3') {
            $quarter_name = 'Quarter 3';   
        }
        if($quarter == 'quarter4') {
            $quarter_name = 'Quarter 4';   
        }
        $report_name = $quarter_name.' '.$year.'-'.$next_year.'.xlsx';
        return Excel::download(new QuaterReportExport($params), $report_name, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function pnlReport(Request $request) {
        $params = $request->all();

        return Excel::download(new PNLReportExport($params), 'P&LReport.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function forecastReport(Request $request) {
        $params = $request->all();
        $date = $params['to_date'];
        $report_name = 'Income Forecast Upto '.$date.'.xlsx' ;

        return Excel::download(new ForecastReportExport($params), $report_name, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function cashflowReport(Request $request) {
        $params = $request->all();

        return Excel::download(new CashFlowReportExport($params), 'CashFlowReports.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function expectedExpenseReport(Request $request) {
        $params = $request->all();
        $expected_expense = ExpectedExpense::with(['epxected_expense_list'])->whereExpectedExpenseYear($params['expected_expense_year'])->first();
        if(!$expected_expense) {
            return redirect()->route('reports')->with('danger', 'There is no expected expense for selected year.');
        }else {
            if($expected_expense->epxected_expense_list->isEmpty()) {
                return redirect()->route('reports')->with('danger', 'There is no expected expense for selected year.');
            }
        }
        $report_name = 'Expected Expense '.str_replace(' ', '', $params['expected_expense_year']).'.xlsx';

        return Excel::download(new ExpectedExpenseReportExport($params), $report_name, \Maatwebsite\Excel\Excel::XLSX);
    }

    //projectReport
    public function projectReport(Request $request) {
        $params = $request->all();

        return Excel::download(new ProjectReportExport($params), 'ProjectReport.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    //expenseReport
    public function expenseReport(Request $request) {
        $params = $request->all();
        $expense_year=explode(" - ",$params['expense_year']);
        $expense_arr = [];
        $start_date =  $expense_year[0].'-07-01';
        $end_date =  $expense_year[1].'-06-30';

        $expensesQuery = Expense::with(['supplier', 'payment_method'])
        ->leftJoin('projects', 'expenses.project_id', '=', 'projects.id')
        ->whereBetween('expense_date', [$start_date, $end_date])
        ->select('expenses.*', 'projects.name as exp_project_name');
        
        if(isset($params['category_id']) && !empty($params['category_id'])){
            $category_ids=$params['category_id'];
           
            $category_ids_array = explode(",", $category_ids);
            $expensesQuery->whereIn('supplier_expense_category', $category_ids_array);
        }
        
        $expenses = $expensesQuery->get();
       
      
        $expense_arr = $expenses->toArray();

        if(!$expense_arr) {
            return redirect()->route('reports')->with('danger', 'There is no  expense for selected year.');
        }
        return Excel::download(new ExpenseReportExport($params), 'ExpenseReport.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

}
