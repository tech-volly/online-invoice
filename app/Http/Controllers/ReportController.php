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
use App\Exports\ClientStatementExport;
use App\Models\Client;
use App\Models\ExpectedExpense;
use App\Models\Expense;
use Excel;
use App\Models\Project;
use App\Models\ExpenseCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:report-list', ['only' => ['index', 'quaterComparison', 'quaterReport', 'pnlReport', 'forecastReport', 'cashflowReport']]);
    }

    public function index()
    {
        $projects = Project::orderBy('name', 'asc')->whereIsStatus(1)->get();
        $clients = Client::orderBy('client_business_name', 'asc')->get();
        $expense_categories = ExpenseCategory::orderBy('name', 'asc')->whereIsStatus(1)->get();
        //return view('reports.index');
        return view('reports.index', compact('projects', 'clients', 'expense_categories'));
    }

    public function quaterComparison(Request $request)
    {
        $params = $request->get('year');
        $next_year = $params + 1;
        $report_name = 'Qtr Comparison ' . $params . '-' . $next_year . '.xlsx';

        return Excel::download(new QuarterComparisonExport($params), $report_name, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function quaterReport(Request $request)
    {
        $params = $request->all();
        $year = $params['year'];
        $quarter = $params['quarter'];
        $next_year = $year + 1;
        if ($quarter == 'quarter1') {
            $quarter_name = 'Quarter 1';
        }
        if ($quarter == 'quarter2') {
            $quarter_name = 'Quarter 2';
        }
        if ($quarter == 'quarter3') {
            $quarter_name = 'Quarter 3';
        }
        if ($quarter == 'quarter4') {
            $quarter_name = 'Quarter 4';
        }
        $report_name = $quarter_name . ' ' . $year . '-' . $next_year . '.xlsx';
        return Excel::download(new QuaterReportExport($params), $report_name, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function pnlReport(Request $request)
    {
        $params = $request->all();

        return Excel::download(new PNLReportExport($params), 'P&LReport.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function forecastReport(Request $request)
    {
        $params = $request->all();
        $date = $params['to_date'];
        $report_name = 'Income Forecast Upto ' . $date . '.xlsx';

        return Excel::download(new ForecastReportExport($params), $report_name, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function cashflowReport(Request $request)
    {
        $params = $request->all();

        return Excel::download(new CashFlowReportExport($params), 'CashFlowReports.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function expectedExpenseReport(Request $request)
    {
        $params = $request->all();
        $expected_expense = ExpectedExpense::with(['epxected_expense_list'])->whereExpectedExpenseYear($params['expected_expense_year'])->first();
        if (!$expected_expense) {
            return redirect()->route('reports')->with('danger', 'There is no expected expense for selected year.');
        } else {
            if ($expected_expense->epxected_expense_list->isEmpty()) {
                return redirect()->route('reports')->with('danger', 'There is no expected expense for selected year.');
            }
        }
        $report_name = 'Expected Expense ' . str_replace(' ', '', $params['expected_expense_year']) . '.xlsx';

        return Excel::download(new ExpectedExpenseReportExport($params), $report_name, \Maatwebsite\Excel\Excel::XLSX);
    }

    //projectReport
    public function projectReport(Request $request)
    {
        $params = $request->all();

        return Excel::download(new ProjectReportExport($params), 'ProjectReport.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    //expenseReport
    public function expenseReport(Request $request)
    {
        $params = $request->all();
        $expense_year = explode(" - ", $params['expense_year']);
        $expense_arr = [];
        $start_date =  $expense_year[0] . '-07-01';
        $end_date =  $expense_year[1] . '-06-30';

        $expensesQuery = Expense::with(['supplier', 'payment_method'])
            ->leftJoin('projects', 'expenses.project_id', '=', 'projects.id')
            ->whereBetween('expense_date', [$start_date, $end_date])
            ->select('expenses.*', 'projects.name as exp_project_name');

        if (isset($params['category_id']) && !empty($params['category_id'])) {
            $category_ids = $params['category_id'];

            $category_ids_array = explode(",", $category_ids);
            $expensesQuery->whereIn('supplier_expense_category', $category_ids_array);
        }

        $expenses = $expensesQuery->get();


        $expense_arr = $expenses->toArray();

        if (!$expense_arr) {
            return redirect()->route('reports')->with('danger', 'There is no  expense for selected year.');
        }
        return Excel::download(new ExpenseReportExport($params), 'ExpenseReport.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    // public function clientStatement(Request $request) {
    //     $request->validate([
    //         'client_id' => 'required|exists:clients,id',
    //         'from_date' => 'nullable',
    //         'to_date' => 'nullable',
    //     ]);

    //     $params = $request->all();

    //     return Excel::download(new ClientStatementExport($params), 'ClientStatement.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    // }

    public function clientStatement(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'from_date' => 'nullable',
            'to_date' => 'nullable',
        ]);

        $from_year_month = splitYearMonth($request->from_date);
        $to_year_month = splitYearMonth($request->to_date);

        $from_date = $from_year_month ? $from_year_month . '-01' : null;
        $to_date = $to_year_month ? date("Y-m-t", strtotime($to_year_month)) : null;

        $query = DB::table('invoices')
            ->leftJoin('clients', 'clients.id', '=', 'invoices.client_id')
            ->leftJoin('payment_statuses', 'payment_statuses.id', '=', 'invoices.payment_status_id')
            ->whereNull('invoices.deleted_at')
            ->where('invoices.client_id', $request->client_id);

        if ($from_date) {
            $query->where('invoices.invoice_due_date', '>=', $from_date);
        }

        if ($to_date) {
            $query->where('invoices.invoice_due_date', '<=', $to_date);
        }

        $invoices = $query->select(
            'invoices.id',
            'invoices.invoice_number',
            'clients.client_business_name',
            'clients.client_first_name',
            'clients.client_last_name',
            'invoices.invoice_due_date',
            'payment_statuses.name as payment_status',
            'invoices.invoice_payment_date',
            'invoices.invoice_grand_total'
        )
            ->orderBy('invoices.invoice_due_date', 'asc')
            // dd($query->toSql(), $query->getBindings());
             ->get();

        $data = [];
        $totalInvoice = 0;
        $totalPaid = 0;
        $totalOutstanding = 0;

        foreach ($invoices as $value) {

            $invoiceAmount = (float) $value->invoice_grand_total;

            // ✅ IMPORTANT LOGIC
            $paidAmount = strtolower($value->payment_status) == 'paid'
                ? $invoiceAmount
                : 0;

            $outstanding = $invoiceAmount - $paidAmount;

            $totalInvoice += $invoiceAmount;
            $totalPaid += $paidAmount;
            $totalOutstanding += $outstanding;

            $data[] = [
                'client_name' => $value->client_business_name,
                'shipping_name' => trim($value->client_first_name . ' ' . $value->client_last_name),
                'invoice_number' => $value->invoice_number,
                'due_date' => $value->invoice_due_date ? changeDateFormatAtExport($value->invoice_due_date) : '',
                'payment_status' => $value->payment_status,
                'payment_date' => $value->invoice_payment_date ? changeDateFormatAtExport($value->invoice_payment_date) : '',
                'invoice_amount' => number_format($invoiceAmount, 2),
                'paid_amount' => number_format($paidAmount, 2),
                'outstanding_amount' => number_format($outstanding, 2),
            ];
        }

        // TOTAL
        $data[] = [
            'client_name' => '',
            'shipping_name' => '',
            'invoice_number' => 'TOTAL',
            'due_date' => '',
            'payment_status' => '',
            'payment_date' => '',
            'invoice_amount' => number_format($totalInvoice, 2),
            'paid_amount' => number_format($totalPaid, 2),
            'outstanding_amount' => number_format($totalOutstanding, 2),
        ];

        $pdf = Pdf::loadView('reports.client_statement_pdf', compact('data'));
        return $pdf->download('client-statement.pdf');
    }
}
