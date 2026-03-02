<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Imports\CategoryImport;
use Excel;
use Session;
use App\Models\Product;

class CategoryController extends Controller
{
    function __construct(){
        $this->middleware('permission:product-category-list|product-category-create|product-category-edit|product-category-delete', ['only' => ['index','show']]);
        $this->middleware('permission:product-category-create', ['only' => ['create','createCategory']]);
        $this->middleware('permission:product-category-edit', ['only' => ['edit','updateCategory']]);
        $this->middleware('permission:product-category-delete', ['only' => ['deleteCategory']]);
    }
    
    public function index() {
        $data = Category::orderBy('id','desc')->get();

        return view('masters.categories.index', compact('data'));
    }

    public function createCategory(Request $request) {
        $categories_arr = explode(",", $request->category_name);
        foreach($categories_arr as $category_name) {
            $category = new Category;
            $category->name = trim($category_name);
            $category->is_status = $request->is_status;
            $category->save();
        }
        
        return redirect()->route('categories')->with('success','Product Category created successfully');
    }

    public function editCategory($id) {
        $category = Category::find($id);
        $return = [
            'category' => $category,
            'success' => 1
        ];
       
        return response()->json($return);
    }

    public function updateCategory(Request $request) {
        $category = Category::find($request->category_id);
        $category->name = $request->category_name;
        $category->is_status = $request->is_status;
        $response = $category->save();
        if($response) {
            $message = "Product category updated successfully.";
            $message_class = "success";
        }else {
            $message = "Error in updating Product category. Please try again.";
            $message_class = "danger";
        }

        return redirect()->route('categories')->with($message_class,$message);
    }

    public function deleteCategory($id){
        $category = Category::find($id);
        $is_category = Product::whereCategoryId($id)->first();
        
        if($is_category) {
            $success = 2;
            $return['success'] = $success;
        }else {
            $response = $category->delete();
            if ($response) {
                $success = 1;
            } else {
                $success = 0;
            }
            $return['success'] = $success;
        }

        return response()->json($return);
    }

    public function deleteSelectedProductCategoryRecords(Request $request) {
        $post_array = $request->post();
        $is_categories = Product::whereIn('category_id', $post_array['ids'])->get();
        if(!empty($is_categories->toArray())) {
            $success = 2;
            $return['success'] = $success;
            return response()->json($return);
        }
        
        $response = Category::whereIn('id', $post_array['ids'])->delete();
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

    public function importProductCategories(Request $request) {
        Excel::import(new CategoryImport, request()->file('import_categories_file'));

        return redirect()->route('categories')->with('success','Product Categories are imported successfully');
    }
}
