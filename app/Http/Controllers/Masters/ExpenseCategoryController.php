<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Models\Supplier;
use App\Imports\ExpenseCategoryImport;
use Session;
use Excel;
use DB;

class ExpenseCategoryController extends Controller
{
    function __construct(){
        $this->middleware('permission:expense-category-list|expense-category-create|expense-category-edit|expense-category-delete', ['only' => ['index','show']]);
        $this->middleware('permission:expense-category-create', ['only' => ['create','createCategory']]);
        $this->middleware('permission:expense-category-edit', ['only' => ['edit','updateCategory']]);
        $this->middleware('permission:expense-category-delete', ['only' => ['deleteCategory']]);
    }

    public function index() {
        $data = ExpenseCategory::orderBy('id','desc')->get();

        return view('masters.expense-categories.index', compact('data'));
    }

    public function createCategory(Request $request) {
        $categories_arr = explode(",", $request->category_name);
        foreach($categories_arr as $category_name) {
            $category = new ExpenseCategory;
            $category->name = trim($category_name);
            $category->is_status = $request->is_status;
            $category->save();
        }
       

        return redirect()->route('expense.categories')->with('success','Category created successfully');
    }

    public function editCategory($id) {
        $category = ExpenseCategory::find($id);
        $return = [
            'category' => $category,
            'success' => 1
        ];
       
        return response()->json($return);
    }

    public function updateCategory(Request $request) {
        $category = ExpenseCategory::find($request->category_id);
        $category->name = $request->category_name;
        $category->is_status = $request->is_status;
        $response = $category->save();
        if($response) {
            $message = "Category updated successfully.";
            $message_class = "success";
        }else {
            $message = "Error in updating Category. Please try again.";
            $message_class = "danger";
        }

        return redirect()->route('expense.categories')->with($message_class,$message);
    }

    public function deleteCategory($id){
        $category = ExpenseCategory::find($id);
        $is_category = DB::table('suppliers')->whereRaw('FIND_IN_SET(?, supplier_expense_category)', [$id])->get();
        if(count($is_category) > 0) {
            $success = 2;
            $return['success'] = $success;
            return response()->json($return);
        }
        
        $response = $category->delete();
        if ($response) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function deleteSelectedExpenseCategoryRecords(Request $request) {
        $post_array = $request->post();
        $response = ExpenseCategory::whereIn('id', $post_array['ids'])->delete();
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

    public function importExpenseCategories(Request $request) {
        Excel::import(new ExpenseCategoryImport, request()->file('import_categories_file'));

        return redirect()->route('expense.categories')->with('success','Expense Categories are imported successfully');
    }
}
