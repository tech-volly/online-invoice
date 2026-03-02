<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OpeningBalance;
use Session;

class OpeningBalanceController extends Controller
{
    function __construct(){
        $this->middleware('permission:opening-balance-list|opening-balance-create|opening-balance-edit|opening-balance-delete', ['only' => ['index','show']]);
        $this->middleware('permission:opening-balance-create', ['only' => ['create','createOpeningBalance']]);
        $this->middleware('permission:opening-balance-edit', ['only' => ['edit','updateOpeningBalance']]);
        $this->middleware('permission:opening-balance-delete', ['only' => ['deleteOpeningBalance']]);
    }

    public function index() {
        $data = OpeningBalance::orderBy('id','desc')->get();
        
        return view('masters.opening-balance.index', compact('data'));
    }

    public function createOpeningBalance(Request $request) {
        $split_year = explode('-',$request->opening_balance_date);
        $opening_balance = new OpeningBalance;
        $opening_balance->opening_balance_date = $request->opening_balance_date;
        $opening_balance->opening_year = $split_year[0];
        $opening_balance->opening_balance_value = $request->opening_balance_value;    
        $opening_balance->save();
        
        return redirect()->route('opening-balance')->with('success','Opening Balance created successfully');
    }

    public function editOpeningBalance($id) {
        $opening_balance = OpeningBalance::find($id);
        $return = [
            'opening_balance' => $opening_balance,
            'success' => 1
        ];
       
        return response()->json($return);
    }

    public function updateOpeningBalance(Request $request) {
        $split_year = explode('-',$request->opening_balance_date);
        $opening_balance = OpeningBalance::find($request->opening_balance_id);
        $opening_balance->opening_balance_date = $request->opening_balance_date;
        $opening_balance->opening_year = $split_year[0];
        $opening_balance->opening_balance_value = $request->opening_balance_value;    
        $response = $opening_balance->save();

        if($response) {
            $message = "Opening Balance updated successfully.";
            $message_class = "success";
        }else {
            $message = "Error in updating Opening Balance. Please try again.";
            $message_class = "danger";
        }

        return redirect()->route('opening-balance')->with($message_class,$message);
    }

    public function deleteOpeningBalance($id){
        $opening_balance = OpeningBalance::find($id);
        $response = $opening_balance->delete();
        if ($response) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;
        
        return response()->json($return);
    }

    public function deleteSelectedOpeningBalanceRecords(Request $request) {
        $post_array = $request->post();
        $response = OpeningBalance::whereIn('id', $post_array['ids'])->delete();
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
