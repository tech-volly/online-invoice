<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\Quote;
use File;
use Session;

class BrandController extends Controller
{
    function __construct(){
        $this->middleware('permission:brand-list|brand-create|brand-edit|brand-delete', ['only' => ['index','show']]);
        $this->middleware('permission:brand-create', ['only' => ['addBrand','store']]);
        $this->middleware('permission:brand-edit', ['only' => ['addBrand','update']]);
        $this->middleware('permission:brand-delete', ['only' => ['deleteBrand']]);
    }

    public function index() {
        $data = Brand::orderBy('id','desc')->get();
        return view('masters.brands.index', compact('data'));
    }

    public function addBrand($id = "") {
        if ($id == "") {
            $data = new Brand;
        } else if ($id > 0) {
            $data = Brand::find($id);
        }

        return view('masters.brands.add', compact('data'));
    }
    
    public function addBrandAction(Request $request) {
        $post_array = $request->post();        
        $id = (isset($post_array['id']) && $post_array['id'] > 0) ? $post_array['id'] : 0;
        if($id == 0) {
            $brand = new Brand();
            $previous_file_name = '';
        }else if ($id > 0) {
            $brand = Brand::find($id);
            $previous_file_name = $brand->image;

        }
        $brand->name = $post_array['name'];
        $brand->is_status = $post_array['is_status'];      
    
        if ($request->file('image')) {
            $strtotime = date('YmdHis');
            $image = $request->file('image');
            $image_name = $image->getClientOriginalName();
            $image_name = strtolower(str_replace(" ", "", $image_name));
            $modified_file_name = $strtotime . '_' . $image_name;
            $brand->image = $modified_file_name;
        }
        $response = $brand->save();
        $brand_id = $brand->id;

        if ($request->file('image')) {
            $image->move(public_path('uploads/brands/' . $brand_id . '/'), $modified_file_name);
            if (is_file(public_path('uploads/brands/' . $brand_id . '/' . $previous_file_name))) {
                unlink(public_path('uploads/brands/' . $brand_id . '/' . $previous_file_name));
            }
        }

        if($response) {
            if($id == 0) {
                $message = "Brand added successfully.";
            }else if ($id > 0) {
                $message = "Brand updated successfully.";
            }
            $message_class = "success";
        }else {
            if ($id == 0) {
                $message = "Error in adding Brand. Please try again.";
            } else if ($id > 0) {
                $message = "Error in updating Brand. Please try again.";
            }
            $message_class = "danger";
        }

        return redirect()->route('brands')->with($message_class, $message);

    }

    public function deleteBrandLogo($id) {
        $brand = Brand::whereId($id)->first();
        unlink(public_path('uploads/brands/' . $id . '/' . $brand->image));
        $affected = Brand::whereId($id)->update(['image'=> '']);
        if ($affected) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function deleteBrand($id) {
        $brand = Brand::find($id);
        $is_brand = Invoice::whereBrandId($id)->first();
        if($is_brand) {
            $success = 2;
            $return['success'] = $success;
        }else {
            File::deleteDirectory(public_path('uploads/brands/' . $id));
            $response = $brand->delete();
            if ($response) {
                $success = 1;
            } else {
                $success = 0;
            }
            $return['success'] = $success;
        }


        return response()->json($return);
    }

    public function deleteSelectedBrandRecords(Request $request) {
        $post_array = $request->post();
        $is_brands = Invoice::whereIn('brand_id', $post_array['ids'])->get();
        $is_brand_subscription =  Subscription::whereIn('brand_id', $post_array['ids'])->get();
        $is_brand_quote = Quote::whereIn('brand_id', $post_array['ids'])->get();

        if(!empty($is_brands->toArray()) || !empty($is_brand_subscription->toArray()) || !empty($is_brand_quote->toArray())) {
            $success = 2;
            $return['success'] = $success;
            return response()->json($return);
        }

        $response = Brand::whereIn('id', $post_array['ids'])->delete();
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
