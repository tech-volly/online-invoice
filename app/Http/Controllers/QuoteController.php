<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quote;
use App\Models\QuotePayment;
use App\Models\Client;
use App\Models\Product;
use App\Models\Brand;
use App\Models\PaymentStatus;
use App\Models\EstimateSetting;
use App\Mail\SendQuoteMail;
use App\Exports\QuotesExport;
use App\Exports\QuotesByFilterExport;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Session;
use Excel;
use Mail;
use PDF;
use File;

class QuoteController extends Controller
{
    function __construct() {
        $this->middleware('permission:quote-list|quote-create|quote-edit|quote-delete', ['only' => ['index','store']]);
        $this->middleware('permission:quote-create', ['only' => ['addQuote','store']]);
        $this->middleware('permission:quote-edit', ['only' => ['addQuote','update']]);
        $this->middleware('permission:quote-delete', ['only' => ['deleteQuote']]);
    }
    
    public function index(Request $request) {
        if($request->get('client') || $request->get('quote_status')) { 
            $where_query = "1";
            if($request->get('quote_status') && $request->get('quote_status') != 'null') {
                $quote_status = $request->get('quote_status');
                $where_query .= " AND (quote_payment_status = '" .$quote_status. "' ";
                $where_query .= ')';
            }
            $where_has = "1";
            if($request->get('client') && $request->get('client') != 'null') {
                $client = $request->get('client');
                $where_has .= " AND ( id = '" . $client . "' ";
                $where_has .= ')';
            }
            $data = Quote::with(['client'])->whereRaw($where_query)->whereHas('client', function($query) use ($where_has) {
                $query->whereRaw($where_has);
            })->orderBy('id', 'desc')->get();

        }else {
            $data = Quote::with(['client', 'payment_status'])->orderBy('id', 'desc')->get();
        }
        $clients = Client::orderBy('client_business_name', 'asc')->whereIsStatus(1)->get();
        
        return view('quotes.index', compact('data', 'clients'));
    }

    public function addQuote($id = "") {
        $clients = Client::orderBy('client_business_name', 'asc')->whereIsStatus(1)->get();
        $products = Product::orderBy('product_name', 'asc')->whereIsStatus(1)->get();
        $brands = Brand::orderBy('name', 'asc')->whereIsStatus(1)->get();
        $payment_statuses = PaymentStatus::orderBy('name', 'asc')->get();
        $estimate_setting = EstimateSetting::first();
        if ($id == "") {
            $data = new Quote;
        } else if ($id > 0) {
            $data = Quote::where('id', $id)->with(['quote_payments.product'])->first();
        }
        return view('quotes.add', compact('data', 'clients', 'products','brands', 'payment_statuses', 'estimate_setting'));
    }

