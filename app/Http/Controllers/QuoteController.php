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
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DateTime;
use Session;
use Excel;
use Mail;
use PDF;
use File;
use DB;

class QuoteController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:quote-list|quote-create|quote-edit|quote-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:quote-create', ['only' => ['addQuote', 'store']]);
        $this->middleware('permission:quote-edit', ['only' => ['addQuote', 'update']]);
        $this->middleware('permission:quote-delete', ['only' => ['deleteQuote']]);
    }

    public function index(Request $request)
    {
        // For server-side pagination we only return the view; data will be fetched via AJAX
        $clients = Client::orderBy('client_business_name', 'asc')->whereIsStatus(1)->get();

        return view('quotes.index', compact('clients'));
    }

    /**
     * Server-side DataTable source for Quotes
     */
    public function getQuotes(Request $request)
    {
        $columns = [
            0 => 'quotes.id',
            1 => 'quotes.quote_number',
            2 => 'clients.client_business_name',
            3 => 'quotes.quote_date',
            4 => 'quotes.quote_grand_total',
            5 => 'quotes.quote_payment_status',
            6 => 'quotes.is_status'
        ];

        $length = $request->input('length');
        $start  = $request->input('start');
        $search = $request->input('search.value');

        $query = Quote::leftJoin('clients', 'quotes.client_id', '=', 'clients.id')
            ->select('quotes.*', 'clients.client_business_name');

        // Filters
        if ($request->has('client') && $request->get('client')) {
            $query->where('quotes.client_id', $request->get('client'));
        }
        if ($request->has('quote_status') && $request->get('quote_status')) {
            $query->where('quotes.quote_payment_status', $request->get('quote_status'));
        }

        $totalData = $query->count();

        // Search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('clients.client_business_name', 'LIKE', "%{$search}%")
                    ->orWhere('quotes.quote_number', 'LIKE', "%{$search}%")
                    ->orWhere('quotes.quote_grand_total', 'LIKE', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();

        // Ordering
        if ($request->has('order')) {
            $orderColumnIndex = $request->input('order.0.column');
            $orderDir = $request->input('order.0.dir');

            if (isset($columns[$orderColumnIndex])) {
                $query->orderBy($columns[$orderColumnIndex], $orderDir);
            }
        } else {
            $query->orderBy('quotes.id', 'desc');
        }

        $quotes = $query->skip($start)
            ->take($length)
            ->get();

        $data = [];
        foreach ($quotes as $v) {
            $action = '<div class="dropdown dropdown-action">';
            $action .= '<a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>';
            $action .= '<div class="dropdown-menu dropdown-menu-right">';

            if (auth()->user()->can('quote-edit')) {
                $action .= '<a class="dropdown-item" href="' . route('quotes.edit', $v->id) . '">'
                    . '<i class="fa fa-pencil m-r-5"></i> Edit</a>';
            }
            if (auth()->user()->can('quote-delete')) {
                $action .= '<a class="dropdown-item deleteQuoteBtn" href="javascript:void(0)" data-id="' . $v->id . '">'
                    . '<i class="fa fa-trash-o m-r-5"></i> Delete</a>';
            }
            $action .= '<a class="dropdown-item" href="' . route('quote.generate-invoice', $v->id) . '" data-id="' . $v->id . '">'
                . '<i class="fa fa-clone m-r-5"></i> Generate Invoice</a>';

            $action .= '<a class="dropdown-item cloneQuoteBtn" href="' . route('quote.clone', $v->id) . '">'
                . '<i class="fa fa-clone m-r-5"></i> Clone</a>';
            $action .= '<a class="dropdown-item" href="' . route('quotes.download-quote', $v->id) . '">'
                . '<i class="fa fa-download"></i> Download</a>';
            $action .= '</div></div>';

            $statusLabel = '';
            if ($v->quote_payment_status == 'Open') {
                $statusLabel = '<span class="badge bg-inverse-warning">Open</span>';
            } elseif ($v->quote_payment_status == 'Approved') {
                $statusLabel = '<span class="badge bg-inverse-success">Approved</span>';
            } elseif ($v->quote_payment_status == 'Declined') {
                $statusLabel = '<span class="badge bg-inverse-danger">Declined</span>';
            }

            $activeLabel = $v->is_status == 1
                ? '<span class="btn btn-white btn-sm btn-rounded"><i class="fa fa-dot-circle-o text-success"></i> Active </span>'
                : '<span class="btn btn-white btn-sm btn-rounded"><i class="fa fa-dot-circle-o text-danger"></i> Inactive </span>';

            $checkbox = '<div class="checkbox"><input type="checkbox" class="custom-control-input cb-element" id="chk' . $v->id . '" value="' . $v->id . '"><label for="chk' . $v->id . '"></label></div>';

            $data[] = [
                'id' => $checkbox,
                'quote_number' => $v->quote_number,
                'client' => $v->client_business_name ?? '',
                'quote_date' => getFormatedDate($v->quote_date),
                'amount' => getPrice($v->quote_grand_total),
                'quote_status' => $statusLabel,
                'is_status' => $activeLabel,
                'action' => $action
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    public function addQuote($id = "")
    {
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
        return view('quotes.add', compact('data', 'clients', 'products', 'brands', 'payment_statuses', 'estimate_setting'));
    }

    public function addQuoteAction(Request $request)
    {
        $post_array = $request->post();
        $id = (isset($post_array['id']) && $post_array['id'] > 0) ? $post_array['id'] : 0;

        $action_id = $request->quote_payment_id;
        $all_quote_payment_id = Quote::with(['quote_payments'])->whereHas('quote_payments', function ($query) use ($request) {
            $query->where('quote_id', '=', $request->id);
        })->first();

        $quote_date = chnageDateFormat($post_array['quote_date']);

        $is_quote = Quote::latest()->first();
        if ($is_quote) {
            $quote_number = $is_quote->quote_number;
            $quote_code = str_pad($quote_number + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $quote_code = str_pad('1', 6, '0', STR_PAD_LEFT);
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

        if (!empty($request->hidden_prod_id[0])) {
            $hidden_prod_id = $request->hidden_prod_id;
            $count = count($hidden_prod_id);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->quote_payment_id[$i]) || !empty($request->quote_payment_id[$i])) {
                    $quotePayment_id = $request->quote_payment_id[$i];
                    $quote_payment = QuotePayment::find($quotePayment_id);
                } else {
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
        $generated_quote = Quote::with(['client', 'brand', 'quote_payments.product'])->whereId($quote->id)->first();
        $estimate_setting = EstimateSetting::first();
        $data = [
            'quote' => $generated_quote,
            'estimate_setting' => $estimate_setting
        ];
        $pdf = PDF::loadView('quotes.quote-pdf', $data);
        Storage::put('public/quotes/' . $quote->id . '/' . $quote->quote_number . '.pdf', $pdf->output());
        $details = [
            'userName' => $generated_quote->client->client_business_name,
            'quote_number' => $generated_quote->quote_number,
            'quote_date' => $generated_quote->quote_date,
            'quote_total' => $generated_quote->quote_grand_total,
            'file' => storage_path('app/public/quotes/' . $quote->id . '/' . $quote->quote_number . '.pdf'),
        ];

        $all_cc_emails_arr = [];
        if ($quote->quote_emails) {
            $temp_arr = explode(",", $quote->quote_emails);
            foreach ($temp_arr as $temp) {
                array_push($all_cc_emails_arr, trim($temp));
            }
            array_push($all_cc_emails_arr, config('app.cc_admin_email'));
        } else {
            $all_cc_emails_arr = [config('app.cc_admin_email')];
        }
        $clientEmailsArray = explode(',', $generated_quote->client->client_quotes_email);
        Mail::to($clientEmailsArray)->cc($all_cc_emails_arr)->send(new SendQuoteMail($details));

        $save_activity = [
            'email_sender' => config('app.from_email_address'),
            'email_receiver' => $generated_quote->client->client_quotes_email,
            // 'email_content' => 'Quote #' . $details['quote_number'] . ' from S & P Family Trust Trading as HDS',
            'email_content' => 'Quote #' . $details['quote_number'] . ' from ' . config('app.name'),
            'email_send_date' => Carbon::now()->format('Y-m-d H:i:s')
        ];
        $create_log = saveEmailActivity($save_activity);

        if (count(Mail::failures()) > 0) {
            return back()->with('danger', 'Error in sending email to client email address.');
        }

        if ($response) {
            if ($id == 0) {
                $message = "Quote added successfully.";
            } else if ($id > 0) {
                $message = "Quote updated successfully.";
            }
            $message_class = "success";
        } else {
            if ($id == 0) {
                $message = "Error in adding Quote. Please try again.";
            } else if ($id > 0) {
                $message = "Error in updating Quote. Please try again.";
            }
            $message_class = "danger";
        }

        return redirect()->route('quotes')->with($message_class, $message);
    }

    public function getProductDetails($id)
    {
        $product = Product::find($id);
        $response = [
            'success' => 1,
            'product' => $product
        ];

        return response()->json($response);
    }

    public function cloneQuote($id)
    {
        $quote = Quote::find($id);
        $latest_record = Quote::latest()->first();
        if ($latest_record) {
            $quote_number = $latest_record->quote_number;
            $quote_code = str_pad($quote_number + 1, 6, '0', STR_PAD_LEFT);
        }
        $quote_payments = QuotePayment::where('quote_id', $id)->get();

        $clone_quote = $quote->replicate();
        $clone_quote->quote_number = $quote_code;
        $clone_quote->quote_payment_status = 'Open';
        $clone_quote->created_at = Carbon::now();
        $clone_quote->save();

        foreach ($quote_payments as $quote_payment) {
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

    public function deleteSelectedQuoteRecords(Request $request)
    {
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

    public function deleteQuote($id)
    {
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

    public function generateQuotePDF($id)
    {
        $quote = Quote::with(['client', 'brand', 'quote_payments.product'])->whereId($id)->first();
        $estimate_setting = EstimateSetting::first();
        $data = [
            'quote' => $quote,
            'estimate_setting' => $estimate_setting

        ];
        $pdf = PDF::chunkLoadView('<html-separator/>', 'quotes.quote-pdf', $data);

        return $pdf->download('Quote-' . $quote->quote_number . '.pdf');
    }

    public function exportQuotes()
    {
        return Excel::download(new QuotesExport, 'quotes.xlsx');
    }

    public function exportQuotesByOptions(Request $request)
    {
        $params = $request->all();

        return Excel::download(new QuotesByFilterExport($params), 'quotes.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    // public function generateInvoice($id) {
    //     //echo $id; exit;
    //     // $quote = Quote::with(['client','brand','quote_payments.product'])->whereId($id)->first();
    //     $quote = Quote::whereId($id)->first();
    //     $estimate_setting = EstimateSetting::first();

    //     $post_array = $quote->toArray();


    //     //echo "<pre>"; print_r($post_array); exit;
    //     $id = (isset($post_array['id']) && $post_array['id'] > 0) ? $post_array['id'] : 0;


    //     //echo $post_array['quote_date']; exit;
    //     $invoice_date = chnageDateFormat($post_array['quote_date']);

    //     $date = new DateTime($invoice_date);
    //     $date->modify('+7 days');

    //     $invoice_due_date = $date->format('Y-m-d');


    //     $invoice_payment_date = null;
    //     $payment_status_id = getPaymentStatusId();


    //     $invoice_code = str_pad('1', 6, '0', STR_PAD_LEFT);

    //     $invoice = new Invoice;
    //     $invoice->invoice_number = $invoice_code;
    //     $invoice->invoice_date = $invoice_date;
    //     $invoice->invoice_due_date = $invoice_due_date;
    //     $invoice->invoice_payment_terms = '7';
    //     $invoice->invoice_method = 'send_via_email';
    //     $invoice->client_id = $post_array['client_id'];
    //     $invoice->brand_id = $post_array['brand_id'];
    //     $invoice->payment_status_id = $payment_status_id;
    //     $invoice->invoice_po_number = "123";
    //     // $invoice->invoice_notes = $post_array['invoice_notes'];
    //     // $invoice->invoice_emails = $post_array['invoice_emails'];
    //     $invoice->invoice_master_notes = $post_array['quote_master_notes'];
    //     $invoice->invoice_payment_date = $invoice_payment_date;
    //     $invoice->is_status = 1;
    //    // $invoice->project_id = $post_array['project_id'];
    //     $invoice->invoice_discount = $post_array['quote_discount'];
    //     $invoice->product_final_discount_amt = $post_array['product_final_discount_amt'];
    //     $invoice->invoice_item_total = $post_array['quote_item_total'];
    //     $invoice->invoice_grand_gst = $post_array['quote_grand_gst'];
    //     $invoice->invoice_grand_total = $post_array['quote_grand_total'];
    //     $invoice->invoice_round_off = $post_array['quote_round_off'];
    //     $response = $invoice->save();

    //     //QuotePayment
    //     $quote_payments = QuotePayment::whereId($id)->get();
    //     if (!empty($quote_payments)) {
    //         //$hidden_prod_id = $request->hidden_prod_id;
    //         $count = count($quote_payments);
    //         for ($i = 0; $i < $count; $i++) {

    //             $invoce_payment = new InvoicePayment();

    //             $invoce_payment->invoice_id = $invoice->id;
    //             $invoce_payment->product_id = $quote_payments[$i]->product_id;
    //             $invoce_payment->product_description = $quote_payments[$i]->product_description;
    //             $invoce_payment->product_unit_price = $quote_payments[$i]->product_unit_price;
    //             $invoce_payment->product_quantity = $quote_payments[$i]->product_quantity;
    //             $invoce_payment->tax_selection = $quote_payments[$i]->tax_selection;
    //             $invoce_payment->product_subtotal = $quote_payments[$i]->product_subtotal;
    //             $invoce_payment->product_gst = $quote_payments[$i]->product_gst;
    //             $invoce_payment->product_grand_total = $quote_payments[$i]->product_grand_total;
    //             $invoce_payment->save();
    //         }

    //     }


    //     if ($response) {
    //         //echo 1; exit;
    //         $message = "Invoice added successfully.";

    //         $send_email = sendEmailOfGeneratedInvoice($invoice);
    //         $message_class = "success";
    //     } else {
    //         if ($id == 0) {
    //             $message = "Error in adding Invoice. Please try again.";
    //         } else if ($id > 0) {
    //             $message = "Error in updating Invoice. Please try again.";
    //         }
    //         $message_class = "danger";
    //     }

    //     return redirect()->route('quotes')->with($message_class, $message);

    // }

    public function generateInvoice($id)
    {
        $quote = Quote::with(['client', 'brand', 'quote_payments.product'])->whereId($id)->first();

        if (!$quote) {
            return redirect()->route('quotes')->with('danger', 'Quote not found.');
        }

        // Pass quote data to invoice add form via session
        session(['generate_from_quote' => $quote]);

        return redirect()->route('invoices.add');
    }
}
