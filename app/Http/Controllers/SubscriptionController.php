<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\Client;
use App\Models\Brand;
use App\Models\Product;
use App\Models\PaymentStatus;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\InvoiceSetting;
use App\Exports\SubscriptionExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Session;
use Excel;
use Mail;
use PDF;
use File;
use DB;
use Exception;

class SubscriptionController extends Controller
{
    function __construct() {
        $this->middleware('permission:subscription-list|subscription-create|subscription-edit|subscription-delete', ['only' => ['index','store']]);
        $this->middleware('permission:subscription-create', ['only' => ['addSubscription','store']]);
        $this->middleware('permission:subscription-edit', ['only' => ['addSubscription','update']]);
        $this->middleware('permission:subscription-delete', ['only' => ['deleteSubscription']]);
    }

    public function index() {
        $data = Subscription::with(['client'])->orderBy('id', 'desc')->get();
        return view('subscriptions.index', compact('data'));
    }

    public function addSubscription($id = "") {
        $clients = Client::orderBy('client_business_name', 'asc')->whereIsStatus(1)->get();
        $products = Product::orderBy('product_name', 'asc')->whereIsStatus(1)->get();
        $brands = Brand::orderBy('name', 'asc')->whereIsStatus(1)->get();
        $payment_statuses = PaymentStatus::orderBy('name', 'asc')->get();
        if ($id == "") {
            $data = new Subscription;
        } else if ($id > 0) {
            $data = Subscription::where('id', $id)->with(['subscription_payments.product'])->first();
        }
        return view('subscriptions.add', compact('data', 'clients', 'products','brands', 'payment_statuses'));
    }

