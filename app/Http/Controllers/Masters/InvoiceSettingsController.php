<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoiceSetting;

class InvoiceSettingsController extends Controller
{
    function __construct(){
        $this->middleware('permission:invoice-setting-list', ['only' => ['index']]);
    }

    public function index() {
        $data = InvoiceSetting::first();
        return view('masters.invoice-settings.index', compact('data'));
    }

    public function saveInvoiceSettings(Request $request) {
        $post_array = $request->post();        
        $id = (isset($post_array['id']) && $post_array['id'] > 0) ? $post_array['id'] : 0;
        if($id == 0) {
            $invoice_setting = new InvoiceSetting();
        }else if ($id > 0) {
            $invoice_setting = InvoiceSetting::find($id);
        }
        $invoice_setting->invoice_header_company_name = $post_array['invoice_header_company_name'];
        $invoice_setting->invoice_header_number = $post_array['invoice_header_number'];
        $invoice_setting->invoice_header_street_address = $post_array['invoice_header_street_address'];
        $invoice_setting->invoice_header_city = $post_array['invoice_header_city'];
        $invoice_setting->invoice_header_state = $post_array['invoice_header_state'];
        $invoice_setting->invoice_header_postalcode = $post_array['invoice_header_postalcode'];
        $invoice_setting->invoice_header_tollfree = $post_array['invoice_header_tollfree'];
        $invoice_setting->invoice_header_email = $post_array['invoice_header_email'];
        $invoice_setting->invoice_header_website = $post_array['invoice_header_website'];
        $invoice_setting->invoice_footer_company_name = $post_array['invoice_footer_company_name'];
        $invoice_setting->invoice_footer_bsb_number = $post_array['invoice_footer_bsb_number'];
        $invoice_setting->invoice_footer_acc_number = $post_array['invoice_footer_acc_number'];
        $invoice_setting->invoice_footer_email = $post_array['invoice_footer_email'];
        $invoice_setting->invoice_footer_notes = $post_array['invoice_footer_notes'];
        $response = $invoice_setting->save();

        if($response) {
            if($id == 0) {
                $message = "Invoice Settings are added successfully.";
            }else if ($id > 0) {
                $message = "Invoice Settings are updated successfully.";
            }
            $message_class = "success";
        }else {
            if ($id == 0) {
                $message = "Error in adding Invoice Settings. Please try again.";
            } else if ($id > 0) {
                $message = "Error in updating Invoice Settings. Please try again.";
            }
            $message_class = "danger";
        }

        return redirect()->route('invoice-settings')->with($message_class, $message);
    }
}
