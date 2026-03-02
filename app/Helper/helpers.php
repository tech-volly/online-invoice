<?php

use App\Models\Category;
use App\Models\Supplier;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\LeadFollowUp;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\InvoiceSetting;
use App\Models\PaymentStatus;
use App\Models\InvoicePayment;
use App\Models\Expense;
use App\Models\EmailLog;
use App\Models\InvoiceResourceImage;
use App\Mail\SendInvoiceMail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use PDF;
use File;
use DB;

function getFormatedDate($date = "", $format = 'd F, Y') {
    if ($date == "") {
        $date = date('Y-m-d');
    }
    return date($format, strtotime($date));
}

function getFormatedDateTime($date_time = "", $format = 'd F Y, g:i A') {
    if ($date_time == "") {
        return "---";
    }
    return date($format, strtotime($date_time));
}

//Function for date at insert time
function chnageDateFormat($date) {
    $split_date = explode("-", $date);
    $formatted_date = $split_date[2] . '-' . $split_date[1] . '-' . $split_date[0];
    return $formatted_date;
}

function getDateDisplayFormat($date) {
    $split_date = explode("-", $date);
    $formatted_date = $split_date[2] . '-' . $split_date[1] . '-' . $split_date[0];
    return $formatted_date;
}

//Function for date and time at insert time
function changeDateTimeFormat($datetime) {
    $formatted_datetime = Carbon::parse($datetime);
    return $formatted_datetime;
}

// Function to change date format at export time
function changeDateFormatAtExport($date) {
    return date("d/m/Y", strtotime($date));
}

function replaceSpaceWithDash($product_title) {
    $product_title = strtolower(str_replace(" ", "-", $product_title));
    $product_title = preg_replace('/[^A-Za-z0-9\-]/', '-', $product_title);
    $product_title = preg_replace('/\-$/', '', $product_title);
    $product_title = preg_replace('/-+/', '-', $product_title);
    return $product_title;
}

function categoryName($id) {
    $category = Category::find($id);
    return $category ? $category->name : '';
}

function getExpenseCategory($id) {
    $category = ExpenseCategory::find($id);
    return $category ? $category->name : '';
}

function getSupplierName($id) {
    $supplier = Supplier::find($id);
    return $supplier ? $supplier->supplier_first_name . ' ' . $supplier->supplier_last_name : '';
}

function getPaymentMethodName($id) {
    $payment_method = PaymentMethod::find($id);
    return $payment_method ? $payment_method->payment_method_name : '';
}

function getLeadFollowUpDetails($lead_id, $val = "Y") {
    $lead_followup = LeadFollowUp::whereLeadId($lead_id)->orderBy('id', 'desc')->first();
    if ($val == "Y") {
        return $lead_followup ? ($lead_followup->followup_datetime ? getFormatedDate($lead_followup->followup_datetime) : '') : '';
    }
    return $lead_followup ? ($lead_followup->followup_datetime ? changeDateFormatAtExport($lead_followup->followup_datetime) : '') : '';
}

function getLeadDiscussionDate($lead_id, $val = "Y") {
    $lead_discussion = LeadFollowUp::whereLeadId($lead_id)->orderBy('id', 'desc')->first();
    if ($val == "Y") { 
        return $lead_discussion ? ($lead_discussion->lead_discussion_date ? getFormatedDate($lead_discussion->lead_discussion_date) : '' ) : '';    
    }
    return $lead_discussion ? ($lead_discussion->lead_discussion_date ? changeDateFormatAtExport($lead_discussion->lead_discussion_date) : '' ) : '';
}

function generateRandomPassword() {
    $comb = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array();
    $combLen = strlen($comb) - 1;
    for ($i = 0; $i < 6; $i++) {
        $n = rand(0, $combLen);
        $pass[] = $comb[$n];
    }
    return implode($pass);
}

function getCalculatedPrice($tax_type, $product_price) {
    if ($tax_type == 'GST Inclusive') {
        // $product_gst_value = $product_price * 11 / (100 + 11);
        $product_gst_value = $product_price / 11;
        $product_base_price = $product_price - $product_gst_value;
        $product_final_price = $product_price;
    } else if ($tax_type == 'GST') {
        $product_gst_value = ($product_price * 10) / 100;
        $product_base_price = null;
        $product_final_price = $product_price + $product_gst_value;
    } else if ($tax_type == 'No GST') {
        $product_base_price = null;
        $product_gst_value = null;
        $product_final_price = $product_price;
    }
    $success = [
        'product_base_price' => round($product_base_price, 2),
        'product_gst_value' => round($product_gst_value, 2),
        'product_final_price' => round($product_final_price, 2)
    ];

    return $success;
}

