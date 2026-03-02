<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Client;
use App\Models\PaymentMethod;
use App\Models\Supplier;
use App\Models\ExpenseCategory;
use App\Exports\ExpensesExport;
use App\Exports\SampleExpenseExport;
use App\Imports\ExpensesImport;
use Excel;
use File;
use Session;
use DB;
use App\Models\Project;

class ExpenseController extends Controller
{
    function __construct() {
        $this->middleware('permission:expense-list|expense-create|expense-edit|expense-delete', ['only' => ['index','store']]);
        $this->middleware('permission:expense-create', ['only' => ['addExpense','store']]);
        $this->middleware('permission:expense-edit', ['only' => ['addExpense','update']]);
        $this->middleware('permission:expense-delete', ['only' => ['deleteExpense']]);
    }

    public function index(){
        //$data = Expense::with(['supplier'])->orderBy('id', 'desc')->get();
        $data = Expense::with(['supplier'])
                        ->leftJoin('projects', 'expenses.project_id', '=', 'projects.id')
                        ->orderBy('expenses.id', 'desc')
                        ->select('expenses.*', 'projects.name as project_name')
                        ->get();
        
        return view('expenses.index', compact('data'));
    }

    public function addExpense($id = ""){
        $payment_methods = PaymentMethod::orderBy('payment_method_name', 'asc')->whereIsStatus(1)->get();
        $suppliers = Supplier::orderBy('supplier_business_name', 'asc')->whereIsStatus(1)->get();

        $categories = Category::orderBy('name', 'asc')->whereIsStatus(1)->get();
        $projects = Project::orderBy('name', 'asc')->whereIsStatus(1)->get();
        if ($id == "") {
            $data = new Expense;
        } else if ($id > 0) {
            $data = Expense::find($id);
        }

        return view('expenses.add', compact('data', 'categories', 'payment_methods', 'suppliers','projects'));
    }

    public function addExpenseAction(Request $request) {
        $post_array = $request->post();        
        $id = (isset($post_array['id']) && $post_array['id'] > 0) ? $post_array['id'] : 0;
        if($id == 0) {
            $expense = new Expense();
            $previous_file_name = '';
        }else if ($id > 0) {
            $expense = Expense::find($id);
            $previous_file_name = $expense->expense_attached_receipt;

        }
        $expense_date = chnageDateFormat($post_array['expense_date']);
        $expense->supplier_invoice_number = $post_array['supplier_invoice_number'];
        $expense->payment_method_id = $post_array['payment_method_id'];
        $expense->supplier_id = $post_array['supplier_id'];
        $expense->supplier_expense_category = $post_array['supplier_expense_category'];
        $expense->expense_tax = $post_array['expense_tax'];
        $expense->expense_amount = $post_array['expense_amount'];
        $expense->expense_date = $expense_date;
        $expense->expense_description = $post_array['expense_description'];
        $expense->is_status = 1; 
        $expense->project_id=$post_array['project_id'];
    
        if ($request->file('expense_attached_receipt')) {
            $strtotime = date('YmdHis');
            $image = $request->file('expense_attached_receipt');
            $image_name = $image->getClientOriginalName();
            $image_name = strtolower(str_replace(" ", "", $image_name));
            $modified_file_name = $strtotime . '_' . $image_name;
            $expense->expense_attached_receipt = $modified_file_name;
        }
        $response = $expense->save();
        $expense_id = $expense->id;

        if ($request->file('expense_attached_receipt')) {
            $image->move(public_path('uploads/expenses/' . $expense_id . '/'), $modified_file_name);
            if (is_file(public_path('uploads/expenses/' . $expense_id . '/' . $previous_file_name))) {
                unlink(public_path('uploads/expenses/' . $expense_id . '/' . $previous_file_name));
            }
        }

        if($response) {
            if($id == 0) {
                $message = "Expense added successfully.";
            }else if ($id > 0) {
                $message = "Expense updated successfully.";
            }
            $message_class = "success";
        }else {
            if ($id == 0) {
                $message = "Error in adding Expense. Please try again.";
            } else if ($id > 0) {
                $message = "Error in updating Expense. Please try again.";
            }
            $message_class = "danger";
        }

        return redirect()->route('expenses')->with($message_class, $message);

    }

