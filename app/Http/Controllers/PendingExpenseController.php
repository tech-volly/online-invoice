<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PendingExpense;
use App\Models\Supplier;
use App\Models\Expense;
use App\Imports\PendingExpenseImport;
use Excel;

class PendingExpenseController extends Controller
{
    public function index() {
        $data = PendingExpense::orderBy('id','desc')->get();
        $suppliers = Supplier::orderBy('supplier_business_name', 'asc')->whereIsStatus(1)->get();
        
        return view('pending-expense.index', compact('data', 'suppliers'));
    }

    public function importPendingExpense(Request $request) {
        Excel::import(new PendingExpenseImport, request()->file('import_pending_expense_file'));

        return redirect()->route('pending-expense')->with('success','Pending expenses are imported successfully');
    }

    public function confirmPendingExpense(Request $request) {
        $post_array = $request->post();
        $expense = new Expense();
        $expense->supplier_invoice_number = '0'; 
        $expense->payment_method_id = 5;
        $expense->supplier_id = $post_array['supplier_id'];
        $expense->supplier_expense_category = $post_array['exp_category'];
        $expense->expense_tax = 'GST Inclusive';
        $expense->expense_amount = $post_array['amount'];
        $expense->expense_date = $post_array['expense_date'];
        $expense->expense_description = $post_array['description'];
        $expense->is_status = 1;   
        $response = $expense->save();
        if($response) {    
            $pending_expense = PendingExpense::find($post_array['pending_expense_id']);
            $pending_expense->delete();
            $success = 1;
        }else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

}