function getGstPriceForExpense($tax_type, $amount, $symbol = "Y") {
    if ($tax_type == 'GST Inclusive') {
        // $gst_value = $amount * 11 / (100 + 11);
        $gst_value = $amount / 11;
    }else if($tax_type == 'No GST') {
        $gst_value = 0;
    }else {
        $gst_value = 0;
    }

    return getPrice($gst_value, $symbol);
}

function getSubscriptionNextDate($subscription_cycle, $subscription_start_date) {
    if ($subscription_cycle == 'daily') {
        $start_date = Carbon::create($subscription_start_date);
        $next_date = $start_date->addDay();
    } elseif ($subscription_cycle == 'weekly') {
        $start_date = Carbon::create($subscription_start_date);
        $next_date = $start_date->addWeek();
    } elseif ($subscription_cycle == 'monthly') {
        $start_date = Carbon::create($subscription_start_date);
        $next_date = $start_date->addMonth();
    } elseif ($subscription_cycle == 'quaterly') {
        $start_date = Carbon::create($subscription_start_date);
        $next_date = $start_date->addMonths(3);
    } elseif ($subscription_cycle == 'yearly') {
        $start_date = Carbon::create($subscription_start_date);
        $next_date = $start_date->addYear();
    }

    return $next_date;
}

function getGeneratedSubscriptionCount($subscription_id) {
    $invoices = Invoice::whereSubscriptionId($subscription_id)->count();
    return $invoices;
}

function getNextAmountForSubscription($id) {
    $subscription = Subscription::whereId($id)->with(['subscription_payments'])->first();
    $grand_total = calculateIncrementedPrice($subscription);

    return $grand_total;
}

function calculateIncrementedPrice($subscription) {
    $inclusive_tax_amt_final = 0;
    $final_total_final = 0;
    $ex_product_total_final = 0;
    $final_item_total = 0;
    $exclusiv_tax_amt_final = 0;
    $grand_total = 0;
    $ng_total_final = 0;
    $final_total = 0;
    $inclusive_tax = 0;
    $inclusive_tax_amt = 0;
    $item_total = 0;
    $ex_product_total = 0;
    $exclusiv_tax_amt = 0;
    $round_exclusive_tax = 0;
    $ng_item_total = 0;

    $generated_invoice = getGeneratedSubscriptionCount($subscription->id);
    
    foreach ($subscription->subscription_payments as $subscription_payment) {
        $tax_type = $subscription_payment->tax_selection;
        $product_quantity = $subscription_payment->product_quantity;
        if($generated_invoice > 0 && $subscription->is_subscription_next_increment == 1) {
            $increment = $subscription->subscription_incremented_percentage;
            $unit_price = ($subscription_payment->product_unit_price * $increment) / 100;
            $increment_unit_price = $subscription_payment->product_unit_price + $unit_price;
        }else if ($generated_invoice == 0 && $subscription->is_subscription_next_increment == 1){
            $increment_unit_price = $subscription_payment->product_unit_price;
        }else {
            $increment_unit_price = $subscription_payment->product_unit_price;
        }

        if ($tax_type == 'GST Inclusive') {
            $final_total = $increment_unit_price * $product_quantity;
            $final_total_final += $final_total;
            // $inclusive_tax = $final_total * 11 / (100 + 11);
            $inclusive_tax = $final_total / 11;
            $inclusive_tax_amt = round($inclusive_tax, 2);
            $inclusive_tax_amt_final += $inclusive_tax_amt;
            $item_total += $final_total - $inclusive_tax_amt;
        } else if ($tax_type == 'GST') {
            $ex_product_total = $increment_unit_price * $product_quantity;
            $ex_product_total_final += $ex_product_total;
            $exclusiv_tax_amt = ($ex_product_total * 10) / 100;
            $round_exclusive_tax = round($exclusiv_tax_amt, 2);
            $exclusiv_tax_amt_final += $exclusiv_tax_amt;
        } else if ($tax_type == 'No GST') {
            $ng_item_total = $increment_unit_price * $product_quantity;
            $ng_total_final += $ng_item_total;
        }

        $final_item_total = $item_total + $ex_product_total_final + $ng_total_final;
        $grand_total = $final_item_total + $inclusive_tax_amt_final + $exclusiv_tax_amt_final;
    }
    return $grand_total;
}

