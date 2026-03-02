<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentStatus;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\Quote;
use Session;

class PaymentStatusController extends Controller
{
    function __construct(){
        $this->middleware('permission:payment-status-list|payment-status-create|payment-status-edit|payment-status-delete', ['only' => ['index','show']]);
        $this->middleware('permission:payment-status-create', ['only' => ['createPaymentStatus','store']]);
        $this->middleware('permission:payment-status-edit', ['only' => ['editPaymentStatus','update']]);
        $this->middleware('permission:payment-status-delete', ['only' => ['deletePaymentStatus']]);
    }

    public function index() {
        $data = PaymentStatus::orderBy('id','desc')->get();

        return view('masters.payment-status.index', compact('data'));
    }

    public function createPaymentStatus(Request $request) {        
        $payment_status_arr = explode(",", $request->name);
        foreach($payment_status_arr as $name) {
            $payment_status = new PaymentStatus;
            $payment_status->name = trim($name);
            $payment_status->is_status = $request->is_status;
            $response = $payment_status->save();            
        }

        if($response) {
            $message = "Payment Status added successfully.";
            $message_class = "success";
        }else {
            $message = "Error in adding Payment Status. Please try again.";
            $message_class = "danger";
        }
        return redirect()->route('payment-statuses')->with($message_class, $message);
    }

    public function editPaymentStatus($id) {
        $payment_status = PaymentStatus::find($id);
        $return = [
            'payment_status' => $payment_status,
            'success' => 1
        ];
       
        return response()->json($return);
    }

    public function updatePaymentStatus(Request $request) {
        $payment_status = PaymentStatus::find($request->payment_status_id);
        $payment_status->name = $request->payment_status_name;
        $payment_status->is_status = $request->is_status;
        $response = $payment_status->save();
        if($response) {
            $message = "Payment Status updated successfully.";
            $message_class = "success";
        }else {
            $message = "Error in updating Payment Status. Please try again.";
            $message_class = "danger";
        }

        return redirect()->route('payment-statuses')->with($message_class,$message);
    }

    public function deletePaymentStatus($id) {
        $payment_status = PaymentStatus::find($id);
        $is_payment_status = Invoice::wherePaymentStatusId($id)->first();
        
        if($is_payment_status) {
            $success = 2;
            $return['success'] = $success;
        }else {
            $response = $payment_status->delete();
            if ($response) {
                $success = 1;
            } else {
                $success = 0;
            }
            $return['success'] = $success;
        }

        return response()->json($return);
    }

    public function deleteSelectedPaymentStatusRecords(Request $request) {
        $post_array = $request->post();

        $is_payment_statuses = Invoice::whereIn('payment_status_id', $post_array['ids'])->get();
        $is_payment_statuses_subscription =  Subscription::whereIn('payment_status_id', $post_array['ids'])->get();
        $is_payment_statuses_quote = Quote::whereIn('payment_status_id', $post_array['ids'])->get();

        if(!empty($is_payment_statuses->toArray()) || !empty($is_payment_statuses_subscription->toArray()) || !empty($is_payment_statuses_quote->toArray()) ) {
            $success = 2;
            $return['success'] = $success;
            return response()->json($return);
        }


        $response = PaymentStatus::whereIn('id', $post_array['ids'])->delete();
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

}