    public function addSubscriptionAction(Request $request) {

        $post_array = $request->post();
        $payment_status_id = getPaymentStatusId();
        $id = (isset($post_array['id']) && $post_array['id'] > 0) ? $post_array['id'] : 0;

        $action_id = $request->subscription_payment_id;
        $all_subscription_payment_id = Subscription::with(['subscription_payments'])->whereHas('subscription_payments', function($query) use ($request) {
            $query->where('subscription_id', '=', $request->id);
        })->first();

        $subscription_start_date = chnageDateFormat($post_array['subscription_start_date']);
        $subscription_due_date = chnageDateFormat($post_array['subscription_due_date']);
    
        if ($id == 0) {
            $subscription = new Subscription;
        } else if ($id > 0) {
            $subscription = Subscription::find($id);
        }


        if($subscription_start_date == $subscription->subscription_start_date) {
            $sub_start_date = $subscription->subscription_start_date;
            $subscription_next_date = $subscription->subscription_next_date;
        }else {

            $sub_start_date = $subscription_start_date;
            $current_date = Carbon::now()->format('Y-m-d');
            if($sub_start_date > $current_date) {
                $subscription_next_date = $subscription_start_date;
            }else {
                $subscription_next_date = getSubscriptionNextDate($post_array['subscription_cycle'], $sub_start_date);
            }
        }

        $subscription->subscription_name = $post_array['subscription_name'];
        $subscription->subscription_cycle = $post_array['subscription_cycle'];
        $subscription->subscription_start_date = $sub_start_date;
        $subscription->subscription_next_date = $subscription_next_date;
        if(isset($post_array['is_subscription_next_increment']) && $post_array['is_subscription_next_increment']) {
            $subscription->is_subscription_next_increment = 1;
            $subscription->subscription_incremented_percentage = $post_array['subscription_incremented_percentage'];
        }else {
            $subscription->is_subscription_next_increment = 0;
            $subscription->subscription_incremented_percentage = null;
        }
        $subscription->subscription_due_date = $subscription_due_date;
        $subscription->subscription_payment_terms = $post_array['subscription_payment_terms'];
        $subscription->subscription_method = 'send_via_email';
        $subscription->client_id = $post_array['client_id'];
        $subscription->brand_id = $post_array['brand_id'];
        $subscription->subscription_item_total = $post_array['subscription_grand_item_total'];
        $subscription->subscription_grand_gst = $post_array['subscription_grand_gst'];
        $subscription->subscription_grand_total = $post_array['product_final_total'];
        $subscription->subscription_round_off = $post_array['product_final_round_off'];
        $subscription->is_status = 1;
        $response = $subscription->save();

        if(!empty($request->hidden_prod_id[0])) {
            $hidden_prod_id = $request->hidden_prod_id;
            $count = count($hidden_prod_id);
            for ($i = 0; $i < $count; $i++) {     
                if(isset($request->subscription_payment_id[$i]) || !empty($request->subscription_payment_id[$i])) {
                    $subscriptionPayment_id = $request->subscription_payment_id[$i];
                    $subscription_payment = SubscriptionPayment::find($subscriptionPayment_id);
                }else {
                    $subscription_payment = new SubscriptionPayment();
                }
                $subscription_payment->subscription_id = $subscription->id;
                $subscription_payment->product_id = $request->hidden_prod_id[$i];
                $subscription_payment->product_description = $request->hidden_product_description[$i];
                $subscription_payment->product_unit_price = $request->hidden_prod_unit_price[$i];
                $subscription_payment->product_quantity = $request->hidden_prod_quantity[$i];
                $subscription_payment->tax_selection = $request->hidden_prod_tax_sel[$i];
                $subscription_payment->product_subtotal = $request->hidden_product_subtotal[$i];
                $subscription_payment->product_gst = $request->hidden_prod_gst[$i];
                $subscription_payment->product_grand_total = $request->hidden_product_grand_total[$i];
                $subscription_payment->save();
            }
            if (!empty($all_subscription_payment_id)) {
                foreach ($all_subscription_payment_id->subscription_payments as $i_id) {
                    if (in_array($i_id->id, $action_id)) {
                    } else {
                        $delete = SubscriptionPayment::find($i_id->id);
                        $delete->delete();
                    }
                }
            }

        }
        if (empty($action_id) && !empty($request->another_id[0])) {
            foreach ($all_subscription_payment_id->subscription_payments as $i_id) {
                $delete = SubscriptionPayment::find($i_id->id);
                $delete->delete();
            }
        }

        //Generate Invoice if subscription start date is == today's date
        if($subscription_start_date == date('Y-m-d')) {
            //Get Due Date
            $formatted_date = Carbon::create(date('Y-m-d'));
            $add_days = $formatted_date->addDays($subscription->subscription_payment_terms);
            $due_date = Carbon::createFromFormat('Y-m-d H:i:s', $add_days)->format('Y-m-d');
            //Get latest invioce number
            $latest_record = Invoice::latest()->first();
            $invoice_number = $latest_record->invoice_number;
            $invoice_code = str_pad($invoice_number+1 ,6, '0', STR_PAD_LEFT);
            $invoice_setting = InvoiceSetting::first();
            
            $invoice = new Invoice;
            $invoice->invoice_number = $invoice_code;
            $invoice->invoice_date = $subscription_start_date;
            $invoice->invoice_due_date = $due_date;
            $invoice->invoice_payment_terms = $subscription->subscription_payment_terms;
            $invoice->payment_status_id = $payment_status_id;
            $invoice->invoice_payment_date = null;
            $invoice->invoice_po_number = null;
            $invoice->invoice_master_notes = $invoice_setting->invoice_footer_notes;
            $invoice->client_id = $subscription->client_id;
            $invoice->brand_id = $subscription->brand_id;
            $invoice->invoice_method = $subscription->subscription_method;
            $invoice->subscription_id = $subscription->id;
            $invoice->invoice_item_total = $subscription->subscription_item_total;
            $invoice->invoice_grand_gst = $subscription->subscription_grand_gst;
            $invoice->invoice_grand_total = $subscription->subscription_grand_total;
            $invoice->invoice_round_off = $subscription->subscription_round_off;
            $invoice->is_status = 1;
            $invoice->created_at = Carbon::now();
            $invoice->save();

            $subscription_payments = SubscriptionPayment::whereSubscriptionId($subscription->id)->get();
            foreach($subscription_payments as $subscription_payment) {
                $invoice_payment = new InvoicePayment();
                $invoice_payment->invoice_id = $invoice->id;
                $invoice_payment->product_id = $subscription_payment->product_id;
                $invoice_payment->product_description = $subscription_payment->product_description;
                $invoice_payment->tax_selection = $subscription_payment->tax_selection;
                $invoice_payment->product_quantity = $subscription_payment->product_quantity;
                $invoice_payment->product_unit_price = $subscription_payment->product_unit_price;
                $invoice_payment->product_subtotal = $subscription_payment->product_subtotal;
                $invoice_payment->product_gst = $subscription_payment->product_gst;
                $invoice_payment->product_grand_total = $subscription_payment->product_grand_total;
                $invoice_payment->created_at = Carbon::now();
                $invoice_payment->save();
            }

            //Generate invoice pdf and send to client in email
            $send_email = sendEmailOfGeneratedInvoice($invoice);
        }

        if($response) {
            if($id == 0) {
                $message = "Subscription added successfully.";
            }else if ($id > 0) {
                $message = "Subscription updated successfully.";
            }
            $message_class = "success";
        }else {
            if ($id == 0) {
                $message = "Error in adding Subscription. Please try again.";
            } else if ($id > 0) {
                $message = "Error in updating Subscription. Please try again.";
            }
            $message_class = "danger";
        }

        return redirect()->route('subscriptions')->with($message_class, $message);
    }