    public function addQuoteAction(Request $request) {
        $post_array = $request->post();
        $id = (isset($post_array['id']) && $post_array['id'] > 0) ? $post_array['id'] : 0;

        $action_id = $request->quote_payment_id;
        $all_quote_payment_id = Quote::with(['quote_payments'])->whereHas('quote_payments', function($query) use ($request) {
            $query->where('quote_id', '=', $request->id);
        })->first();

        $quote_date = chnageDateFormat($post_array['quote_date']);
    
        $is_quote = Quote::latest()->first();
        if($is_quote) {
            $quote_number = $is_quote->quote_number;
            $quote_code = str_pad($quote_number+1 ,6, '0', STR_PAD_LEFT);
        }else{
            $quote_code = str_pad('1',6, '0', STR_PAD_LEFT);    
        } 
        
        if ($id == 0) {
            $quote = new Quote;
        } else if ($id > 0) {
            $quote = Quote::where('id', $id)->first();
            $quote_code = $quote->quote_number;
        }
        $quote->quote_number = $quote_code;
        $quote->quote_date = $quote_date;
        $quote->quote_method = 'send_via_email';
        $quote->client_id = $post_array['client_id'];
        $quote->brand_id = $post_array['brand_id'];
        $quote->quote_payment_status = $post_array['quote_payment_status'];
        $quote->quote_discount = $post_array['quote_discount'];
        $quote->quote_emails = $post_array['quote_emails'];
        $quote->is_status = 1;
        $quote->product_final_discount_amt = $post_array['product_final_discount_amt'];
        $quote->quote_item_total = $post_array['quote_item_total'];
        $quote->quote_grand_gst = $post_array['quote_grand_gst'];
        $quote->quote_grand_total = $post_array['quote_grand_total'];    
        $quote->quote_round_off = $post_array['product_final_round_off']; 
        $quote->quote_master_notes = $post_array['quote_master_notes'];
        $response = $quote->save();

        if(!empty($request->hidden_prod_id[0])) {
            $hidden_prod_id = $request->hidden_prod_id;
            $count = count($hidden_prod_id);
            for ($i = 0; $i < $count; $i++) {     
                if(isset($request->quote_payment_id[$i]) || !empty($request->quote_payment_id[$i])) {
                    $quotePayment_id = $request->quote_payment_id[$i];
                    $quote_payment = QuotePayment::find($quotePayment_id);
                }else {
                    $quote_payment = new QuotePayment();
                }
                $quote_payment->quote_id = $quote->id;
                $quote_payment->product_id = $request->hidden_prod_id[$i];
                $quote_payment->product_description = $request->hidden_product_description[$i];
                $quote_payment->product_unit_price = $request->hidden_prod_unit_price[$i];
                $quote_payment->product_quantity = $request->hidden_prod_quantity[$i];
                $quote_payment->tax_selection = $request->hidden_prod_tax_sel[$i];
                $quote_payment->product_subtotal = $request->hidden_product_subtotal[$i];
                $quote_payment->product_gst = $request->hidden_prod_gst[$i];
                $quote_payment->product_grand_total = $request->hidden_product_grand_total[$i];
                $quote_payment->save();
            }
            if (!empty($all_quote_payment_id)) {
                foreach ($all_quote_payment_id->quote_payments as $i_id) {
                    if (in_array($i_id->id, $action_id)) {
                    } else {
                        $delete = QuotePayment::find($i_id->id);
                        $delete->delete();
                    }
                }
            }

        }
        if (empty($action_id) && !empty($request->another_id[0])) {
            foreach ($all_quote_payment_id->quote_payments as $i_id) {
                $delete = QuotePayment::find($i_id->id);
                $delete->delete();
            }
        }

        //Generate quote pdf and send to client in email
        $generated_quote = Quote::with(['client','brand','quote_payments.product'])->whereId($quote->id)->first();
        $estimate_setting = EstimateSetting::first();
        $data = [
            'quote' => $generated_quote,
            'estimate_setting' => $estimate_setting
        ];
        $pdf = PDF::loadView('quotes.quote-pdf', $data);
        Storage::put('public/quotes/'.$quote->id.'/'.$quote->quote_number.'.pdf', $pdf->output());
        $details = [
            'userName' => $generated_quote->client->client_business_name,
            'quote_number' => $generated_quote->quote_number,
            'quote_date' => $generated_quote->quote_date,
            'quote_total' => $generated_quote->quote_grand_total,
            'file' => storage_path('app/public/quotes/'.$quote->id.'/'.$quote->quote_number.'.pdf'),   
        ];

        $all_cc_emails_arr = [];
        if($quote->quote_emails) { 
            $temp_arr = explode(",", $quote->quote_emails);
            foreach($temp_arr as $temp) {
                array_push($all_cc_emails_arr, trim($temp));    
            }
            array_push($all_cc_emails_arr, config('app.cc_admin_email'));
        }else {
            $all_cc_emails_arr = [config('app.cc_admin_email')];
        }
        $clientEmailsArray = explode(',',$generated_quote->client->client_email);
        Mail::to($clientEmailsArray)->cc($all_cc_emails_arr)->send(new SendQuoteMail($details));

        $save_activity = [
            'email_sender' => config('app.from_email_address'),
            'email_receiver' => $generated_quote->client->client_email,
            'email_content' => 'Quote #'.$details['quote_number'].' from S & P Family Trust Trading as HDS',
            'email_send_date' => Carbon::now()->format('Y-m-d H:i:s')
        ];
        $create_log = saveEmailActivity($save_activity); 

        if (count(Mail::failures()) > 0) {
            return back()->with('danger', 'Error in sending email to client email address.');
        }

        if($response) {
            if($id == 0) {
                $message = "Quote added successfully.";
            }else if ($id > 0) {
                $message = "Quote updated successfully.";
            }
            $message_class = "success";
        }else {
            if ($id == 0) {
                $message = "Error in adding Quote. Please try again.";
            } else if ($id > 0) {
                $message = "Error in updating Quote. Please try again.";
            }
            $message_class = "danger";
        }

        return redirect()->route('quotes')->with($message_class, $message);

    }

