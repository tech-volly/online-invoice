<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\Expense;
use App\Imports\PaymentMethodsImport;
use Excel;
use Session;

class PaymentMethodController extends Controller
{
    function __construct(){
        $this->middleware('permission:payment-method-list|payment-method-create|payment-method-edit|payment-method-delete', ['only' => ['index','show']]);
        $this->middleware('permission:payment-method-create', ['only' => ['createPaymentMethod','store']]);
        $this->middleware('permission:payment-method-edit', ['only' => ['editPaymentMethod','update']]);
        $this->middleware('permission:payment-method-delete', ['only' => ['deletePaymentMethod']]);
    }

    public function index() {
        $data = PaymentMethod::orderBy('id','desc')->get();

        return view('masters.payment-methods.index', compact('data'));
    }

    public function createPaymentMethod(Request $request) {        
        $payment_method_arr = explode(",", $request->payment_method_name);
        foreach($payment_method_arr as $payment_method_name) {
            $payment_method = new PaymentMethod;
            $payment_method->payment_method_name = trim($payment_method_name);
            $payment_method->is_status = $request->is_status;
            $response = $payment_method->save();            
        }

        if($response) {
            $message = "Payment Method added successfully.";
            $message_class = "success";
        }else {
            $message = "Error in adding Payment Method. Please try again.";
            $message_class = "danger";
        }
        return redirect()->route('payment-methods')->with($message_class, $message);
    }

    public function editPaymentMethod($id) {
        $payment_method = PaymentMethod::find($id);
        $return = [
            'payment_method' => $payment_method,
            'success' => 1
        ];
       
        return response()->json($return);
    }

    public function updatePaymentMethod(Request $request) {
        $payment_method = PaymentMethod::find($request->payment_method_id);
        $payment_method->payment_method_name = $request->payment_method_name;
        $payment_method->is_status = $request->is_status;
        $response = $payment_method->save();
        if($response) {
            $message = "Payment Method updated successfully.";
            $message_class = "success";
        }else {
            $message = "Error in updating Payment Method. Please try again.";
            $message_class = "danger";
        }

        return redirect()->route('payment-methods')->with($message_class,$message);
    }

    public function deletePaymentMethod($id) {
        $payment_method = PaymentMethod::find($id);
        $is_payment_method = Expense::wherePaymentMethodId($id)->first();

        if($is_payment_method) {
            $success = 2;
            $return['success'] = $success;
        }else {
            $response = $payment_method->delete();
            if ($response) {
                $success = 1;
            } else {
                $success = 0;
            }
            $return['success'] = $success;
        }

        return response()->json($return);
    }

    public function deleteSelectedPaymentMethodRecords(Request $request) {
        $post_array = $request->post();

        $is_payment_methods = Expense::whereIn('payment_method_id', $post_array['ids'])->get();
        if(!empty($is_payment_methods->toArray())) {
            $success = 2;
            $return['success'] = $success;
            return response()->json($return);
        }


        $response = PaymentMethod::whereIn('id', $post_array['ids'])->delete();
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

    public function importPaymetMethods(Request $request) {
        Excel::import(new PaymentMethodsImport, request()->file('import_payment_method_file'));

        return redirect()->route('payment-methods')->with('success','Payment Methods are imported successfully');
    }
}
