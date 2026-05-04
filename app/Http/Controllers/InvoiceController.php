<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Client;
use App\Models\Product;
use App\Models\Brand;
use App\Models\PaymentStatus;
use App\Models\InvoiceSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use App\Mail\SendInvoiceMail;
use App\Mail\CancelInvoiceMail;
use App\Exports\InvoiceExport;
use App\Exports\InvoiceByPaymentStatusExport;
use Excel;
use Session;
use Mail;
use PDF;
use File;
use App\Models\Project;

class InvoiceController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:invoice-list|invoice-create|invoice-edit|invoice-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:invoice-create', ['only' => ['addInvoice', 'store']]);
        $this->middleware('permission:invoice-edit', ['only' => ['addInvoice', 'update']]);
        $this->middleware('permission:invoice-delete', ['only' => ['deleteInvoice']]);
    }

    // public function index(Request $request)
    // {
    //     if ($request->get('client') || $request->get('payment_status') || $request->get('from_payment_date') || $request->get('to_payment_date')) {
    //         $where_query = "1";
    //         if (!empty($request->get('from_payment_date'))) {
    //             $from_payment_date = chnageDateFormat($request->get('from_payment_date'));
    //             $to_payment_date = chnageDateFormat($request->get('to_payment_date'));
    //             $where_query .= " AND (invoice_payment_date between '" . $from_payment_date . "' and '" . $to_payment_date . "' ";
    //             $where_query .= ')';
    //         }
    //         $where_has = "1";
    //         if ($request->get('client') && $request->get('client') != 'null') {

    //             $client = $request->get('client');
    //             $where_has .= " AND ( id = '" . $client . "' ";
    //             $where_has .= ')';
    //         }
    //         $where_has_payment = "1";
    //         if ($request->get('payment_status') && $request->get('payment_status') != 'null') {
    //             $payment_status = getPaymentStatusName($request->get('payment_status'));
    //             $where_has_payment .= " AND (name = '" . $payment_status . "' ";
    //             $where_has_payment .= ')';
    //         }
    //         $data = Invoice::with(['client', 'payment_status', 'project'])
    //             ->whereRaw($where_query)
    //             ->whereHas('client', function ($query) use ($where_has) {
    //                 $query->whereRaw($where_has);
    //             })->whereHas('payment_status', function ($q) use ($where_has_payment) {
    //                 $q->whereRaw($where_has_payment);
    //             })
    //             ->orderBy('id', 'desc')
    //             ->get();
    //     } else {
    //         $data = Invoice::with(['client', 'payment_status', 'project'])->orderBy('id', 'desc')->get();
    //         //    $data = Invoice::with(['client', 'payment_status'])
    //         //                 ->leftJoin('projects', 'invoices.project_id', '=', 'projects.id')
    //         //                 ->orderBy('invoices.id', 'desc')
    //         //                 ->select('invoices.*', 'projects.name as project_name')
    //         //                 ->get();
    //     }
    //     $payment_statuses = PaymentStatus::orderBy('name', 'asc')->whereIsStatus(1)->get();
    //     $clients = Client::orderBy('client_business_name', 'asc')->whereIsStatus(1)->get();
    //     $projects = Project::orderBy('name', 'asc')->whereIsStatus(1)->get();

    //     return view('invoices.index', compact('data', 'payment_statuses', 'clients', 'projects'));
    // }

    public function index()
    {
        $payment_statuses = PaymentStatus::orderBy('name')->whereIsStatus(1)->get();
        $clients = Client::orderBy('client_business_name')->whereIsStatus(1)->get();
        $projects = Project::orderBy('name')->whereIsStatus(1)->get();

        return view('invoices.index', compact('payment_statuses', 'clients', 'projects'));
    }
    public function getInvoices(Request $request)
    {

        $columns = [
            0 => 'id',
            1 => 'invoice_number',
            2 => 'client_id',
            3 => 'project_id',
            4 => 'invoice_date',
            5 => 'invoice_due_date',
            6 => 'invoice_grand_total',
            7 => 'payment_status_id',
            8 => 'invoice_payment_date',
            9 => 'is_status'
        ];

        $length = $request->length;
        $start = $request->start;
        $search = $request->search['value'];

        $query = Invoice::with(['client', 'payment_status', 'project']);

        /*
        FILTERS
        */

        if ($request->client) {
            $query->where('client_id', $request->client);
        }

        if ($request->payment_status) {
            $query->whereHas('payment_status', function ($q) use ($request) {
                $status = getPaymentStatusName($request->payment_status);
                $q->where('name', $status);
            });
        }

        if ($request->from_payment_date) {
            $from = chnageDateFormat($request->from_payment_date);
            $to   = chnageDateFormat($request->to_payment_date);

            $query->whereBetween('invoice_payment_date', [$from, $to]);
        }

        /*
        SEARCH
        */

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%$search%");
            });
        }

        $totalData = Invoice::count();
        $totalFiltered = $query->count();

        /*
        ORDER
        */

        if ($request->order) {
            $columnIndex = $request->order[0]['column'];
            $dir = $request->order[0]['dir'];

            $query->orderBy($columns[$columnIndex], $dir);
        } else {
            $query->orderBy('id', 'desc');
        }

        $invoices = $query->skip($start)->take($length)->get();

        $data = [];

        foreach ($invoices as $v) {

            /*
            CHECKBOX
            */

            $checkbox = '<div class="checkbox">
            <input type="checkbox" id="chk' . $v->id . '" class="custom-control-input cb-element" value="' . $v->id . '">
            <label for="chk' . $v->id . '"></label>
        </div>';

            /*
            PAYMENT STATUS
            */

            $paymentStatus = $v->payment_status->name ?? '';

            if ($paymentStatus == 'Paid') {
                $paymentStatusHtml = '<span class="badge bg-inverse-success">Paid</span>';
            } elseif ($paymentStatus == 'Hold') {
                $paymentStatusHtml = '<span class="badge bg-inverse-info">Hold</span>';
            } elseif ($paymentStatus == 'Unpaid') {
                $paymentStatusHtml = '<span class="badge bg-inverse-warning">Unpaid</span>';
            } elseif ($paymentStatus == 'Cancelled') {
                $paymentStatusHtml = '<span class="badge bg-inverse-danger">Cancelled</span>';
            } else {
                $paymentStatusHtml = $paymentStatus;
            }

            /*
            STATUS
            */

            $statusHtml = $v->is_status == 1
                ? '<span class="btn btn-white btn-sm btn-rounded"><i class="fa fa-dot-circle-o text-success"></i> Active</span>'
                : '<span class="btn btn-white btn-sm btn-rounded"><i class="fa fa-dot-circle-o text-danger"></i> Inactive</span>';

            /*
            ACTION
            */

            $action = '<div class="dropdown dropdown-action">
            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
            <i class="material-icons">more_vert</i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">';

            if ($paymentStatus != 'Paid') {
                if (auth()->user()->can('invoice-edit')) {
                    $action .= '<a class="dropdown-item" href="' . route('invoices.edit', $v->id) . '">
                <i class="fa fa-pencil m-r-5"></i> Edit</a>';
                }

                $action .= '<a class="dropdown-item" href="' . route('invoice.clone', $v->id) . '">
            <i class="fa fa-clone m-r-5"></i> Clone</a>';
            }

            if ($paymentStatus == 'Paid') {
                $action .= '<a class="dropdown-item" href="' . route('invoices.view', $v->id) . '">
            <i class="fa fa-eye m-r-5"></i> View</a>';
            }

            if (auth()->user()->can('invoice-delete')) {
                $action .= '<a class="dropdown-item deleteInvoiceBtn" data-id="' . $v->id . '" href="javascript:void(0)">
            <i class="fa fa-trash-o m-r-5"></i> Delete</a>';
            }

            $action .= '<a class="dropdown-item" href="' . route('invoices.download-invoice', $v->id) . '">
            <i class="fa fa-download"></i> Download</a>';


            if ($paymentStatus == 'Unpaid') {
                $action .= '<a class="dropdown-item cancelInvoiceBtn" href="javascript:void(0)" data-id="' . $v->id . '">
    <i class="fa fa-ban m-r-5"></i> Cancel Invoice</a>';
            }

            $data[] = [
                'checkbox' => $checkbox,
                'invoice_number' => $v->invoice_number,
                'client' => $v->client->client_business_name ?? '',
                'project' => $v->project->name ?? '',
                'invoice_date' => getFormatedDate($v->invoice_date),
                'invoice_due_date' => getFormatedDate($v->invoice_due_date),
                'invoice_grand_total' => getPrice($v->invoice_grand_total),
                'payment_status' => $paymentStatusHtml,
                'invoice_payment_date' => $v->invoice_payment_date ? getFormatedDate($v->invoice_payment_date) : '',
                'categories' => getInvoiceProductCategories($v->id),
                'status' => $statusHtml,
                'action' => $action
            ];
        }

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ]);
    }

    public function addInvoice($id = "")
    {
        $current_route =  Route::currentRouteName();
        if ($current_route == 'invoices.view') {
            $readonly = 'readonly';
        } else if ($current_route == 'invoices.edit') {
            $readonly = '';
        } else if ($current_route == 'invoices.add') {
            $readonly = '';
        }
        $clients = Client::orderBy('client_business_name', 'asc')->whereIsStatus(1)->get();
        $products = Product::orderBy('product_name', 'asc')->whereIsStatus(1)->get();
        $brands = Brand::orderBy('name', 'asc')->whereIsStatus(1)->get();
        $payment_statuses = PaymentStatus::orderBy('name', 'asc')->get();
        $invoice_setting = InvoiceSetting::first();
        $projects = Project::orderBy('name', 'asc')->whereIsStatus(1)->get();
        if ($id == "") {
            $data = new Invoice;
            // ── Pre-fill from quote if redirected from Generate Invoice ──
            $quoteData = session('generate_from_quote');
            if ($quoteData) {
                session()->forget('generate_from_quote');
                $data->client_id                 = $quoteData->client_id;
                $data->brand_id                  = $quoteData->brand_id;
                $data->invoice_discount          = $quoteData->quote_discount;
                $data->product_final_discount_amt = $quoteData->product_final_discount_amt;
                $data->invoice_item_total        = $quoteData->quote_item_total;
                $data->invoice_grand_gst         = $quoteData->quote_grand_gst;
                $data->invoice_grand_total       = $quoteData->quote_grand_total;
                $data->invoice_round_off         = $quoteData->quote_round_off;
                $data->invoice_master_notes      = $quoteData->quote_master_notes;
                $data->invoice_date              = now()->format('Y-m-d');
                $data->invoice_due_date          = now()->addDays(7)->format('Y-m-d');
                $data->invoice_payment_terms     = 7;
                // Attach quote payments so the blade can render line items
                $data->setRelation('invoice_payments', $quoteData->quote_payments->map(function ($qp) {
                    $ip = new InvoicePayment();
                    $ip->id                   = null; // new — no existing payment id
                    $ip->product_id           = $qp->product_id;
                    $ip->product_description  = $qp->product_description;
                    $ip->product_unit_price   = $qp->product_unit_price;
                    $ip->product_quantity     = $qp->product_quantity;
                    $ip->tax_selection        = $qp->tax_selection;
                    $ip->product_subtotal     = $qp->product_subtotal;
                    $ip->product_gst          = $qp->product_gst;
                    $ip->product_grand_total  = $qp->product_grand_total;
                    $ip->setRelation('product', $qp->product);
                    return $ip;
                }));
            } else {
                $data->setRelation('invoice_payments', collect());
            }
        } else if ($id > 0) {
            $data = Invoice::where('id', $id)->with(['invoice_payments.product'])->first();
        }
        return view('invoices.add', compact('data', 'clients', 'products', 'brands', 'payment_statuses', 'readonly', 'invoice_setting', 'projects'));
    }

    public function addInvoiceAction(Request $request)
    {
        $post_array = $request->post();
        $id = (isset($post_array['id']) && $post_array['id'] > 0) ? $post_array['id'] : 0;
        $action_id = $request->invoice_payment_id;
        $all_invoice_payment_id = Invoice::with(['invoice_payments'])->whereHas('invoice_payments', function ($query) use ($request) {
            $query->where('invoice_id', '=', $request->id);
        })->first();

        $invoice_date = chnageDateFormat($post_array['invoice_date']);
        $invoice_due_date = chnageDateFormat($post_array['invoice_due_date']);

        if ($post_array['invoice_payment_date']) {
            $invoice_payment_date = chnageDateFormat($post_array['invoice_payment_date']);
            $payment_status_id = getPaidPaymentStatusId();
        } else {
            $invoice_payment_date = null;
            $payment_status_id = getPaymentStatusId();
        }

        $is_invoice = Invoice::latest()->first();
        if ($is_invoice) {
            $invoice_number = $is_invoice->invoice_number;
            $invoice_code = str_pad($invoice_number + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $invoice_code = str_pad('1', 6, '0', STR_PAD_LEFT);
        }

        if ($id == 0) {
            $invoice = new Invoice;
        } else if ($id > 0) {
            $invoice = Invoice::where('id', $id)->first();
            $invoice_code = $invoice->invoice_number;
        }
        $invoice->invoice_number = $invoice_code;
        $invoice->invoice_date = $invoice_date;
        $invoice->invoice_due_date = $invoice_due_date;
        $invoice->invoice_payment_terms = $post_array['invoice_payment_terms'];
        $invoice->invoice_method = 'send_via_email';
        $invoice->client_id = $post_array['client_id'];
        $invoice->brand_id = $post_array['brand_id'];
        $invoice->payment_status_id = $payment_status_id;
        $invoice->invoice_po_number = $post_array['invoice_po_number'];
        $invoice->invoice_notes = $post_array['invoice_notes'];
        $invoice->invoice_emails = $post_array['invoice_emails'];
        $invoice->invoice_master_notes = $post_array['invoice_master_notes'];
        $invoice->invoice_payment_date = $invoice_payment_date;
        $invoice->is_status = 1;
        $invoice->project_id = $post_array['project_id'];
        $invoice->invoice_discount = $post_array['invoice_discount'];
        $invoice->product_final_discount_amt = $post_array['product_final_discount_amt'];
        $invoice->invoice_item_total = $post_array['invoice_grand_item_total'];
        $invoice->invoice_grand_gst = $post_array['invoice_grand_gst'];
        $invoice->invoice_grand_total = $post_array['product_final_total'];
        $invoice->invoice_round_off = $post_array['product_final_round_off'];
        $response = $invoice->save();

        if (!empty($request->hidden_prod_id[0])) {
            $hidden_prod_id = $request->hidden_prod_id;
            $count = count($hidden_prod_id);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->invoice_payment_id[$i]) || !empty($request->invoice_payment_id[$i])) {
                    $invoicePayment_id = $request->invoice_payment_id[$i];
                    $invoce_payment = InvoicePayment::find($invoicePayment_id);
                } else {
                    $invoce_payment = new InvoicePayment();
                }
                $invoce_payment->invoice_id = $invoice->id;
                $invoce_payment->product_id = $request->hidden_prod_id[$i];
                $invoce_payment->product_description = $request->hidden_product_description[$i];
                $invoce_payment->product_unit_price = $request->hidden_prod_unit_price[$i];
                $invoce_payment->product_quantity = $request->hidden_prod_quantity[$i];
                $invoce_payment->tax_selection = $request->hidden_prod_tax_sel[$i];
                $invoce_payment->product_subtotal = $request->hidden_product_subtotal[$i];
                $invoce_payment->product_gst = $request->hidden_prod_gst[$i];
                $invoce_payment->product_grand_total = $request->hidden_product_grand_total[$i];
                $invoce_payment->save();
            }
            if (!empty($all_invoice_payment_id)) {
                foreach ($all_invoice_payment_id->invoice_payments as $i_id) {
                    if (in_array($i_id->id, $action_id)) {
                    } else {
                        $delete = InvoicePayment::find($i_id->id);
                        $delete->delete();
                    }
                }
            }
        }
        if (empty($action_id) && !empty($request->another_id[0])) {
            foreach ($all_invoice_payment_id->invoice_payments as $i_id) {
                $delete = InvoicePayment::find($i_id->id);
                $delete->delete();
            }
        }

        if ($response) {
            if ($id == 0) {
                //Generate invoice pdf and send to client in email
                $send_email = sendEmailOfGeneratedInvoice($invoice);
                $message = "Invoice added successfully.";
            } else if ($id > 0) {
                if ($invoice->is_clonned == 1) {
                    $send_email = sendEmailOfGeneratedInvoice($invoice);
                }
                $invoice->is_clonned = 0;
                $invoice->save();
                $message = "Invoice updated successfully.";
            }
            $message_class = "success";
        } else {
            if ($id == 0) {
                $message = "Error in adding Invoice. Please try again.";
            } else if ($id > 0) {
                $message = "Error in updating Invoice. Please try again.";
            }
            $message_class = "danger";
        }

        return redirect()->route('invoices')->with($message_class, $message);
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

    public function cloneInvoice($id)
    {
        $invoice = Invoice::find($id);
        $payment_status_id = getPaymentStatusId();
        $latest_record = Invoice::latest()->first();
        if ($latest_record) {
            $invoice_number = $latest_record->invoice_number;
            $invoice_code = str_pad($invoice_number + 1, 6, '0', STR_PAD_LEFT);
        }
        $invoice_payments = InvoicePayment::where('invoice_id', $id)->get();

        $clone_invoice = $invoice->replicate();
        $clone_invoice->invoice_number = $invoice_code;
        $clone_invoice->payment_status_id = $payment_status_id;
        $clone_invoice->is_clonned = 1;
        $clone_invoice->invoice_payment_date = null;
        $clone_invoice->created_at = Carbon::now();
        $clone_invoice->save();

        foreach ($invoice_payments as $invoce_payment) {
            $clone_invoice_payment = new InvoicePayment();
            $clone_invoice_payment->invoice_id = $clone_invoice->id;
            $clone_invoice_payment->product_id = $invoce_payment->product_id;
            $clone_invoice_payment->product_description = $invoce_payment->product_description;
            $clone_invoice_payment->product_unit_price = $invoce_payment->product_unit_price;
            $clone_invoice_payment->product_quantity = $invoce_payment->product_quantity;
            $clone_invoice_payment->tax_selection = $invoce_payment->tax_selection;
            $clone_invoice_payment->product_subtotal = $invoce_payment->product_subtotal;
            $clone_invoice_payment->product_gst = $invoce_payment->product_gst;
            $clone_invoice_payment->product_grand_total = $invoce_payment->product_grand_total;
            $clone_invoice_payment->save();
        }

        return redirect()->route('invoices.edit', $clone_invoice->id)->with('success', 'Invoice clonned successfully.');
    }

    public function deleteSelectedInvoiceRecords(Request $request)
    {
        $post_array = $request->post();
        $response = Invoice::whereIn('id', $post_array['ids'])->delete();
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

    public function getInvoiceDueDate(Request $request)
    {
        $formatted_date = Carbon::create($request->invoice_date);
        $add_days = $formatted_date->addDays($request->payment_terms);
        $due_date = Carbon::createFromFormat('Y-m-d H:i:s', $add_days)->format('d-m-Y');
        $response = [
            'success' => 1,
            'due_date' => $due_date
        ];

        return response()->json($response);
    }

    public function deleteInvoice($id)
    {
        $invoice = Invoice::find($id);
        $response = $invoice->delete();
        if ($response) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function generateInvoicePDF($id)
    {
        $invoice = Invoice::with(['client', 'brand', 'invoice_payments.product'])->whereId($id)->first();
        $invoice_setting = InvoiceSetting::first();
        $data = [
            'invoice' => $invoice,
            'invoice_setting' => $invoice_setting

        ];
        $pdf = PDF::chunkLoadView('<html-separator/>', 'invoices.invoice-pdf', $data);

        return $pdf->download('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function changeInvoicePaymentStatus(Request $request)
    {
        $post_array = $request->post();
        $response = Invoice::whereIn('id', $post_array['ids'])->update([
            'payment_status_id' => $post_array['status']
        ]);
        $payment_status = PaymentStatus::whereId($post_array['status'])->first()->name;
        if ($payment_status == 'Paid') {
            $payment_date = chnageDateFormat($post_array['payment_date']);
            Invoice::whereIn('id', $post_array['ids'])->update([
                'invoice_payment_date' => $payment_date
            ]);
        } else {
            Invoice::whereIn('id', $post_array['ids'])->update([
                'invoice_payment_date' => null
            ]);
        }
        if ($response) {
            Session::flash('success', 'Payment status is changed for selected records.');
            $success = 1;
        } else {
            Session::flash('danger', 'Error in changing the status. Please try again.');
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    //code for edit project
    public function EditProject(Request $request)
    {
        $post_array = $request->post();
        $response = Invoice::where([
            'id' => $post_array['ids'],
        ])->update([
            'project_id' =>  $post_array['project_id']
        ]);
        if ($response) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }
    //end code here for edit project
    public function viewInvoiceNote($id)
    {
        $invoice = Invoice::find($id);
        if ($invoice) {
            $return = [
                'invoice_notes' => $invoice->invoice_notes,
                'success' => 1
            ];
        } else {
            $return = [
                'invoice_notes' => null,
                'success' => 0
            ];
        }

        return response()->json($return);
    }

    public function exportInvoices()
    {
        return Excel::download(new InvoiceExport, 'invoices.xlsx');
    }

    public function sendEmailToClient($id)
    {
        $invoice = Invoice::find($id);
        $send_email = sendEmailOfGeneratedInvoice($invoice);
        $return = [
            'invoice_notes' => 'Email send successfully.',
            'success' => $send_email
        ];

        return response()->json($return);
    }

    public function exportInvoiceByStatus(Request $request)
    {
        $params = $request->all();
        if ($request->get('payment_status') && $request->get('payment_status') != 'null') {
            $payment_status = getPaymentStatusName($params);
            $file_name = 'Invoices-' . $payment_status . '.xlsx';
        } else {
            $file_name = 'Invoices.xlsx';
        }

        return Excel::download(new InvoiceByPaymentStatusExport($params), $file_name, \Maatwebsite\Excel\Excel::XLSX);
    }

    //CODE FOR CANCLE INVOICE 6AR26
    public function cancelInvoice($id)
    {
        $invoice = Invoice::with(['client', 'payment_status'])->find($id);

        if (!$invoice) {
            return response()->json(['success' => 0, 'message' => 'Invoice not found.']);
        }

        // Get the "Cancelled" payment status id
        $cancelledStatus = PaymentStatus::where('name', 'Cancelled')->first();

        if (!$cancelledStatus) {
            return response()->json(['success' => 0, 'message' => 'Cancelled status not configured.']);
        }

        // Update invoice status to Cancelled
        $invoice->payment_status_id = $cancelledStatus->id;
        $invoice->invoice_payment_date = null;
        $response = $invoice->save();

        if ($response) {
            // Send cancellation email to client
            \Log::info('Mail config: ' . config('mail.default'));
            try {
                $clientEmails = $invoice->invoice_emails
                    ? explode(',', $invoice->invoice_emails)
                    : ($invoice->client->client_email ?? null);

                if ($clientEmails) {
                    $details = [
                        'userName' => $invoice->client->client_business_name,
                        'invoice_number' => $invoice->invoice_number,
                        'invoice_date' => $invoice->invoice_date,
                        'invoice_due_date' => $invoice->invoice_due_date,
                        'invoice_total' => $invoice->invoice_grand_total
                    ];
                    \Log::info('Sending email to: ' . json_encode($clientEmails));
                    Mail::to($clientEmails)->send(new CancelInvoiceMail($details));
                    \Log::info('Email sent successfully');
                } else {
                    \Log::info('No client emails found');
                }
                $emailSent = true;
            } catch (\Exception $e) {
                \Log::info('Email send failed: ' . $e->getMessage());
                $emailSent = false;
            }

            return response()->json([
                'success'    => 1,
                'message'    => 'Invoice cancelled successfully.'
            ]);
        }

        return response()->json(['success' => 0, 'message' => 'Error cancelling invoice.']);
    }
}