    public function deleteExpense($id) {
        $expense = Expense::find($id);
        File::deleteDirectory(public_path('uploads/expenses/' . $id));
        // unlink(public_path('uploads/expenses/' . $id . '/' . $expense->expense_attached_receipt));
        $response = $expense->delete();
        if ($response) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function getSupplierExpenseCategories($supplier_id) {
        $supplier = Supplier::whereId($supplier_id)->first();
        $supplier_categories_arr = explode(',',$supplier->supplier_expense_category);
        $expense_categories = ExpenseCategory::whereIn('id', $supplier_categories_arr)->whereIsStatus(1)->orderBy('name','asc')->get();
        if($expense_categories) {
            $response = [
                'success' => 1,
                'expense_categories' =>$expense_categories
            ];
        }else {
            $response = [
                'success' => 0,
                'expense_categories' => null
            ];
        }
    
        return response()->json($response);
    }

    public function deleteAttachedImage($id) {
        $expense = Expense::whereId($id)->first();
        unlink(public_path('uploads/expenses/' . $id . '/' . $expense->expense_attached_receipt));
        $affected = Expense::whereId($id)->update(['expense_attached_receipt'=> '']);
        if ($affected) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function deleteSelectedExpenseRecords(Request $request) {
        $post_array = $request->post();
        $response = Expense::whereIn('id', $post_array['ids'])->delete();
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

    public function exportExpenses() {
        return Excel::download(new ExpensesExport, 'expenses.xlsx');
    }

    public function exportSampleExpenseFile() {
        return Excel::download(new SampleExpenseExport, 'sample-expense.xlsx');
    }

    public function importExpenses(Request $request) {
        $import = new ExpensesImport;
        Excel::import($import, request()->file('import_expense_file'));
        $rows_data = $import->data->toArray();
        $invalid_temp_arr = [];
        $valid_arr = [];

        foreach($rows_data as $key=>$data) {
            $data['row_id'] = $key+2;
            
            // Remove empty rows
            if($data['supplier_invoice_number'] == '' && $data['select_payment_method'] == '' && $data['select_supplier'] == '' && 
            $data['select_supplier_category'] == '' && $data['select_tax'] == '' && $data['amount'] == '' &&
            $data['payment_date'] == '' && $data['description'] == '' ) { 
                break;
            }

            // Remove invalid rows
            if($data['supplier_invoice_number'] == '' || $data['select_payment_method'] == '' || $data['select_supplier'] == '' || 
            $data['select_supplier_category'] == '' || $data['select_tax'] == '' || $data['amount'] == '' ||
            $data['payment_date'] == '' || $data['description'] == '' ) {
                array_push($invalid_temp_arr, $data);           
            }

            // Check supplier has correct expense categor 
            if($data['select_supplier']) {
                $supplier = Supplier::whereSupplierBusinessName($data['select_supplier'])->first();
                if($supplier) {
                    $supplier_id = $supplier->id;
                    $expense_category = ExpenseCategory::whereName($data['select_supplier_category'])->first();
                    $expense_category_id = $expense_category ? $expense_category->id : 0;
        
                    $check_supplier_category = DB::table("suppliers")
                        ->where('id', '=', $supplier_id)
                        ->whereRaw('FIND_IN_SET(?, 	supplier_expense_category)', [$expense_category_id])
                        ->first();
            
                    if(!$check_supplier_category) {
                        array_push($invalid_temp_arr, $data);
                    }
                }
            }

            if($data['select_payment_method']) {
                $payment_method = PaymentMethod::wherePaymentMethodName($data['select_payment_method'])->first();
                $payment_method_id = $payment_method ? $payment_method->id : 0;
            }

            if($data['supplier_invoice_number'] != '' && $data['select_payment_method'] != '' && $data['select_supplier'] != '' && 
            $data['select_supplier_category'] != '' && $data['select_tax'] != '' && $data['amount'] != '' &&
            $data['payment_date'] != '' && $data['description'] != '' && isset($check_supplier_category) ) { 
                array_push($valid_arr, $data);
            }
        }
        $invalid_arr =  array_map("unserialize", array_unique(array_map("serialize", $invalid_temp_arr)));
        if(count($invalid_arr) > 0) {
            return view('expenses.invalid-import-data', compact('invalid_arr'));
        }else {
            foreach($valid_arr as $index => $data) {
                $payment_method = PaymentMethod::wherePaymentMethodName($data['select_payment_method'])->first();
                $payment_method_id = $payment_method ? $payment_method->id : 0;

                $supplier = Supplier::whereSupplierBusinessName($data['select_supplier'])->first();
                $supplier_id = $supplier->id;

                $project = Project::where('name', $data['project_name'])->first();
                $project_id = $project->id;
                
                $existingExpense = Expense::where('supplier_invoice_number', $data['supplier_invoice_number'])
                ->where('payment_method_id',  $payment_method_id)
                ->where('supplier_id',  $supplier_id)
                ->where('project_id',  $project_id)
                ->where('supplier_expense_category', getExpenseCategoryId($data['select_supplier_category']))
                ->where('expense_tax', $data['select_tax'])
                ->where('expense_amount', $data['amount'])
                ->where('expense_date', $data['payment_date'])
                ->where('expense_description', $data['description'])
                ->first();

                if($existingExpense){
                 $errors[]=($index + 2)   ;
                }else{
                    $expense = new Expense();
                    $expense_date = $data['payment_date'];
                    $expense->supplier_invoice_number = $data['supplier_invoice_number'];
                    $expense->payment_method_id = $payment_method_id;
                    $expense->supplier_id = $supplier_id;
                    $expense->project_id = $project_id;
                    $expense->supplier_expense_category = getExpenseCategoryId($data['select_supplier_category']);
                    $expense->expense_tax = $data['select_tax'];
                    $expense->expense_amount = $data['amount'];
                    $expense->expense_date = $expense_date;
                    $expense->expense_description = $data['description'];
                    $expense->is_status = 1;      
                    $expense->save();
                }
            }
            if(sizeof($errors)>0){
                $errorString = implode(', ', $errors);
                return redirect()->route('expenses')->with('danger', 'Duplicate entry found in row '.$errorString);
    
            }else{
                return redirect()->route('expenses')->with('success','Expenses are imported successfully');
            }
        }
    }
}