    public function deleteSubscription($id) {
        $subscription = Subscription::find($id);
        $response = $subscription->delete();
        if ($response) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function deleteSelectedSubscriptionRecords(Request $request) {
        $post_array = $request->post();
        $response = Subscription::whereIn('id', $post_array['ids'])->delete();
        if ($response) {
            Session::flash('success', 'Selected record(s) deleted successfully.');
            $success = 1;
        } else {
            Session::flash('danger', 'Error in deleting selected record(s). Please try again.');
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function getProductDetails($id) {
        $product = Product::find($id);
        $response = [
            'success' => 1,
            'product' =>$product
        ];
        
        return response()->json($response);
    }

    public function getSubscriptionDueDate(Request $request) {
        $formatted_date = Carbon::create($request->subscription_date);
        $add_days = $formatted_date->addDays($request->payment_terms);
        $due_date = Carbon::createFromFormat('Y-m-d H:i:s', $add_days)->format('d-m-Y');
        $response = [
            'success' => 1,
            'due_date' =>$due_date
        ];
        
        return response()->json($response);
    }

    //Subscription cron functions starts
    public function recurringSubscriptionCron() {
        echo "in cron function".'<br>';
        $subscriptions = Subscription::with(['client','brand','subscription_payments.product'])->whereSubscriptionNextDate(Carbon::today())->get();
        foreach($subscriptions as $subscription) {
            $current_date = date('Y-m-d');    
            if($subscription->subscription_cycle == "daily") {
                //Calculate start date of invoice
                $next_date = Carbon::create($subscription->subscription_next_date);
                $daily_date = $next_date->addDay();
                $this->generateSubscriptionForCron($subscription, $current_date, $daily_date);
                
            }else if($subscription->subscription_cycle == "weekly") {
                //Calculate start date of invoice
                $next_date = Carbon::create($subscription->subscription_next_date);
                $weekly_date = $next_date->addWeek();
                $this->generateSubscriptionForCron($subscription, $current_date, $weekly_date);
                
            }else if($subscription->subscription_cycle == "monthly") {
                //Calculate start date of invoice
                $next_date = Carbon::create($subscription->subscription_next_date);
                $monthly_date = $next_date->addMonth();
                $this->generateSubscriptionForCron($subscription, $current_date, $monthly_date);
                
            }else if($subscription->subscription_cycle == "quaterly") {
                //Calculate start date of invoice
                $next_date = Carbon::create($subscription->subscription_next_date);
                $quaterly_date = $next_date->addMonths(3);
                $this->generateSubscriptionForCron($subscription, $current_date, $quaterly_date);
                
            }else if($subscription->subscription_cycle == "yearly") {
                //Calculate start date of invoice
                $next_date = Carbon::create($subscription->subscription_next_date);
                $yearly_date = $next_date->addYear();
                $this->generateSubscriptionForCron($subscription, $current_date, $yearly_date);
                
            }else {
                echo "<br> There is no invoice to generate";        
            }
        }
        echo "<br> Invoice Generated Successfully";
    }

    public function generateSubscriptionForCron($subscription, $current_date, $next_date) {        
        $count_generated_invoice = Invoice::whereSubscriptionId($subscription->id)->count();
        $payment_status_id = getPaymentStatusId();
        if($count_generated_invoice > 0) { 
            $parent_subscription_for_inc = Invoice::whereSubscriptionId($subscription->id)->latest()->first();
            if($subscription->is_subscription_next_increment == 1) {
                $subscription_payments = SubscriptionPayment::whereSubscriptionId($parent_subscription_for_inc->subscription_id)->get(); 
            }else{
                $subscription_payments = SubscriptionPayment::whereSubscriptionId($subscription->id)->get();
            }
        }else {
            $subscription_payments = SubscriptionPayment::whereSubscriptionId($subscription->id)->get();
        }

        //tax calculation values
        $final_total = 0;
        $inclusive_tax = 0;
        $inclusive_tax_amt = 0;
        //Calculate invoice date
        $formatted_date = Carbon::create($current_date);
        $add_days = $formatted_date->addDays($subscription->subscription_payment_terms);
        $due_date = Carbon::createFromFormat('Y-m-d H:i:s', $add_days)->format('Y-m-d');
        $latest_record = Invoice::latest()->first();
        if($latest_record) {
            $invoice_number = $latest_record->invoice_number;
            $invoice_code = str_pad($invoice_number+1 ,6, '0', STR_PAD_LEFT);
        }
        $invoice_setting = InvoiceSetting::first();

        $new_invoice = new Invoice;
        $new_invoice->invoice_number = $invoice_code;
        $new_invoice->invoice_date = $current_date;
        $new_invoice->invoice_due_date = $due_date;
        $new_invoice->invoice_payment_terms = $subscription->subscription_payment_terms;
        $new_invoice->payment_status_id = $payment_status_id;
        $new_invoice->invoice_payment_date = null;
        $new_invoice->invoice_po_number = null;
        $new_invoice->invoice_notes = null;
        $new_invoice->invoice_master_notes = $invoice_setting->invoice_footer_notes;
        $new_invoice->client_id = $subscription->client_id;
        $new_invoice->brand_id = $subscription->brand_id;
        $new_invoice->invoice_method = $subscription->subscription_method;
        $new_invoice->subscription_id = $subscription->id;
        $new_invoice->is_status = 1;
        $new_invoice->created_at = Carbon::now();
        $new_invoice->save();
    
        //Change the next date of subscription
        $subscription->subscription_next_date = $next_date;
       
        foreach($subscription_payments as $subscription_payment) {
            $new_invoice_payment = new InvoicePayment();
            $new_invoice_payment->invoice_id = $new_invoice->id;
            $new_invoice_payment->product_id = $subscription_payment->product_id;
            $new_invoice_payment->product_description = $subscription_payment->product_description;
            $new_invoice_payment->tax_selection = $subscription_payment->tax_selection;
            $new_invoice_payment->product_quantity = $subscription_payment->product_quantity;

            if($subscription->is_subscription_next_increment == 1) {
                $increment = $subscription->subscription_incremented_percentage;
                $tax_type = $subscription_payment->tax_selection;
                $product_quantity = $subscription_payment->product_quantity;
                $unit_price = ($subscription_payment->product_unit_price * $increment) / 100;
                $increment_unit_price = $subscription_payment->product_unit_price + $unit_price;                
                
                $return_values = $this->incrementedTaxCalculation($tax_type, $increment_unit_price, $product_quantity);

                $new_invoice_payment->product_unit_price = $increment_unit_price;
                $new_invoice_payment->product_subtotal = $return_values['final_item_total'];
                $new_invoice_payment->product_gst = $return_values['in_ex_gst_amount'];
                $new_invoice_payment->product_grand_total = $return_values['item_grand_total'];
            
                //Change the value of subscription payment while doing next increment
                $subscription_payment->product_unit_price = $increment_unit_price;
                $subscription_payment->product_subtotal = $return_values['final_item_total'];
                $subscription_payment->product_gst = $return_values['in_ex_gst_amount'];
                $subscription_payment->product_grand_total = $return_values['item_grand_total'];
                $subscription_payment->save();

            }else {
                $new_invoice_payment->product_unit_price = $subscription_payment->product_unit_price;
                $new_invoice_payment->product_subtotal = $subscription_payment->product_subtotal;
                $new_invoice_payment->product_gst = $subscription_payment->product_gst;
                $new_invoice_payment->product_grand_total = $subscription_payment->product_grand_total;
            }
            $new_invoice_payment->created_at = Carbon::now();
            $new_invoice_payment->save();
        }

        $total = $this->grandTotalAmount($new_invoice->id);
        $round_off = getRoundedAmount($total['grand_total']);

        $new_invoice->invoice_item_total = $total['final_item_total'];
        $new_invoice->invoice_grand_gst = $total['final_tax_amt'];
        // $new_invoice->invoice_grand_total = $total['grand_total'];
        $new_invoice->invoice_grand_total = $round_off['amount'];
        $new_invoice->invoice_round_off = $round_off['round_amount'];
        $new_invoice->save();

        //Save the subscription with updated values
        if($subscription->is_subscription_next_increment == 1) {
            $subscription->subscription_item_total = $total['final_item_total'];
            $subscription->subscription_grand_gst = $total['final_tax_amt'];
            // $subscription->subscription_grand_total = $total['grand_total'];
            $subscription->subscription_grand_total = $round_off['amount'];
            $subscription->subscription_round_off = $round_off['round_amount'];
        }
        $subscription->save();

        // Generate invoice pdf and send to client in email
        $send_email = sendEmailOfGeneratedInvoice($new_invoice);
    }

    public function incrementedTaxCalculation($tax_type, $increment_unit_price, $product_quantity) {
        $in_ex_gst_amount = 0;
        $final_total_final =0;
        $final_item_total = 0;
        $item_grand_total = 0;
        if($tax_type == 'GST Inclusive') {
            $final_total = $increment_unit_price * $product_quantity;
            $final_total_final += $final_total;
            // $inclusive_tax =  $final_total*11/(100+11);
            $inclusive_tax =  $final_total / 11;
            $inclusive_tax_amt = round($inclusive_tax ,2);
            $in_ex_gst_amount += $inclusive_tax_amt;
            $final_item_total += $final_total - $inclusive_tax_amt;
            $item_grand_total = $final_item_total + $in_ex_gst_amount;
        }else if($tax_type == 'GST'){   
            $ex_product_total = $increment_unit_price * $product_quantity;
            $final_item_total += $ex_product_total;
            $exclusiv_tax_amt = ($ex_product_total * 10) / 100;
            $round_exclusive_tax = round($exclusiv_tax_amt, 2);
            $in_ex_gst_amount += $exclusiv_tax_amt;

            $item_grand_total = $final_item_total + $in_ex_gst_amount;
        }else if($tax_type == 'No GST') {
            $ng_item_total = $increment_unit_price * $product_quantity;
            $final_item_total += $ng_item_total;
            $in_ex_gst_amount = 0;
            $item_grand_total = $final_item_total;
        }
        $return = [
            'final_item_total' => $final_item_total,
            'in_ex_gst_amount' => $in_ex_gst_amount,
            'item_grand_total' => $item_grand_total
        ];
        
        
        return $return;
    }

    public function grandTotalAmount($clone_invoice_id) {
        $invoice_payments = InvoicePayment::where('invoice_id', $clone_invoice_id)->get();
        $final_item_total = 0;
        $grand_total = 0;
        $final_tax_amt = 0;
        foreach($invoice_payments as $invoce_payment) {
            $subtotal = $invoce_payment->product_subtotal;
            $product_gst = $invoce_payment->product_gst;
            $each_row_total = $subtotal ? $subtotal : 0;
            $each_row_tax = $product_gst ? $product_gst : 0;
            $final_item_total += $each_row_total;
            $final_tax_amt += $each_row_tax;
            $grand_total = $final_item_total + $final_tax_amt;    
        }
        $return = [
            'final_item_total' => round($final_item_total, 2),
            'final_tax_amt' => round($final_tax_amt, 2),
            'grand_total' => round($grand_total, 2),
        ];

        return $return;
    }

    public function changeSubscriptionDueDate() {
        $prev_month_year = date('Y-m', strtotime("-1 month"));
        // $prev_month_year = '2022-08';
        $prev_start_date = $prev_month_year.'-'.'01';
        $prev_end_date = date("Y-m-t", strtotime($prev_start_date));

        try {
            $change_due_date = Subscription::where('subscription_cycle', '=', 'yearly')->whereBetween('subscription_due_date', [$prev_start_date, $prev_end_date])
                ->update(['subscription_due_date' => DB::raw("subscription_due_date + INTERVAL 1 YEAR")]);

            return "Subscription due date is updated successfully.";
        }catch(Exception $e) {
            return "Error in updating subscription due date.";
        }

    }
    //Subscription cron functions ends

    public function exportSubscriptions() {
        return Excel::download(new SubscriptionExport, 'subscriptions.xlsx');
    }
}