    public function getProductDetails($id) {
        $product = Product::find($id);
        $response = [
            'success' => 1,
            'product' =>$product
        ];
        
        return response()->json($response);
    }

    public function cloneQuote($id) {
        $quote = Quote::find($id);
        $latest_record = Quote::latest()->first();
        if($latest_record) {
            $quote_number = $latest_record->quote_number;
            $quote_code = str_pad($quote_number+1 ,6, '0', STR_PAD_LEFT);
        }
        $quote_payments = QuotePayment::where('quote_id', $id)->get();
     
        $clone_quote = $quote->replicate();
        $clone_quote->quote_number = $quote_code;
        $clone_quote->quote_payment_status = 'Open';
        $clone_quote->created_at = Carbon::now();
        $clone_quote->save();
        
        foreach($quote_payments as $quote_payment) {
            $clone_quote_payment = new QuotePayment();
            $clone_quote_payment->quote_id = $clone_quote->id;
            $clone_quote_payment->product_id = $quote_payment->product_id;
            $clone_quote_payment->product_description = $quote_payment->product_description;
            $clone_quote_payment->product_unit_price = $quote_payment->product_unit_price;
            $clone_quote_payment->product_quantity = $quote_payment->product_quantity;
            $clone_quote_payment->tax_selection = $quote_payment->tax_selection;
            $clone_quote_payment->product_subtotal = $quote_payment->product_subtotal;
            $clone_quote_payment->product_gst = $quote_payment->product_gst;
            $clone_quote_payment->product_grand_total = $quote_payment->product_grand_total;
            $clone_quote_payment->save();
        }

        return redirect()->route('quotes.edit', $clone_quote->id)->with('success', 'Quote clonned successfully.');
    }

    public function deleteSelectedQuoteRecords(Request $request) {
        $post_array = $request->post();
        $response = Quote::whereIn('id', $post_array['ids'])->delete();
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

    public function deleteQuote($id) {
        $quote = Quote::find($id);
        $response = $quote->delete();
        if ($response) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function generateQuotePDF($id) {
        $quote = Quote::with(['client','brand','quote_payments.product'])->whereId($id)->first();
        $estimate_setting = EstimateSetting::first();
        $data = [
            'quote' => $quote,
            'estimate_setting' => $estimate_setting

        ];
        $pdf = PDF::chunkLoadView('<html-separator/>', 'quotes.quote-pdf', $data);
		
        return $pdf->download('Quote-'.$quote->quote_number.'.pdf');
    }

    public function exportQuotes() {
        return Excel::download(new QuotesExport, 'quotes.xlsx');
    }

    public function exportQuotesByOptions(Request $request) {
        $params = $request->all();
        
        return Excel::download(new QuotesByFilterExport($params), 'quotes.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