function sendEmailOfGeneratedInvoice($invoice) {
    $generated_invoice = Invoice::with(['client', 'brand', 'invoice_payments.product'])->whereId($invoice->id)->first();
    $invoice_setting = InvoiceSetting::first();
    $data = [
        'invoice' => $generated_invoice,
        'invoice_setting' => $invoice_setting
    ];
    $pdf = PDF::loadView('invoices.invoice-pdf', $data);
    Storage::put('public/invoices/' . $invoice->id . '/' . $invoice->invoice_number . '.pdf', $pdf->output());
    $details = [
        'userName' => $generated_invoice->client->client_business_name,
        'file' => storage_path('app/public/invoices/' . $invoice->id . '/' . $invoice->invoice_number . '.pdf'),
        'invoice_number' => $generated_invoice->invoice_number,
        'invoice_date' => $generated_invoice->invoice_date,
        'invoice_due_date' => $generated_invoice->invoice_due_date,
        'invoice_total' => $generated_invoice->invoice_grand_total
    ];

    $all_cc_emails_arr = [];
    if($invoice->invoice_emails) { 
        $temp_arr = explode(",", $invoice->invoice_emails);
        foreach($temp_arr as $temp) {
            array_push($all_cc_emails_arr, trim($temp));    
        }
        array_push($all_cc_emails_arr, config('app.cc_admin_email'));
    }else {
        $all_cc_emails_arr = [config('app.cc_admin_email')];
    }
    $clientEmailsArray = explode(',', $generated_invoice->client->client_email);
    Mail::to($clientEmailsArray)->cc($all_cc_emails_arr)->send(new SendInvoiceMail($details));
    
    $save_activity = [
        'email_sender' => config('app.from_email_address'),
        'email_receiver' => $generated_invoice->client->client_email,
        'email_content' => 'Invoice #'.$details['invoice_number'].' from S & P Family Trust Trading as HDS',
        'email_send_date' => Carbon::now()->format('Y-m-d H:i:s')
    ];
    $create_log = saveEmailActivity($save_activity);

    if (count(Mail::failures()) > 0) {
        $return = 0;
        return back()->with('danger', 'Error in sending email to client email address.');
    }
    $return = 1;
    return $return;
}

function getPaymentStatusId() {
    $payment_status = PaymentStatus::whereName('Unpaid')->first();

    return $payment_status->id;
}

function getPaidPaymentStatusId() {
    $payment_status = PaymentStatus::whereName('Paid')->first();

    return $payment_status->id;   
}

function getPaymentStatusName($id) {
    $payment_status = PaymentStatus::whereId($id)->first();

    return $payment_status->name;   
}

function getPrice($num = "", $symbol = "Y") {
    if($symbol == "Y") {
        $num = number_format($num, 2);
    }
    $num_exp = explode(".", $num);
    $num = $num_exp[0];
    
    if(@$num_exp[1] == "00" || @$num_exp[1] == "") {
        $decimal = "";
    } else {
        $decimal = "." . str_pad(@$num_exp[1], 2, 0, STR_PAD_RIGHT);
    }
    if($symbol == "Y") {
        $thecash = '$ ' . $num . $decimal;
    }else {
        $thecash = $num . $decimal;
    }
    return $thecash;
}

