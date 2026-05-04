<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Subscription;
use App\Models\ExpectedExpense;
use App\Models\Quote;
use App\Exports\ClientRevenueReportExport;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use DatePeriod;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index()
    {

        // Current month total sales and expense starts
        $invoices = DB::table('invoices')->whereMonth('invoice_payment_date', Carbon::now()->month)
            ->whereYear('invoice_payment_date', Carbon::now()->year)
            ->where('invoices.deleted_at', '=', null)
            ->select(DB::raw("SUM(invoice_grand_total) as invoice_grand_total"))
            ->get();

        $expenses = DB::table('expenses')->whereMonth('expense_date', Carbon::now()->month)
            ->whereYear('expense_date', Carbon::now()->year)
            ->where('expenses.deleted_at', '=', null)
            ->select(DB::raw("SUM(expense_amount) as expense_total"))
            ->get();
        // Current month total sales and expense ends

        $current_month = Carbon::now()->month;
        if ($current_month >= 7 && $current_month <= 12) {
            $current_year = Carbon::now()->year;
            $next_year = date('Y', strtotime('+1 year'));
        } else {
            $current_year = Carbon::now()->year - 1;
            $next_year = Carbon::now()->year;
        }

        $curr_year_exp_inc_exp = Carbon::now()->year;

        $q1_start_date = $current_year . '-07-01';
        $q1_end_date = $current_year . '-09-30';

        $q2_start_date = $current_year . '-10-01';
        $q2_end_date = $current_year . '-12-31';

        $q3_start_date = $next_year . '-01-01';
        $q3_end_date = $next_year . '-03-31';

        $q4_start_date = $next_year . '-04-01';
        $q4_end_date = $next_year . '-06-30';

        // GST paid and collected starts
        $q1_gst_paid = gstPaidByQuarter($q1_start_date, $q1_end_date);
        $q1_gst_collected = gstCollectedByQuarter($q1_start_date, $q1_end_date);

        $q2_gst_paid = gstPaidByQuarter($q2_start_date, $q2_end_date);
        $q2_gst_collected = gstCollectedByQuarter($q2_start_date, $q2_end_date);

        $q3_gst_paid = gstPaidByQuarter($q3_start_date, $q3_end_date);
        $q3_gst_collected = gstCollectedByQuarter($q3_start_date, $q3_end_date);

        $q4_gst_paid = gstPaidByQuarter($q4_start_date, $q4_end_date);
        $q4_gst_collected = gstCollectedByQuarter($q4_start_date, $q4_end_date);
        // GST paid and collected ends

        // List on unpaid invoices starts
        $unpaind_invoices = Invoice::with(['client', 'payment_status'])
            ->whereHas('payment_status', function ($query) {
                $query->where('name', '=', 'Unpaid');
            })->orderBY('id', 'desc')
            ->get();
        // List on unpaid invoices ends

        // Income and Expense quarter starts 
        $q1_income = totalIncomeByQuarter($q1_start_date, $q1_end_date);
        $q1_expense = totalExpenseByQuarter($q1_start_date, $q1_end_date);
        $q1_profit = totalProfitByQuarter($q1_income, $q1_expense);
        $q1_profit_per = getProfitForQuarterReport($q1_profit, $q1_income);

        $q2_income = totalIncomeByQuarter($q2_start_date, $q2_end_date);
        $q2_expense = totalExpenseByQuarter($q2_start_date, $q2_end_date);
        $q2_profit = totalProfitByQuarter($q2_income, $q2_expense);
        $q2_profit_per = getProfitForQuarterReport($q2_profit, $q2_income);

        $q3_income = totalIncomeByQuarter($q3_start_date, $q3_end_date);
        $q3_expense = totalExpenseByQuarter($q3_start_date, $q3_end_date);
        $q3_profit = totalProfitByQuarter($q3_income, $q3_expense);
        $q3_profit_per = getProfitForQuarterReport($q3_profit, $q3_income);

        $q4_income = totalIncomeByQuarter($q4_start_date, $q4_end_date);
        $q4_expense = totalExpenseByQuarter($q4_start_date, $q4_end_date);
        $q4_profit = totalProfitByQuarter($q4_income, $q4_expense);
        $q4_profit_per = getProfitForQuarterReport($q4_profit, $q4_income);
        // Income and Expense quarter ends

        // List of Recurring invoices starts
        $subscriptions = Subscription::with(['client', 'brand', 'subscription_payments.product'])
            ->whereDate('subscription_next_date', '>=', Carbon::now())
            ->orderBy('subscription_next_date', 'asc')
            ->get();
        // List of Recurring invoices ends


        // Get data for income-expense chart starts
        $expense_of_year = DB::table('expenses')
            ->whereBetween('expense_date', [$q1_start_date, $q4_end_date])
            ->where('expenses.deleted_at', '=', null)
            ->select(
                \DB::raw('SUM(expense_amount) as exp_total'),
                \DB::raw("EXTRACT(MONTH FROM `expense_date`) as month"),
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
        $income_of_year = DB::table('invoices')
            ->whereBetween('invoice_payment_date', [$q1_start_date, $q4_end_date])
            ->where('invoices.deleted_at', '=', null)
            ->select(
                \DB::raw('SUM(invoice_grand_total) as inc_total'),
                \DB::raw("EXTRACT(MONTH FROM `invoice_payment_date`) as month"),
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
        $new_exp_arr = [];
        $final_arr = [];
        $month_arr = [7, 8, 9, 10, 11, 12, 01, 02, 03, 04, 05, 06];
        $month_name_arr = ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        foreach ($expense_of_year as $key => $val) {
            $new_exp_arr[$val->month] = $val->exp_total;
        }
        $new_inc_arr = [];
        foreach ($income_of_year as $key => $val) {
            $new_inc_arr[$val->month] = $val->inc_total;
        }
        foreach ($month_arr as $key => $val) {
            $final_arr[$val]['y'] = $val;
            if (isset($new_inc_arr[$val])) {
                $final_arr[$val]['a'] = round($new_inc_arr[$val], 2);
            } else {
                $final_arr[$val]['a'] = 0;
            }
            if (isset($new_exp_arr[$val])) {
                $final_arr[$val]['b'] = round($new_exp_arr[$val], 2);
            } else {
                $final_arr[$val]['b'] = 0;
            }
        }
        $final_arr_values = array_values($final_arr);

        $loop_count = 0;
        foreach ($final_arr_values as $key => $val) {
            $loop_count++;
            if ($loop_count <= 6) {
                if ($current_month >= 1 && $current_month <= 6) {
                    $month_year = date('Y') - 1;
                } else {
                    $month_year = date('Y');
                }
            } else {
                if ($current_month >= 1 && $current_month <= 6) {
                    $month_year = date('Y');
                } else {
                    $month_year = date('Y') + 1;
                }
            }
            $month_year = substr($month_year, 2);
            $final_arr_values[$key]['y'] = $month_name_arr[$key] . " - " . $month_year;
        }
        $income_expense_arr = json_encode($final_arr_values);
        // Get data for income-expense chart ends

        //Get data for expected income-expense chart starts
        $month_val = Carbon::now()->format('m');
        $in_ex_start_date = $curr_year_exp_inc_exp . '-' . $month_val . '-01';
        $change_date_format = Carbon::create($in_ex_start_date);
        $add_year_in_date = $change_date_format->addMonths(11);
        $in_ex_end_date = Carbon::parse($add_year_in_date)->endOfMonth()->toDateString();
        $start = new DateTime($in_ex_start_date);
        $end = new DateTime($in_ex_end_date);
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);
        $month_cur_next_arr = [];
        $month_name_cur_next_arr = [];
        foreach ($period as $key => $dt) {
            array_push($month_cur_next_arr, $dt->format("n"));
        }
        foreach ($period as $key => $dt) {
            array_push($month_name_cur_next_arr, $dt->format("M"));
        }

        $unpaid_status_id = getPaymentStatusId();
        $expected_invoices_year = DB::table('invoices')
            ->leftjoin('payment_statuses', 'payment_statuses.id', '=', 'invoices.payment_status_id')
            ->where('invoices.payment_status_id', '=', $unpaid_status_id)
            ->whereBetween('invoices.invoice_due_date', [$in_ex_start_date, $in_ex_end_date])
            ->where('invoices.deleted_at', '=', null)
            ->select(
                \DB::raw('SUM(invoices.invoice_grand_total) as inc_total'),
                \DB::raw("EXTRACT(MONTH FROM `invoice_due_date`) as month"),
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
        $expected_invoices = [];
        $formated_expected_inv_arr = [];
        foreach ($expected_invoices_year as $key => $val) {
            $expected_invoices[$val->month] = $val->inc_total;
        }

        $expected_subscriptions_year = DB::table('subscriptions')
            ->where('subscriptions.subscription_cycle', '=', 'yearly')
            ->whereBetween('subscriptions.subscription_due_date', [$in_ex_start_date, $in_ex_end_date])
            ->where('subscriptions.deleted_at', '=', null)
            ->select(
                \DB::raw('SUM(subscriptions.subscription_grand_total) as sub_total'),
                \DB::raw("EXTRACT(MONTH FROM `subscription_due_date`) as month"),
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
        $expected_subscriptions = [];
        $formated_expected_sub_arr = [];
        foreach ($expected_subscriptions_year as $key => $val) {
            $expected_subscriptions[$val->month] = $val->sub_total;
        }
        foreach ($month_cur_next_arr as $key => $val) {
            // Invoices
            $formated_expected_inv_arr[$val]['y'] = $val;
            if (isset($expected_invoices[$val])) {
                $formated_expected_inv_arr[$val]['a'] = round($expected_invoices[$val], 2);
            } else {
                $formated_expected_inv_arr[$val]['a'] = 0;
            }
            // Subscriptions
            $formated_expected_sub_arr[$val]['y'] = $val;
            if (isset($expected_subscriptions[$val])) {
                $formated_expected_sub_arr[$val]['a'] = round($expected_subscriptions[$val], 2);
            } else {
                $formated_expected_sub_arr[$val]['a'] = 0;
            }
        }

        $expected_fin_year = $current_year . ' - ' . $next_year;
        if ($month_val == 7) {
            $expected_expense_data = DB::table('expected_expenses')->where('expected_expenses.expected_expense_year', $expected_fin_year)
                ->leftjoin('expected_expense_lists', 'expected_expense_lists.expected_expense_id', '=', 'expected_expenses.id')
                ->where('expected_expenses.deleted_at', '=', null)
                ->where('expected_expense_lists.deleted_at', '=', null)
                ->select(
                    \DB::raw('SUM(expected_expense_lists.expected_july_expense) as jul_exp'),
                    \DB::raw('SUM(expected_expense_lists.expected_aug_expense) as aug_exp'),
                    \DB::raw('SUM(expected_expense_lists.expected_sept_expense) as sep_exp'),
                    \DB::raw('SUM(expected_expense_lists.expected_oct_expense) as oct_exp'),
                    \DB::raw('SUM(expected_expense_lists.expected_nov_expense) as nov_exp'),
                    \DB::raw('SUM(expected_expense_lists.expected_desc_expense) as des_exp'),
                    \DB::raw('SUM(expected_expense_lists.expected_jan_expense) as jan_exp'),
                    \DB::raw('SUM(expected_expense_lists.expected_feb_expense) as feb_exp'),
                    \DB::raw('SUM(expected_expense_lists.expected_mar_expense) as mar_exp'),
                    \DB::raw('SUM(expected_expense_lists.expected_apr_expense) as apr_exp'),
                    \DB::raw('SUM(expected_expense_lists.expected_may_expense) as may_exp'),
                    \DB::raw('SUM(expected_expense_lists.expected_june_expense) as jun_exp')
                )->get();
            $format_expected_exp = [];
            foreach ($expected_expense_data as $key => $val) {
                $format_expected_exp['7'] = $val->jul_exp;
                $format_expected_exp['8'] = $val->aug_exp;
                $format_expected_exp['9'] = $val->sep_exp;
                $format_expected_exp['10'] = $val->oct_exp;
                $format_expected_exp['11'] = $val->nov_exp;
                $format_expected_exp['12'] = $val->des_exp;
                $format_expected_exp['1'] = $val->jan_exp;
                $format_expected_exp['2'] = $val->feb_exp;
                $format_expected_exp['3'] = $val->mar_exp;
                $format_expected_exp['4'] = $val->apr_exp;
                $format_expected_exp['5'] = $val->may_exp;
                $format_expected_exp['6'] = $val->jun_exp;
            }
        } else if ($month_val == 8) {
            $fields1 = 'SUM(expected_expense_lists.expected_aug_expense) as aug_exp, SUM(expected_expense_lists.expected_sept_expense) as sep_exp, SUM(expected_expense_lists.expected_oct_expense) as oct_exp,
            SUM(expected_expense_lists.expected_nov_expense) as nov_exp, SUM(expected_expense_lists.expected_desc_expense) as des_exp, SUM(expected_expense_lists.expected_jan_expense) as jan_exp,
            SUM(expected_expense_lists.expected_feb_expense) as feb_exp, SUM(expected_expense_lists.expected_mar_expense) as mar_exp,
            SUM(expected_expense_lists.expected_apr_expense) as apr_exp, SUM(expected_expense_lists.expected_may_expense) as may_exp,
            SUM(expected_expense_lists.expected_june_expense) as jun_exp';

            $fields2 = 'SUM(expected_expense_lists.expected_july_expense) as jul_exp';

            $response = getExpectedExpenseData($current_year, $next_year, $fields1, $fields2);
            $format_expected_exp = [];
            $format_expected_exp['8'] = $response['expected_expense_data_1']->aug_exp;
            $format_expected_exp['9'] = $response['expected_expense_data_1']->sep_exp;
            $format_expected_exp['10'] = $response['expected_expense_data_1']->oct_exp;
            $format_expected_exp['11'] = $response['expected_expense_data_1']->nov_exp;
            $format_expected_exp['12'] = $response['expected_expense_data_1']->des_exp;
            $format_expected_exp['1'] = $response['expected_expense_data_1']->jan_exp;
            $format_expected_exp['2'] = $response['expected_expense_data_1']->feb_exp;
            $format_expected_exp['3'] = $response['expected_expense_data_1']->mar_exp;
            $format_expected_exp['4'] = $response['expected_expense_data_1']->apr_exp;
            $format_expected_exp['5'] = $response['expected_expense_data_1']->may_exp;
            $format_expected_exp['6'] = $response['expected_expense_data_1']->jun_exp;
            $format_expected_exp['7'] = $response['expected_expense_data_2']->jul_exp;
        } else if ($month_val == 9) {
            $fields1 = 'SUM(expected_expense_lists.expected_sept_expense) as sep_exp, 
            SUM(expected_expense_lists.expected_oct_expense) as oct_exp, SUM(expected_expense_lists.expected_nov_expense) as nov_exp, 
            SUM(expected_expense_lists.expected_desc_expense) as des_exp, SUM(expected_expense_lists.expected_jan_expense) as jan_exp,
            SUM(expected_expense_lists.expected_feb_expense) as feb_exp, SUM(expected_expense_lists.expected_mar_expense) as mar_exp,
            SUM(expected_expense_lists.expected_apr_expense) as apr_exp, SUM(expected_expense_lists.expected_may_expense) as may_exp,
            SUM(expected_expense_lists.expected_june_expense) as jun_exp';

            $fields2 = 'SUM(expected_expense_lists.expected_july_expense) as jul_exp, SUM(expected_expense_lists.expected_aug_expense) as aug_exp';

            $response = getExpectedExpenseData($current_year, $next_year, $fields1, $fields2);
            $format_expected_exp = [];
            $format_expected_exp['9'] = $response['expected_expense_data_1']->sep_exp;
            $format_expected_exp['10'] = $response['expected_expense_data_1']->oct_exp;
            $format_expected_exp['11'] = $response['expected_expense_data_1']->nov_exp;
            $format_expected_exp['12'] = $response['expected_expense_data_1']->des_exp;
            $format_expected_exp['1'] = $response['expected_expense_data_1']->jan_exp;
            $format_expected_exp['2'] = $response['expected_expense_data_1']->feb_exp;
            $format_expected_exp['3'] = $response['expected_expense_data_1']->mar_exp;
            $format_expected_exp['4'] = $response['expected_expense_data_1']->apr_exp;
            $format_expected_exp['5'] = $response['expected_expense_data_1']->may_exp;
            $format_expected_exp['6'] = $response['expected_expense_data_1']->jun_exp;
            $format_expected_exp['7'] = $response['expected_expense_data_2']->jul_exp;
            $format_expected_exp['8'] = $response['expected_expense_data_2']->aug_exp;
        } else if ($month_val == 10) {
            $fields1 = 'SUM(expected_expense_lists.expected_oct_expense) as oct_exp, SUM(expected_expense_lists.expected_nov_expense) as nov_exp, 
            SUM(expected_expense_lists.expected_desc_expense) as des_exp, SUM(expected_expense_lists.expected_jan_expense) as jan_exp,
            SUM(expected_expense_lists.expected_feb_expense) as feb_exp, SUM(expected_expense_lists.expected_mar_expense) as mar_exp,
            SUM(expected_expense_lists.expected_apr_expense) as apr_exp, SUM(expected_expense_lists.expected_may_expense) as may_exp,
            SUM(expected_expense_lists.expected_june_expense) as jun_exp';

            $fields2 = 'SUM(expected_expense_lists.expected_july_expense) as jul_exp, SUM(expected_expense_lists.expected_aug_expense) as aug_exp,
            SUM(expected_expense_lists.expected_sept_expense) as sep_exp';

            $response = getExpectedExpenseData($current_year, $next_year, $fields1, $fields2);
            $format_expected_exp = [];
            $format_expected_exp['10'] = $response['expected_expense_data_1']->oct_exp;
            $format_expected_exp['11'] = $response['expected_expense_data_1']->nov_exp;
            $format_expected_exp['12'] = $response['expected_expense_data_1']->des_exp;
            $format_expected_exp['1'] = $response['expected_expense_data_1']->jan_exp;
            $format_expected_exp['2'] = $response['expected_expense_data_1']->feb_exp;
            $format_expected_exp['3'] = $response['expected_expense_data_1']->mar_exp;
            $format_expected_exp['4'] = $response['expected_expense_data_1']->apr_exp;
            $format_expected_exp['5'] = $response['expected_expense_data_1']->may_exp;
            $format_expected_exp['6'] = $response['expected_expense_data_1']->jun_exp;
            $format_expected_exp['7'] = $response['expected_expense_data_2']->jul_exp;
            $format_expected_exp['8'] = $response['expected_expense_data_2']->aug_exp;
            $format_expected_exp['9'] = $response['expected_expense_data_2']->sep_exp;
        } else if ($month_val == 11) {
            $fields1 = 'SUM(expected_expense_lists.expected_nov_expense) as nov_exp, SUM(expected_expense_lists.expected_desc_expense) as des_exp, 
            SUM(expected_expense_lists.expected_jan_expense) as jan_exp, SUM(expected_expense_lists.expected_feb_expense) as feb_exp, 
            SUM(expected_expense_lists.expected_mar_expense) as mar_exp, SUM(expected_expense_lists.expected_apr_expense) as apr_exp, 
            SUM(expected_expense_lists.expected_may_expense) as may_exp,SUM(expected_expense_lists.expected_june_expense) as jun_exp';

            $fields2 = 'SUM(expected_expense_lists.expected_july_expense) as jul_exp, SUM(expected_expense_lists.expected_aug_expense) as aug_exp,
            SUM(expected_expense_lists.expected_sept_expense) as sep_exp, SUM(expected_expense_lists.expected_oct_expense) as oct_exp';

            $response = getExpectedExpenseData($current_year, $next_year, $fields1, $fields2);
            $format_expected_exp = [];
            $format_expected_exp['11'] = $response['expected_expense_data_1']->nov_exp;
            $format_expected_exp['12'] = $response['expected_expense_data_1']->des_exp;
            $format_expected_exp['1'] = $response['expected_expense_data_1']->jan_exp;
            $format_expected_exp['2'] = $response['expected_expense_data_1']->feb_exp;
            $format_expected_exp['3'] = $response['expected_expense_data_1']->mar_exp;
            $format_expected_exp['4'] = $response['expected_expense_data_1']->apr_exp;
            $format_expected_exp['5'] = $response['expected_expense_data_1']->may_exp;
            $format_expected_exp['6'] = $response['expected_expense_data_1']->jun_exp;
            $format_expected_exp['7'] = $response['expected_expense_data_2']->jul_exp;
            $format_expected_exp['8'] = $response['expected_expense_data_2']->aug_exp;
            $format_expected_exp['9'] = $response['expected_expense_data_2']->sep_exp;
            $format_expected_exp['10'] = $response['expected_expense_data_2']->oct_exp;
        } else if ($month_val == 12) {
            $fields1 = 'SUM(expected_expense_lists.expected_desc_expense) as des_exp, SUM(expected_expense_lists.expected_jan_expense) as jan_exp, 
            SUM(expected_expense_lists.expected_feb_expense) as feb_exp, SUM(expected_expense_lists.expected_mar_expense) as mar_exp, 
            SUM(expected_expense_lists.expected_apr_expense) as apr_exp, SUM(expected_expense_lists.expected_may_expense) as may_exp,
            SUM(expected_expense_lists.expected_june_expense) as jun_exp';

            $fields2 = 'SUM(expected_expense_lists.expected_july_expense) as jul_exp, SUM(expected_expense_lists.expected_aug_expense) as aug_exp,
            SUM(expected_expense_lists.expected_sept_expense) as sep_exp, SUM(expected_expense_lists.expected_oct_expense) as oct_exp,
            SUM(expected_expense_lists.expected_nov_expense) as nov_exp';

            $response = getExpectedExpenseData($current_year, $next_year, $fields1, $fields2);
            $format_expected_exp = [];
            $format_expected_exp['12'] = $response['expected_expense_data_1']->des_exp;
            $format_expected_exp['1'] = $response['expected_expense_data_1']->jan_exp;
            $format_expected_exp['2'] = $response['expected_expense_data_1']->feb_exp;
            $format_expected_exp['3'] = $response['expected_expense_data_1']->mar_exp;
            $format_expected_exp['4'] = $response['expected_expense_data_1']->apr_exp;
            $format_expected_exp['5'] = $response['expected_expense_data_1']->may_exp;
            $format_expected_exp['6'] = $response['expected_expense_data_1']->jun_exp;
            $format_expected_exp['7'] = $response['expected_expense_data_2']->jul_exp;
            $format_expected_exp['8'] = $response['expected_expense_data_2']->aug_exp;
            $format_expected_exp['9'] = $response['expected_expense_data_2']->sep_exp;
            $format_expected_exp['10'] = $response['expected_expense_data_2']->oct_exp;
            $format_expected_exp['11'] = $response['expected_expense_data_2']->nov_exp;
        } else if ($month_val == 1) {
            $fields1 = 'SUM(expected_expense_lists.expected_jan_expense) as jan_exp, 
            SUM(expected_expense_lists.expected_feb_expense) as feb_exp, SUM(expected_expense_lists.expected_mar_expense) as mar_exp, 
            SUM(expected_expense_lists.expected_apr_expense) as apr_exp, SUM(expected_expense_lists.expected_may_expense) as may_exp,
            SUM(expected_expense_lists.expected_june_expense) as jun_exp';

            $fields2 = 'SUM(expected_expense_lists.expected_july_expense) as jul_exp, SUM(expected_expense_lists.expected_aug_expense) as aug_exp,
            SUM(expected_expense_lists.expected_sept_expense) as sep_exp, SUM(expected_expense_lists.expected_oct_expense) as oct_exp,
            SUM(expected_expense_lists.expected_nov_expense) as nov_exp, SUM(expected_expense_lists.expected_desc_expense) as des_exp';

            $response = getExpectedExpenseData($current_year, $next_year, $fields1, $fields2);
            $format_expected_exp = [];
            $format_expected_exp['1'] = $response['expected_expense_data_1']->jan_exp;
            $format_expected_exp['2'] = $response['expected_expense_data_1']->feb_exp;
            $format_expected_exp['3'] = $response['expected_expense_data_1']->mar_exp;
            $format_expected_exp['4'] = $response['expected_expense_data_1']->apr_exp;
            $format_expected_exp['5'] = $response['expected_expense_data_1']->may_exp;
            $format_expected_exp['6'] = $response['expected_expense_data_1']->jun_exp;
            $format_expected_exp['7'] = $response['expected_expense_data_2']->jul_exp;
            $format_expected_exp['8'] = $response['expected_expense_data_2']->aug_exp;
            $format_expected_exp['9'] = $response['expected_expense_data_2']->sep_exp;
            $format_expected_exp['10'] = $response['expected_expense_data_2']->oct_exp;
            $format_expected_exp['11'] = $response['expected_expense_data_2']->nov_exp;
            $format_expected_exp['12'] = $response['expected_expense_data_2']->des_exp;
        } else if ($month_val == 2) {
            $fields1 = 'SUM(expected_expense_lists.expected_feb_expense) as feb_exp, SUM(expected_expense_lists.expected_mar_expense) as mar_exp, 
            SUM(expected_expense_lists.expected_apr_expense) as apr_exp, SUM(expected_expense_lists.expected_may_expense) as may_exp,
            SUM(expected_expense_lists.expected_june_expense) as jun_exp';

            $fields2 = 'SUM(expected_expense_lists.expected_july_expense) as jul_exp, SUM(expected_expense_lists.expected_aug_expense) as aug_exp,
            SUM(expected_expense_lists.expected_sept_expense) as sep_exp, SUM(expected_expense_lists.expected_oct_expense) as oct_exp,
            SUM(expected_expense_lists.expected_nov_expense) as nov_exp, SUM(expected_expense_lists.expected_desc_expense) as des_exp,
            SUM(expected_expense_lists.expected_jan_expense) as jan_exp';

            $response = getExpectedExpenseData($current_year, $next_year, $fields1, $fields2);
            $format_expected_exp = [];
            $format_expected_exp['2'] = $response['expected_expense_data_1']->feb_exp;
            $format_expected_exp['3'] = $response['expected_expense_data_1']->mar_exp;
            $format_expected_exp['4'] = $response['expected_expense_data_1']->apr_exp;
            $format_expected_exp['5'] = $response['expected_expense_data_1']->may_exp;
            $format_expected_exp['6'] = $response['expected_expense_data_1']->jun_exp;
            $format_expected_exp['7'] = $response['expected_expense_data_2']->jul_exp;
            $format_expected_exp['8'] = $response['expected_expense_data_2']->aug_exp;
            $format_expected_exp['9'] = $response['expected_expense_data_2']->sep_exp;
            $format_expected_exp['10'] = $response['expected_expense_data_2']->oct_exp;
            $format_expected_exp['11'] = $response['expected_expense_data_2']->nov_exp;
            $format_expected_exp['12'] = $response['expected_expense_data_2']->des_exp;
            $format_expected_exp['1'] = $response['expected_expense_data_2']->jan_exp;
        } else if ($month_val == 3) {
            $fields1 = 'SUM(expected_expense_lists.expected_mar_expense) as mar_exp, 
            SUM(expected_expense_lists.expected_apr_expense) as apr_exp, SUM(expected_expense_lists.expected_may_expense) as may_exp,
            SUM(expected_expense_lists.expected_june_expense) as jun_exp';

            $fields2 = 'SUM(expected_expense_lists.expected_july_expense) as jul_exp, SUM(expected_expense_lists.expected_aug_expense) as aug_exp,
            SUM(expected_expense_lists.expected_sept_expense) as sep_exp, SUM(expected_expense_lists.expected_oct_expense) as oct_exp,
            SUM(expected_expense_lists.expected_nov_expense) as nov_exp, SUM(expected_expense_lists.expected_desc_expense) as des_exp,
            SUM(expected_expense_lists.expected_jan_expense) as jan_exp, SUM(expected_expense_lists.expected_feb_expense) as feb_exp';

            $response = getExpectedExpenseData($current_year, $next_year, $fields1, $fields2);
            $format_expected_exp = [];
            $format_expected_exp['3'] = $response['expected_expense_data_1']->mar_exp;
            $format_expected_exp['4'] = $response['expected_expense_data_1']->apr_exp;
            $format_expected_exp['5'] = $response['expected_expense_data_1']->may_exp;
            $format_expected_exp['6'] = $response['expected_expense_data_1']->jun_exp;
            $format_expected_exp['7'] = $response['expected_expense_data_2']->jul_exp;
            $format_expected_exp['8'] = $response['expected_expense_data_2']->aug_exp;
            $format_expected_exp['9'] = $response['expected_expense_data_2']->sep_exp;
            $format_expected_exp['10'] = $response['expected_expense_data_2']->oct_exp;
            $format_expected_exp['11'] = $response['expected_expense_data_2']->nov_exp;
            $format_expected_exp['12'] = $response['expected_expense_data_2']->des_exp;
            $format_expected_exp['1'] = $response['expected_expense_data_2']->jan_exp;
            $format_expected_exp['2'] = $response['expected_expense_data_2']->feb_exp;
        } else if ($month_val == 4) {
            $fields1 = 'SUM(expected_expense_lists.expected_apr_expense) as apr_exp, SUM(expected_expense_lists.expected_may_expense) as may_exp,
            SUM(expected_expense_lists.expected_june_expense) as jun_exp';

            $fields2 = 'SUM(expected_expense_lists.expected_july_expense) as jul_exp, SUM(expected_expense_lists.expected_aug_expense) as aug_exp,
            SUM(expected_expense_lists.expected_sept_expense) as sep_exp, SUM(expected_expense_lists.expected_oct_expense) as oct_exp,
            SUM(expected_expense_lists.expected_nov_expense) as nov_exp, SUM(expected_expense_lists.expected_desc_expense) as des_exp,
            SUM(expected_expense_lists.expected_jan_expense) as jan_exp, SUM(expected_expense_lists.expected_feb_expense) as feb_exp, 
            SUM(expected_expense_lists.expected_mar_expense) as mar_exp';

            $response = getExpectedExpenseData($current_year, $next_year, $fields1, $fields2);
            $format_expected_exp = [];
            $format_expected_exp['4'] = $response['expected_expense_data_1']->apr_exp;
            $format_expected_exp['5'] = $response['expected_expense_data_1']->may_exp;
            $format_expected_exp['6'] = $response['expected_expense_data_1']->jun_exp;
            $format_expected_exp['7'] = $response['expected_expense_data_2']->jul_exp;
            $format_expected_exp['8'] = $response['expected_expense_data_2']->aug_exp;
            $format_expected_exp['9'] = $response['expected_expense_data_2']->sep_exp;
            $format_expected_exp['10'] = $response['expected_expense_data_2']->oct_exp;
            $format_expected_exp['11'] = $response['expected_expense_data_2']->nov_exp;
            $format_expected_exp['12'] = $response['expected_expense_data_2']->des_exp;
            $format_expected_exp['1'] = $response['expected_expense_data_2']->jan_exp;
            $format_expected_exp['2'] = $response['expected_expense_data_2']->feb_exp;
            $format_expected_exp['3'] = $response['expected_expense_data_2']->mar_exp;
        } else if ($month_val == 5) {
            $fields1 = 'SUM(expected_expense_lists.expected_may_expense) as may_exp, SUM(expected_expense_lists.expected_june_expense) as jun_exp';

            $fields2 = 'SUM(expected_expense_lists.expected_july_expense) as jul_exp, SUM(expected_expense_lists.expected_aug_expense) as aug_exp,
            SUM(expected_expense_lists.expected_sept_expense) as sep_exp, SUM(expected_expense_lists.expected_oct_expense) as oct_exp,
            SUM(expected_expense_lists.expected_nov_expense) as nov_exp, SUM(expected_expense_lists.expected_desc_expense) as des_exp,
            SUM(expected_expense_lists.expected_jan_expense) as jan_exp, SUM(expected_expense_lists.expected_feb_expense) as feb_exp, 
            SUM(expected_expense_lists.expected_mar_expense) as mar_exp, SUM(expected_expense_lists.expected_apr_expense) as apr_exp';

            $response = getExpectedExpenseData($current_year, $next_year, $fields1, $fields2);
            $format_expected_exp = [];
            $format_expected_exp['5'] = $response['expected_expense_data_1']->may_exp;
            $format_expected_exp['6'] = $response['expected_expense_data_1']->jun_exp;
            $format_expected_exp['7'] = $response['expected_expense_data_2']->jul_exp;
            $format_expected_exp['8'] = $response['expected_expense_data_2']->aug_exp;
            $format_expected_exp['9'] = $response['expected_expense_data_2']->sep_exp;
            $format_expected_exp['10'] = $response['expected_expense_data_2']->oct_exp;
            $format_expected_exp['11'] = $response['expected_expense_data_2']->nov_exp;
            $format_expected_exp['12'] = $response['expected_expense_data_2']->des_exp;
            $format_expected_exp['1'] = $response['expected_expense_data_2']->jan_exp;
            $format_expected_exp['2'] = $response['expected_expense_data_2']->feb_exp;
            $format_expected_exp['3'] = $response['expected_expense_data_2']->mar_exp;
            $format_expected_exp['4'] = $response['expected_expense_data_2']->apr_exp;
        } else if ($month_val == 6) {
            $fields1 = 'SUM(expected_expense_lists.expected_june_expense) as jun_exp';

            $fields2 = 'SUM(expected_expense_lists.expected_july_expense) as jul_exp, SUM(expected_expense_lists.expected_aug_expense) as aug_exp,
            SUM(expected_expense_lists.expected_sept_expense) as sep_exp, SUM(expected_expense_lists.expected_oct_expense) as oct_exp,
            SUM(expected_expense_lists.expected_nov_expense) as nov_exp, SUM(expected_expense_lists.expected_desc_expense) as des_exp,
            SUM(expected_expense_lists.expected_jan_expense) as jan_exp, SUM(expected_expense_lists.expected_feb_expense) as feb_exp, 
            SUM(expected_expense_lists.expected_mar_expense) as mar_exp, SUM(expected_expense_lists.expected_apr_expense) as apr_exp,
            SUM(expected_expense_lists.expected_may_expense) as may_exp';

            $response = getExpectedExpenseData($current_year, $next_year, $fields1, $fields2);
            $format_expected_exp = [];
            $format_expected_exp['6'] = $response['expected_expense_data_1']->jun_exp;
            $format_expected_exp['7'] = $response['expected_expense_data_2']->jul_exp;
            $format_expected_exp['8'] = $response['expected_expense_data_2']->aug_exp;
            $format_expected_exp['9'] = $response['expected_expense_data_2']->sep_exp;
            $format_expected_exp['10'] = $response['expected_expense_data_2']->oct_exp;
            $format_expected_exp['11'] = $response['expected_expense_data_2']->nov_exp;
            $format_expected_exp['12'] = $response['expected_expense_data_2']->des_exp;
            $format_expected_exp['1'] = $response['expected_expense_data_2']->jan_exp;
            $format_expected_exp['2'] = $response['expected_expense_data_2']->feb_exp;
            $format_expected_exp['3'] = $response['expected_expense_data_2']->mar_exp;
            $format_expected_exp['4'] = $response['expected_expense_data_2']->apr_exp;
            $format_expected_exp['5'] = $response['expected_expense_data_2']->may_exp;
        }

        $expected_inv_sub_arr = [];
        foreach ($formated_expected_inv_arr as $key => $val) {
            $expected_inv_sub_arr[$val['y']] = [];
            $expected_inv_sub_arr[$val['y']]['y'] = $val['y'];
            if ($val['y'] == $formated_expected_sub_arr[$key]['y']) {
                $addition = $val['a'] + $formated_expected_sub_arr[$key]['a'];
                $expected_inv_sub_arr[$val['y']]['a'] = round($addition, 2);
                $expected_inv_sub_arr[$val['y']]['b'] = $format_expected_exp[$key] ? round($format_expected_exp[$key], 2) : 0;
            }
        }

        $final_expected_inc_exp_arr = array_values($expected_inv_sub_arr);
        $expected_inc_exp_diff = 0;
        foreach ($final_expected_inc_exp_arr as $key => $val) {
            if (date('m') <= 12) {
                if ($val['y'] >= date('m') && $val['y'] <= 12) {
                    $month_year = date('Y');
                } else {
                    $month_year = date('Y') + 1;
                }
            } else {
                if ($val['y'] <= date('m') && $val['y'] <= 6) {
                    $month_year = date('Y') + 1;
                } else {
                    $month_year = date('Y');
                }
            }
            $month_year = substr($month_year, 2);
            $vall = $val['a'] - $val['b'];
            $final_expected_inc_exp_arr[$key]['y'] = $month_name_cur_next_arr[$key] . " - " . $month_year . " \n $vall";
            $expected_inc_exp_diff += $val['a'] - $val['b'];
        }
        $expected_inc_exp_arr = json_encode($final_expected_inc_exp_arr);
        //Get data for expected income-expense chart ends

        // Unpaid invoice total starts
        $unpaid_invoice_total =  Invoice::with(['payment_status'])->whereHas('payment_status', function ($query) {
            $query->where('name', '=', 'Unpaid');
        })->select(\DB::raw('SUM(invoice_grand_total) as inc_total'))->get();
        // Unpaid invoice total ends

        //Recurring invoice total starts
        $next_subscriptions = Subscription::whereSubscriptionCycle('yearly')->get();
        $next_subscription_total = 0;
        foreach ($next_subscriptions as $key => $val) {
            $next_subscription_total += getNextAmountForSubscription($val->id);
        }
        $recurring_invoice_total = $next_subscription_total;
        //Recurring invoice total ends


        //Total expense based on category starts
        $payroll_cat_id = getExpenseCategoryId('Payroll');
        $payroll_cat_exp = getCategoryExpense($payroll_cat_id);
        $contractor_payment_id = getExpenseCategoryId('Contractor Payment');
        $contractor_payment_cat_exp = getCategoryExpense($contractor_payment_id);
        $cogs_cat_id = getExpenseCategoryId('COGS');
        $cogs_cat_exp = getCategoryExpense($cogs_cat_id);
        $government_cat_id = getExpenseCategoryId('Government');
        $government_cat_exp = getCategoryExpense($government_cat_id);
        $subscription_cat_id = getExpenseCategoryId('Subscription');
        $subscription_cat_exp = getCategoryExpense($subscription_cat_id);
        $software_dev_cat_id = getExpenseCategoryId('Software Development');
        $software_dev_cat_exp = getCategoryExpense($software_dev_cat_id);
        $domestic_cat_id = getExpenseCategoryId('Domestic (Air Ticket)');
        $domestic_cat_exp = getCategoryExpense($domestic_cat_id);
        $food_fuel_cat_id = getExpenseCategoryId('Food');
        $food_fuel_cat_exp = getCategoryExpense($food_fuel_cat_id);
        //Total expense based on category ends

        // Latest list of quotes starts
        $quotes = Quote::with(['client', 'payment_status'])->whereQuotePaymentStatus('Open')->orderBy('id', 'desc')->get();
        // Latest list of quotes ends

        // Total amount of unapid invoices and open quotes starts
        $open_quotes_amount = Quote::where('quote_payment_status', '=', 'Open')->select(\DB::raw('SUM(quote_grand_total) as quote_total'))->get();
        // Total amount of unapid invoices and open quotes ends

        //CODE FOR YEARLY EXPENSE AND INVOICE CHART
        $yearly_expenses = DB::table('expenses')
            ->select(
                // DB::raw('YEAR(expense_date) as year'),
                DB::raw('SUM(CASE 
                    WHEN expense_date BETWEEN "2022-07-01" AND "2023-06-30" THEN expense_amount 
                    ELSE 0 
                 END) as expense_of_year1'),
                DB::raw('SUM(CASE 
                    WHEN expense_date BETWEEN "2023-07-01" AND "2024-06-30" THEN expense_amount 
                    ELSE 0 
                 END) as expense_of_year2'),
                DB::raw('SUM(CASE 
                    WHEN expense_date BETWEEN "2024-07-01" AND "2025-06-30" THEN expense_amount 
                    ELSE 0 
                 END) as expense_of_year3'),
                DB::raw('SUM(CASE 
                    WHEN expense_date BETWEEN "2025-07-01" AND "2026-06-30" THEN expense_amount 
                    ELSE 0 
                 END) as expense_of_year4'),
                DB::raw('SUM(CASE 
                    WHEN expense_date BETWEEN "2026-07-01" AND "2027-06-30" THEN expense_amount 
                    ELSE 0 
                 END) as expense_of_year5'),
                DB::raw('SUM(CASE 
                     WHEN expense_date BETWEEN "2027-07-01" AND "2028-06-30" THEN expense_amount 
                     ELSE 0 
                 END) as expense_of_year6'),
                DB::raw('SUM(CASE 
                     WHEN expense_date BETWEEN "2028-07-01" AND "2029-06-30" THEN expense_amount 
                     ELSE 0 
                 END) as expense_of_year7'),
                DB::raw('SUM(CASE 
                     WHEN expense_date BETWEEN "2029-07-01" AND "2030-06-30" THEN expense_amount 
                     ELSE 0 
                 END) as expense_of_year8'),
                DB::raw('SUM(CASE 
                     WHEN expense_date BETWEEN "2030-07-01" AND "2031-06-30" THEN expense_amount 
                     ELSE 0 
                END) as expense_of_year9'),
                DB::raw('SUM(CASE 
                     WHEN expense_date BETWEEN "2031-07-01" AND "2032-06-30" THEN expense_amount 
                     ELSE 0 
                END) as expense_of_year10')
            )
            ->where('expenses.deleted_at', '=', null)
            ->get();
        //  echo $yearly_expenses."\n"; 
        $yearly_income = DB::table('invoices')
            ->select(
                //\DB::raw('SUM(invoice_grand_total) as a')
                DB::raw('SUM(CASE 
                    WHEN invoice_payment_date BETWEEN "2022-07-01" AND "2023-06-30" THEN invoice_grand_total 
                    ELSE 0 
                 END) as invoice_of_year1'),
                DB::raw('SUM(CASE 
                    WHEN invoice_payment_date BETWEEN "2023-07-01" AND "2024-06-30" THEN invoice_grand_total 
                    ELSE 0 
                 END) as invoice_of_year2'),
                DB::raw('SUM(CASE 
                     WHEN invoice_payment_date BETWEEN "2024-07-01" AND "2025-06-30" THEN invoice_grand_total 
                     ELSE 0 
                 END) as invoice_of_year3'),
                DB::raw('SUM(CASE 
                     WHEN invoice_payment_date BETWEEN "2025-07-01" AND "2026-06-30" THEN invoice_grand_total 
                     ELSE 0 
                 END) as invoice_of_year4'),
                DB::raw('SUM(CASE 
                     WHEN invoice_payment_date BETWEEN "2026-07-01" AND "2027-06-30" THEN invoice_grand_total 
                     ELSE 0 
                 END) as invoice_of_year5'),
                DB::raw('SUM(CASE 
                     WHEN invoice_payment_date BETWEEN "2027-07-01" AND "2028-06-30" THEN invoice_grand_total 
                     ELSE 0 
                 END) as invoice_of_year6'),
                DB::raw('SUM(CASE 
                     WHEN invoice_payment_date BETWEEN "2028-07-01" AND "2029-06-30" THEN invoice_grand_total 
                     ELSE 0 
                 END) as invoice_of_year7'),
                DB::raw('SUM(CASE 
                     WHEN invoice_payment_date BETWEEN "2029-07-01" AND "2030-06-30" THEN invoice_grand_total 
                     ELSE 0 
                 END) as invoice_of_year8'),
                DB::raw('SUM(CASE 
                     WHEN invoice_payment_date BETWEEN "2030-07-01" AND "2031-06-30" THEN invoice_grand_total 
                     ELSE 0 
                 END) as invoice_of_year9'),
                DB::raw('SUM(CASE 
                     WHEN invoice_payment_date BETWEEN "2031-07-01" AND "2032-06-30" THEN invoice_grand_total 
                     ELSE 0 
                 END) as invoice_of_year10')
            )
            ->where('invoices.deleted_at', '=', null)
            ->get();
        $month_arr = ['2022-2023', '2023-2024', '2024-2025', '2025-2026', '2026-2027', '2027-2028', '2028-2029', '2029-2030', '2030-2031', '2031-2032'];
        $yearly_expense_and_income = [];
        foreach ($month_arr as $key => $month) {
            // $expense = $yearly_expenses[0]['expense_of_year' . ($key + 1)] ?? 0;
            // $invoice = $yearly_income[0]['invoice_of_year' . ($key + 1)] ?? 0;

            $expenseProperty = 'expense_of_year' . ($key + 1);
            $invoiceProperty = 'invoice_of_year' . ($key + 1);

            $expense = $yearly_expenses[0]->$expenseProperty ?? 0;
            $invoice = $yearly_income[0]->$invoiceProperty ?? 0;
            $yearly_data[] = ['y' => $month, 'a' => round($invoice, 2), 'b' => round($expense, 2)];
        }
        $yearly_expense_and_income = json_encode($yearly_data);
        //END CODE HERE
        //CODE START FOR  PROJECT REPORT
        $projects_array =  DB::table('projects')
            ->where('projects.deleted_at', '=', null)
            ->select(
                \DB::raw("name as project_name")
            )
            ->orderBy('project_name', 'asc')
            ->get();

        $expense_of_project = DB::table('expenses')
            ->Join('projects', 'expenses.project_id', '=', 'projects.id')
            ->where('expenses.deleted_at', '=', null)
            ->select(
                \DB::raw('SUM(expense_amount) as exp_total'),
                \DB::raw("projects.name as project_name")
            )
            ->groupBy('project_name')
            ->orderBy('project_name', 'asc')
            ->get();

        $invoice_of_project = DB::table('invoices')
            ->Join('projects', 'invoices.project_id', '=', 'projects.id')
            ->where('invoices.deleted_at', '=', null)
            ->select(
                \DB::raw('SUM(invoice_grand_total) as inc_total'),
                \DB::raw("projects.name as project_name")
            )
            ->groupBy('project_name')
            ->orderBy('project_name', 'asc')
            ->get();

        $projects_data = [];
        foreach ($projects_array as $project_name) {
            $expense = 0;
            $invoice = 0;
            foreach ($expense_of_project as $expense_item) {
                if ($expense_item->project_name === $project_name->project_name) {
                    $expense = $expense_item->exp_total;
                    break;
                }
            }
            foreach ($invoice_of_project as $invoice_item) {
                if ($invoice_item->project_name === $project_name->project_name) {
                    $invoice = $invoice_item->inc_total;
                    break;
                }
            }
            $projects_data[] = [
                'y' => $project_name->project_name,
                'a' => round($invoice, 2),
                'b' => round($expense, 2),
            ];
        }
        $chunked_array = array_chunk($projects_data, 12);
        $project_expense_and_invoice = json_encode($chunked_array);

        //END CODE HERE
        $data['current_month_sales'] = $invoices[0]->invoice_grand_total;
        $data['current_month_expense'] = $expenses[0]->expense_total;
        $data['q1_gst_paid'] = $q1_gst_paid;
        $data['q1_gst_collected'] = $q1_gst_collected;
        $data['q2_gst_paid'] = $q2_gst_paid;
        $data['q2_gst_collected'] = $q2_gst_collected;
        $data['q3_gst_paid'] = $q3_gst_paid;
        $data['q3_gst_collected'] = $q3_gst_collected;
        $data['q4_gst_paid'] = $q4_gst_paid;
        $data['q4_gst_collected'] = $q4_gst_collected;
        $data['unpaind_invoices'] = $unpaind_invoices;
        $data['q1_income'] = $q1_income;
        $data['q1_expense'] = $q1_expense;
        $data['q1_profit'] = $q1_profit;
        $data['q1_profit_per'] = $q1_profit_per;
        $data['q2_income'] = $q2_income;
        $data['q2_expense'] = $q2_expense;
        $data['q2_profit'] = $q2_profit;
        $data['q2_profit_per'] = $q2_profit_per;
        $data['q3_income'] = $q3_income;
        $data['q3_expense'] = $q3_expense;
        $data['q3_profit'] = $q3_profit;
        $data['q3_profit_per'] = $q3_profit_per;
        $data['q4_income'] = $q4_income;
        $data['q4_expense'] = $q4_expense;
        $data['q4_profit'] = $q4_profit;
        $data['q4_profit_per'] = $q4_profit_per;
        $data['subscriptions'] = $subscriptions;
        $data['payroll_cat_exp'] = $payroll_cat_exp;
        $data['contractor_payment_cat_exp'] = $contractor_payment_cat_exp;
        $data['cogs_cat_exp'] = $cogs_cat_exp;
        $data['government_cat_exp'] = $government_cat_exp;
        $data['subscription_cat_exp'] = $subscription_cat_exp;
        $data['software_dev_cat_exp'] = $software_dev_cat_exp;
        $data['domestic_cat_exp'] = $domestic_cat_exp;
        $data['food_fuel_cat_exp'] = $food_fuel_cat_exp;
        $data['quotes'] = $quotes;
        $data['recurring_invoice_total'] = $recurring_invoice_total;
        $data['unpaid_invoice_total'] = $unpaid_invoice_total[0]->inc_total;
        $data['open_quotes_amount'] = $open_quotes_amount[0]->quote_total;
        $data['expected_fin_year'] = $expected_fin_year;
        $data['expected_inc_exp_diff'] = $expected_inc_exp_diff;
        $data['yearly_expense_and_income'] = $yearly_expense_and_income;
        $data['project_expense_and_invoice'] = $project_expense_and_invoice;

        // Quarterly Comparison for current year vs previous 2 years
        $current_month_num = Carbon::now()->month;
        if ($current_month_num >= 7) {
            $current_fy_start = Carbon::now()->year;
            $current_fy_end = Carbon::now()->year + 1;
        } else {
            $current_fy_start = Carbon::now()->year - 1;
            $current_fy_end = Carbon::now()->year;
        }

        $prev_fy1_start = $current_fy_start - 1;
        $prev_fy1_end = $current_fy_end - 1;
        $prev_fy2_start = $current_fy_start - 2;
        $prev_fy2_end = $current_fy_end - 2;

        $quarterly_comparison = [];
        $quarters = [
            ['name' => 'Q1', 'start_month' => 7, 'end_month' => 9],
            ['name' => 'Q2', 'start_month' => 10, 'end_month' => 12],
            ['name' => 'Q3', 'start_month' => 1, 'end_month' => 3],
            ['name' => 'Q4', 'start_month' => 4, 'end_month' => 6]
        ];

        // Replace the current quarterly comparison section with this:
        foreach ($quarters as $quarter) {
            $q_name = $quarter['name'];
            $start_month = $quarter['start_month'];
            $end_month = $quarter['end_month'];

            $year_offset = (in_array($q_name, ['Q1', 'Q2'])) ? 0 : 1;

            $current_year = $current_fy_start + $year_offset;
            $prev1_year = $prev_fy1_start + $year_offset;
            $prev2_year = $prev_fy2_start + $year_offset;

            $current_start = $current_year . '-' . str_pad($start_month, 2, '0', STR_PAD_LEFT) . '-01';
            $current_end = $current_year . '-' . str_pad($end_month, 2, '0', STR_PAD_LEFT) . '-' . Carbon::create($current_year, $end_month, 1)->endOfMonth()->day;

            $prev1_start = $prev1_year . '-' . str_pad($start_month, 2, '0', STR_PAD_LEFT) . '-01';
            $prev1_end = $prev1_year . '-' . str_pad($end_month, 2, '0', STR_PAD_LEFT) . '-' . Carbon::create($prev1_year, $end_month, 1)->endOfMonth()->day;

            $prev2_start = $prev2_year . '-' . str_pad($start_month, 2, '0', STR_PAD_LEFT) . '-01';
            $prev2_end = $prev2_year . '-' . str_pad($end_month, 2, '0', STR_PAD_LEFT) . '-' . Carbon::create($prev2_year, $end_month, 1)->endOfMonth()->day;

            // Income
            $current_income = DB::table('invoices')->whereBetween('invoice_payment_date', [$current_start, $current_end])->where('invoices.deleted_at', null)->where('payment_status_id', 2)->sum('invoice_grand_total');
            $prev1_income   = DB::table('invoices')->whereBetween('invoice_payment_date', [$prev1_start, $prev1_end])->where('invoices.deleted_at', null)->where('payment_status_id', 2)->sum('invoice_grand_total');
            $prev2_income   = DB::table('invoices')->whereBetween('invoice_payment_date', [$prev2_start, $prev2_end])->where('invoices.deleted_at', null)->where('payment_status_id', 2)->sum('invoice_grand_total');

            // Expense
            $current_expense = DB::table('expenses')->whereBetween('expense_date', [$current_start, $current_end])->where('expenses.deleted_at', null)->sum('expense_amount');
            $prev1_expense   = DB::table('expenses')->whereBetween('expense_date', [$prev1_start, $prev1_end])->where('expenses.deleted_at', null)->sum('expense_amount');
            $prev2_expense   = DB::table('expenses')->whereBetween('expense_date', [$prev2_start, $prev2_end])->where('expenses.deleted_at', null)->sum('expense_amount');

            // Profit = Income - Expense
            $quarterly_comparison[] = [
                'quarter'    => $q_name,
                'current'    => round($current_income - $current_expense, 2),
                'previous_1' => round($prev1_income - $prev1_expense, 2),
                'previous_2' => round($prev2_income - $prev2_expense, 2),
            ];
        }

        $fy_label = ($current_fy_start) . '-' . ($current_fy_end);
        $fy_label_1 = ($prev_fy1_start) . '-' . ($prev_fy1_end);
        $fy_label_2 = ($prev_fy2_start) . '-' . ($prev_fy2_end);

        $quarterly_comparison_json = json_encode($quarterly_comparison);

        // Client Revenue Data for Dashboard
        $currentYear = date('Y');
        $previousYear = $currentYear - 1;

        // Get all clients by revenue for current FY and use top 10 in chart only
        $allClientsByRevenue = DB::table('invoices')
            ->join('clients', 'invoices.client_id', '=', 'clients.id')
            ->whereNull('invoices.deleted_at')
            ->where('invoices.payment_status_id', '=', '2')
            ->whereNull('clients.deleted_at')
            ->whereBetween('invoices.invoice_payment_date', [($currentYear - 1) . '-07-01', $currentYear . '-06-30'])
            ->selectRaw("
                clients.id,
                clients.client_business_name,
                clients.client_number,
                SUM(invoices.invoice_grand_total) as current_year_revenue
            ")
            ->groupBy('clients.id', 'clients.client_business_name', 'clients.client_number')
            ->orderByDesc('current_year_revenue')
            ->get();

        $topClients = $allClientsByRevenue->take(10);

        // Calculate previous year revenue for comparison
        $clientRevenueData = [];
        $totalCurrentRevenue = 0;
        $totalPreviousRevenue = 0;

        foreach ($topClients as $client) {
            $previousRevenue = $this->getClientRevenueByYear($client->id, $previousYear);
            $difference = $client->current_year_revenue - $previousRevenue;
            $percentageChange = $previousRevenue > 0 ? (($difference / $previousRevenue) * 100) : 0;

            $clientRevenueData[] = [
                'id' => $client->id,
                'client_number' => $client->client_number,
                'client_name' => $client->client_business_name,
                'current_revenue' => round($client->current_year_revenue, 2),
                'previous_revenue' => round($previousRevenue, 2),
                'difference' => round($difference, 2),
                'percentage_change' => round($percentageChange, 2)
            ];

            $totalCurrentRevenue += $client->current_year_revenue;
            $totalPreviousRevenue += $previousRevenue;
        }

        // Prepare chart data for client revenue
        $clientRevenueChartData = [
            'labels' => array_column($clientRevenueData, 'client_name'),
            'datasets' => [
                [
                    'label' => 'FY ' . $previousYear . '-' . $currentYear,
                    'data' => array_column($clientRevenueData, 'previous_revenue'),
                    'backgroundColor' => 'rgba(75, 192, 75, 0.7)',
                    'borderColor' => 'rgba(75, 192, 75, 1)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'FY ' . $currentYear . '-' . ($currentYear + 1),
                    'data' => array_column($clientRevenueData, 'current_revenue'),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.7)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2,
                ]
            ]
        ];

        $data['client_revenue_data'] = $clientRevenueData;
        $data['total_current_revenue'] = round($totalCurrentRevenue, 2);
        $data['total_previous_revenue'] = round($totalPreviousRevenue, 2);
        $data['total_revenue_difference'] = round($totalCurrentRevenue - $totalPreviousRevenue, 2);
        $data['current_year'] = $currentYear;
        $data['previous_year'] = $previousYear;

        // Sales vs Expenses Trend Analysis - Last 12 months
        $salesVsExpensesTrend = [];
        $currentDate = Carbon::now();

        for ($i = 11; $i >= 0; $i--) {
            $date = $currentDate->copy()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            // Get sales for the month
            $monthlySales = DB::table('invoices')
                ->whereBetween('invoice_payment_date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
                ->where('invoices.deleted_at', '=', null)
                ->sum('invoice_grand_total');

            // Get expenses for the month
            $monthlyExpenses = DB::table('expenses')
                ->whereBetween('expense_date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
                ->where('expenses.deleted_at', '=', null)
                ->sum('expense_amount');

            // Calculate profit
            $profit = $monthlySales - $monthlyExpenses;

            $salesVsExpensesTrend[] = [
                'month' => $date->format('M Y'),
                'sales' => round($monthlySales ?? 0, 2),
                'expenses' => round($monthlyExpenses ?? 0, 2),
                'profit' => round($profit, 2)
            ];
        }

        $sales_vs_expenses_trend = json_encode($salesVsExpensesTrend);

        return view('dashboard', compact('data', 'income_expense_arr', 'expected_inc_exp_arr', 'yearly_expense_and_income', 'project_expense_and_invoice', 'quarterly_comparison_json', 'fy_label', 'fy_label_1', 'fy_label_2', 'clientRevenueChartData', 'sales_vs_expenses_trend'));
    }

    /**
     * Export client revenue report to Excel/PDF
     */
    public function exportClientRevenueReport(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $compareYear = $request->get('compare_year');
        $format = $request->get('format', 'xlsx');

        $export = new ClientRevenueReportExport($year, $compareYear, $format);
        return $export->download('client-revenue-report-' . $year . '.' . $format);
    }

    /**
     * Get client revenue for a specific financial year (July to June)
     */
    private function getClientRevenueByYear($clientId, $year)
    {
        $startDate = ($year - 1) . '-07-01';
        $endDate = $year . '-06-30';

        $revenue = Invoice::where('client_id', $clientId)
            ->whereBetween('invoice_payment_date', [$startDate, $endDate])
            ->where('payment_status_id', '2')
            ->whereNull('deleted_at')
            ->sum('invoice_grand_total');

        return $revenue ?? 0;
    }
}
