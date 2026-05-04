<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\Quote;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;
use Excel;
use File;
use Session;

class ProductController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:product-create', ['only' => ['addProduct', 'store']]);
        $this->middleware('permission:product-edit', ['only' => ['addProduct', 'update']]);
        $this->middleware('permission:product-delete', ['only' => ['deleteProduct']]);
    }

    public function index()
    {
        //$data = Product::orderBy('id','desc')->get();

        //return view('products.index', compact('data'));
        return view('products.index');
    }

    public function getProducts(Request $request)
    {
        $columns = [
            0 => 'products.id',
            1 => 'products.product_image',
            2 => 'products.product_name',
            3 => 'products.category_id',
            4 => 'products.product_price',
            5 => 'products.product_purchase_price',
            6 => 'products.product_margin',
            7 => 'products.product_tax',
            8 => 'products.is_status',
        ];

        $length = $request->input('length');
        $start  = $request->input('start');
        $search = $request->input('search.value');

        $query = Product::select('products.*');

        $totalData = $query->count();

        // 🔍 SEARCH
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('products.product_name', 'LIKE', "%{$search}%")
                    ->orWhere('products.product_price', 'LIKE', "%{$search}%")
                    ->orWhere('products.product_tax', 'LIKE', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();

        // 📌 ORDERING
        if ($request->has('order')) {
            $orderColumnIndex = $request->input('order.0.column');
            $orderDir         = $request->input('order.0.dir');

            if (isset($columns[$orderColumnIndex])) {
                $query->orderBy($columns[$orderColumnIndex], $orderDir);
            }
        } else {
            $query->orderBy('products.id', 'desc');
        }

        $products = $query->skip($start)->take($length)->get();

        $data = [];

        foreach ($products as $v) {
            $defaultImage = asset('public/assets/img/profiles/avatar-01.jpg');

            if (!empty($v->product_image)) {
                $imagePath = asset('public/uploads/products/' . $v->id . '/' . $v->product_image);
            } else {
                $imagePath = $defaultImage;
            }

            $imageHtml = '
            <h2 class="table-avatar">
                <a href="' . $imagePath . '" class="avatar brand-custom image-link">
                    <img src="' . $imagePath . '" style="height:45px;width:45px;object-fit:cover;">
                </a>
            </h2>';

            $statusHtml = $v->is_status == 1
                ? '<span class="btn btn-white btn-sm btn-rounded"><i class="fa fa-dot-circle-o text-success"></i> Active</span>'
                : '<span class="btn btn-white btn-sm btn-rounded"><i class="fa fa-dot-circle-o text-danger"></i> Inactive</span>';

            $checkboxHtml = '
            <div class="checkbox">
                <input type="checkbox" id="chk' . $v->id . '" class="custom-control-input cb-element" value="' . $v->id . '">
                <label for="chk' . $v->id . '"></label>
            </div>';

            $action = '<div class="dropdown dropdown-action">
            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="material-icons">more_vert</i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">';

            if (auth()->user()->can('product-edit')) {
                $action .= '<a class="dropdown-item" href="' . route('products.edit', $v->id) . '">
                            <i class="fa fa-pencil m-r-5"></i> Edit
                        </a>';
            }

            if (auth()->user()->can('product-delete')) {
                $action .= '<a class="dropdown-item deleteProductBtn" 
                            href="javascript:void(0)" 
                            data-id="' . $v->id . '">
                            <i class="fa fa-trash-o m-r-5"></i> Delete
                        </a>';
            }

            $action .= '</div></div>';

            $data[] = [
                'checkbox'               => $checkboxHtml,
                'image'                  => $imageHtml,
                'product_name'           => $v->product_name,
                'category'               => categoryName($v->category_id),
                'product_price'          => getPrice($v->product_price),
                'product_purchase_price' => getPrice($v->product_purchase_price),
                'product_margin'         => getPrice($v->product_margin),
                'product_tax'            => $v->product_tax,
                'status'                 => $statusHtml,
                'action'                 => $action,
            ];
        }

        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        ]);
    }


    public function addProduct($id = "")
    {
        $categories = Category::orderBy('name', 'asc')->whereIsStatus(1)->get();
        if ($id == "") {
            $data = new Product;
        } else if ($id > 0) {
            $data = Product::find($id);
        }

        return view('products.add', compact('categories', 'data'));
    }

    public function addProductAction(Request $request)
    {
        $post_array = $request->post();
        $id = (isset($post_array['id']) && $post_array['id'] > 0) ? $post_array['id'] : 0;
        if ($id == 0) {
            $product = new Product();
            $previous_file_name = '';
        } else if ($id > 0) {
            $product = Product::find($id);
            $previous_file_name = $product->product_image;
        }

        $product_price_res = getCalculatedPrice($post_array['product_tax'], $post_array['product_price']);
        $product->product_name = $post_array['product_name'];
        $product->product_slug = replaceSpaceWithDash($post_array['product_name']);
        $product->category_id = $post_array['category_id'];
        $product->product_tax = $post_array['product_tax'];
        $product->product_price = $post_array['product_price'];
        $product->product_base_price = $product_price_res['product_base_price'];
        $product->product_gst_value = $product_price_res['product_gst_value'];
        $product->product_final_price = $product_price_res['product_final_price'];
        $product->product_purchase_price = $post_array['product_purchase_price'];
        $product->product_margin = $post_array['product_margin'];
        $product->product_description = $post_array['product_description'];
        $product->is_status = $post_array['is_status'];
        if ($request->file('product_image')) {
            $strtotime = date('YmdHis');
            $image = $request->file('product_image');
            $image_name = $image->getClientOriginalName();
            $image_name = strtolower(str_replace(" ", "", $image_name));
            $modified_file_name = $strtotime . '_' . $image_name;
            $product->product_image = $modified_file_name;
        }
        $response = $product->save();
        $product_id = $product->id;

        if ($request->file('product_image')) {
            $image->move(public_path('uploads/products/' . $product_id . '/'), $modified_file_name);
            if (is_file(public_path('uploads/products/' . $product_id . '/' . $previous_file_name))) {
                unlink(public_path('uploads/products/' . $product_id . '/' . $previous_file_name));
            }
        }

        if ($response) {
            if ($id == 0) {
                $message = "Product added successfully.";
            } else if ($id > 0) {
                $message = "Product updated successfully.";
            }
            $message_class = "success";
        } else {
            if ($id == 0) {
                $message = "Error in adding Product. Please try again.";
            } else if ($id > 0) {
                $message = "Error in updating Product. Please try again.";
            }
            $message_class = "danger";
        }

        return redirect()->route('products')->with($message_class, $message);
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);
        $is_invoice = Invoice::with(['invoice_payments'])->whereHas('invoice_payments', function ($query) use ($id) {
            $query->where('product_id', $id);
        })->first();
        $is_subscription = Subscription::with(['subscription_payments'])->whereHas('subscription_payments', function ($query) use ($id) {
            $query->where('product_id', $id);
        })->first();
        $is_quote = Quote::with(['quote_payments'])->whereHas('quote_payments', function ($query) use ($id) {
            $query->where('product_id', $id);
        })->first();
        if ($is_invoice || $is_quote || $is_subscription) {
            $success = 2;
            $return['success'] = $success;
        } else {
            File::deleteDirectory(public_path('uploads/products/' . $id));
            $response = $product->delete();
            if ($response) {
                $success = 1;
            } else {
                $success = 0;
            }
            $return['success'] = $success;
        }

        return response()->json($return);
    }

    public function deleteProductImage($id)
    {
        $product = Product::whereId($id)->first();
        unlink(public_path('uploads/products/' . $id . '/' . $product->product_image));
        $affected = Product::whereId($id)->update(['product_image' => '']);
        if ($affected) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function deleteSelectedProductRecords(Request $request)
    {
        $post_array = $request->post();

        $is_invoice = Invoice::with(['invoice_payments'])->whereHas('invoice_payments', function ($query) use ($post_array) {
            $query->whereIn('product_id', $post_array['ids']);
        })->get();
        $is_subscription = Subscription::with(['subscription_payments'])->whereHas('subscription_payments', function ($query) use ($post_array) {
            $query->where('product_id', $post_array['ids']);
        })->get();
        $is_quote = Quote::with(['quote_payments'])->whereHas('quote_payments', function ($query) use ($post_array) {
            $query->where('product_id', $post_array['ids']);
        })->get();

        if (!empty($is_invoice->toArray()) || !empty($is_subscription->toArray()) || !empty($is_quote->toArray())) {
            $success = 2;
            $return['success'] = $success;
            return response()->json($return);
        }

        $response = Product::whereIn('id', $post_array['ids'])->delete();
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

    public function importProducts(Request $request)
    {
        Excel::import(new ProductsImport, request()->file('import_products_file'));

        return redirect()->route('products')->with('success', 'Products are imported successfully');
    }

    public function exportProducts()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }
}