function getSEOData($data = []) {
    if (!isset($data['seo_title'])) {
        $seo_data['seo_title'] = "HDS Financials";
    } else {
        $seo_data['seo_title'] = $data['seo_title'];
    }

    $route_name = "";
    $route_info = request()->route();
    if (isset($route_info) && !empty($route_info)) {
        $route_info = request()->route()->getAction();
        $route_name = $route_info['as'];
    }

    if ($route_name == "dashboard") {
        $seo_data['seo_title'] = "HDS Dashboard";
    } else if ($route_name == "clients" || $route_name == "clients.add" || $route_name == "clients.edit") {
        $seo_data['seo_title'] = "HDS Clients";
    } else if ($route_name == "suppliers" || $route_name == "suppliers.add" || $route_name == "suppliers.edit") {
        $seo_data['seo_title'] = "HDS Suppliers";
    } else if ($route_name == "users" || $route_name == "users.add" || $route_name == "users.edit") {
        $seo_data['seo_title'] = "HDS Users";
    } else if ($route_name == "products" || $route_name == "products.add" || $route_name == "products.edit") {
        $seo_data['seo_title'] = "HDS Products";
    } else if ($route_name == "expenses" || $route_name == "expenses.add" || $route_name == "expenses.edit") {
        $seo_data['seo_title'] = "HDS Expenses";
    } else if ($route_name == "invoices" || $route_name == "invoices.add" || $route_name == "invoices.edit") {
        $seo_data['seo_title'] = "HDS Invoices";
    } else if ($route_name == "subscriptions" || $route_name == "subscriptions.add" || $route_name == "subscriptions.edit") {
        $seo_data['seo_title'] = "HDS Subscriptions";
    } else if ($route_name == "quotes" || $route_name == "quotes.add" || $route_name == "quotes.edit") {
        $seo_data['seo_title'] = "HDS Quotes";
    } else if ($route_name == "leads" || $route_name == "leads.add" || $route_name == "leads.edit") {
        $seo_data['seo_title'] = "HDS Leads";
    } else if ($route_name == "invoice-resources" || $route_name == "invoice-resources.add" || $route_name == "invoice-resources.edit") {
        $seo_data['seo_title'] = "HDS Resources";
    } else if ($route_name == "roles") {
        $seo_data['seo_title'] = "HDS Roles & Permissions";
    } else if ($route_name == "categories") {
        $seo_data['seo_title'] = "HDS Product Categories";
    } else if ($route_name == "expense.categories") {
        $seo_data['seo_title'] = "HDS Expense Categories";
    } else if ($route_name == "departments") {
        $seo_data['seo_title'] = "HDS Departments";
    } else if ($route_name == "payment-methods") {
        $seo_data['seo_title'] = "HDS Payment Methods";
    } else if ($route_name == "payment-statuses") {
        $seo_data['seo_title'] = "HDS Payment Statuses";
    } else if ($route_name == "brands") {
        $seo_data['seo_title'] = "HDS Brands";
    } else if ($route_name == "invoice-settings") {
        $seo_data['seo_title'] = "HDS Invoice Settings";
    } else if ($route_name == "estimate-settings") {
        $seo_data['seo_title'] = "HDS Estimate Settings";
    } else if ($route_name == "reports") {
        $seo_data['seo_title'] = "HDS Reports";
    } else if($route_name == "email-logs") {
        $seo_data['seo_title'] = "HDS Email Logs";   
    } else if($route_name == "expected-expenses" || $route_name == "expected-expenses.edit") {
        $seo_data['seo_title'] = "HDS Expected Expenses";   
    }
    $seo_data['seo_title'] = $seo_data['seo_title'];
    
    return $seo_data;
}

function getProfitForQuarterReport($net_profit, $income) {
    if($net_profit == 0) {
        $profit = 0;
    }else if($income == 0) {
        $profit = 0;
    }else {
        $res = ($net_profit / $income) * 100;
        $profit = round($res, 2);
    }

    return $profit;
}

function getInvoiceProductCategories($id) {
    $invoice_payments = InvoicePayment::whereInvoiceId($id)->get();
    $products_id = array_column($invoice_payments->toArray(), 'product_id');
    $product_categories = DB::table('products')
        ->whereIn('products.id', $products_id)
        ->leftjoin('categories', 'categories.id', '=', 'products.category_id')
        ->select('categories.name as category_name')
        ->get();

    $categories = implode(', ' ,array_unique(array_column($product_categories->toArray(), 'category_name')));
    
    return $categories;
}

function splitYearMonth($date) {
    $split_date = explode("-", $date);
    $formatted_date = $split_date[1] . '-' . $split_date[0];
    
    return $formatted_date;
}

function splitYear($date) {
    $split_date = explode("-", $date);
    
    return $split_date[1];
}

function formatPrice($price) {
    $thecash = floatval(preg_replace('/[^\d.]/', '', $price));

    return $thecash;
}

function removedExtraSymbolsFromPrice($price) {
    $replace_dollar = str_replace('$ ', '', $price);
    $replace_comma = str_replace(',', '', $replace_dollar);

    return $replace_comma;
}

// Helper functions for dashboard starts
function gstPaidByQuarter($q_start_date, $q_end_date) {
    $expense_gst_paid = Expense::whereBetween('expense_date', [$q_start_date, $q_end_date])->get();
    $q_gst_paid = 0;
    foreach($expense_gst_paid as $key => $value) {
        if ($value->expense_tax == 'GST Inclusive') {
            // $q_gst_paid += $value->expense_amount * 11 / (100 + 11);
            $q_gst_paid += $value->expense_amount / 11;
        }else if($value->expense_tax == 'No GST') {
            $q_gst_paid +=  0;
        }
    }

    return $q_gst_paid;
}

function gstCollectedByQuarter($q_start_date, $q_end_date) {
    $income_collected_gst = DB::table('invoices')->whereBetween('invoice_payment_date', [$q_start_date, $q_end_date])
        ->where('invoices.deleted_at', '=', null)->select(DB::raw("SUM(invoice_grand_gst) as invoice_grand_gst"))
        ->get();

    return $income_collected_gst[0]->invoice_grand_gst;
}

function totalIncomeByQuarter($q_start_date, $q_end_date) {
    $q_income = DB::table('invoices')->whereBetween('invoice_payment_date', [$q_start_date, $q_end_date])
        ->where('invoices.deleted_at', '=', null)->select(DB::raw('sum(invoices.invoice_grand_total) as q_total_income'))->get();

    return $q_income[0]->q_total_income;
}

function totalExpenseByQuarter($q_start_date, $q_end_date) {
    $q_expense = DB::table('expenses')->whereBetween('expense_date', [$q_start_date, $q_end_date])
        ->where('expenses.deleted_at', '=', null)->select(DB::raw('sum(expenses.expense_amount) as q_total_expense'))->get();

    return $q_expense[0]->q_total_expense;
}

function totalProfitByQuarter($income, $expense) {
    $net_profit = $income - $expense;

    return $net_profit;
}

function getExpenseCategoryId($category_name) {
    $category = ExpenseCategory::whereName($category_name)->first();

    return $category ? $category->id : 0;
}

function getCategoryExpense($category_id) {
    $expense =  DB::table('expenses')->where('expenses.supplier_expense_category', $category_id)
        ->where('expenses.deleted_at', '=', null)
        ->select(DB::raw("SUM(expense_amount) as category_expense"))
        ->get();

    return $expense[0]->category_expense;
}
// Helper functions for dashboard ends

function saveEmailActivity($request) {
    $create_log = EmailLog::create($request);

    return $create_log;
}

function getRoundedAmount($X = 0) {
    $ip_exp = explode(".", $X);

    $IP = $ip_exp[0];
    $FP = isset($ip_exp[1]) ? $ip_exp[1] : 0;

    if (strlen($FP) == 1) {
        $FP1 = substr($FP, 0);
        $FP2 = 0;
    } else if (strlen($FP) == 0) {
        $FP1 = 0;
        $FP2 = 0;
    } else {
        $FP1 = substr($FP, 0, -1);
        $FP2 = substr($FP, 1);
    }

    switch ($FP2) {
        case 0:
            break;
        Case 1:
        Case 2:
        Case 3:
            $FP2 = 0;
            break;
        Case 4:
        Case 5:
        Case 6:
        Case 7:
            $FP2 = 5;
            break;
        Case 8:
        Case 9:
            $FP2 = 0;
            $FP1++;
            break;
        default:
            break;
    }

    if ($FP1 == 10 && $FP2 == 0) {
        $IP++;
        $Y = $IP . ".00";
    } else {
        $Y = $IP . "." . $FP1 . $FP2;
    }
    
    $round_amount = 0.00;
    if ($X > $Y) {
        $round_amount = "- " . ( ( floor($X * 100) - floor($Y * 100) ) / 100 );
    } else {
        $round_amount = ( ( floor($Y * 100) - floor($X * 100) ) / 100 );
    }
    
    $return['amount'] = $Y;
    $return['round_amount'] = $round_amount;
    
    return $return;
}

function getFirstResource($id) {
    $invoice_resource = InvoiceResourceImage::whereInvoiceResourceId($id)->first();

    return $invoice_resource;
}

function getExpectedExpenseData($current_year, $next_year, $fields1, $fields2) {
    $expected_fin_year = $current_year.' - '.$next_year;
    $expected_expense_data_1 = DB::table('expected_expenses')->where('expected_expenses.expected_expense_year', $expected_fin_year)
        ->leftjoin('expected_expense_lists', 'expected_expense_lists.expected_expense_id', '=', 'expected_expenses.id')
        ->where('expected_expenses.deleted_at', '=', null)
        ->where('expected_expense_lists.deleted_at', '=', null)
        ->select(
            \DB::raw($fields1),
    )->get();
   
    $expected_expense_data_1 = $expected_expense_data_1->toArray()[0];
    $expected_fin_year = $next_year.' - '.$next_year+1;
    
    $expected_expense_data_2 = DB::table('expected_expenses')->where('expected_expenses.expected_expense_year', $expected_fin_year)
        ->leftjoin('expected_expense_lists', 'expected_expense_lists.expected_expense_id', '=', 'expected_expenses.id')
        ->where('expected_expenses.deleted_at', '=', null)
        ->where('expected_expense_lists.deleted_at', '=', null)
        ->select(
            \DB::raw($fields2)
    )->get();
    $expected_expense_data_2 = $expected_expense_data_2->toArray()[0];

    $final_arr = [
        'expected_expense_data_1' => $expected_expense_data_1,
        'expected_expense_data_2' => $expected_expense_data_2
    ];

    return $final_arr;
}