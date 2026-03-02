<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceResource;
use App\Models\InvoiceResourceImage;
use File;
use ZipArchive;
use Session;

class InvoiceResourceController extends Controller
{
    function __construct() {
        $this->middleware('permission:resource-list|resource-create|resource-edit|resource-delete', ['only' => ['index','store']]);
        $this->middleware('permission:resource-create', ['only' => ['addResource','store']]);
        $this->middleware('permission:resource-edit', ['only' => ['addResource','update']]);
        $this->middleware('permission:resource-delete', ['only' => ['deleteResource']]);
    }
    
    public function index(){
        $data = InvoiceResource::orderBy('id','desc')->get();

        return view('invoice-resource.index', compact('data'));
    }

    public function addResource($id = "") {
        if ($id == "") {
            $data = new InvoiceResource;
        } else if ($id > 0) {
            $data = InvoiceResource::where('id', $id)->with(['invoice_resource_images'])->first();
        }

        return view('invoice-resource.add', compact('data'));
    }

    public function addResourceAction(Request $request) {
        $post_array = $request->post();
        $id = (isset($post_array['id']) && $post_array['id'] > 0) ? $post_array['id'] : 0;
        if($id == 0) {
            $resource = new InvoiceResource();
            $previous_file_name = "";
        }else if ($id > 0) {
            $resource = InvoiceResource::find($id);
        
        }
        $resource->resource_name = $post_array['resource_name'];
        $resource->is_status = $post_array['is_status'];
        $response = $resource->save();
        if($request->hasFile('resource_image')){
            $files = $request->file('resource_image');
            foreach($files as $file){            
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $modified_ref_file_name = date('YmdHis') . '_' . str_replace(" ", "_", $filename);
                $invoice_resource = new InvoiceResourceImage;
                $invoice_resource->invoice_resource_id = $resource->id;
                $invoice_resource->resource_image = $modified_ref_file_name;
                $invoice_resource->save();
                $file->move(public_path('uploads/invoice-resources/' . $resource->id . '/'), $modified_ref_file_name); 
            }
        }

        if($response) {
            if($id == 0) {
                $message = "Resource added successfully.";
            }else if ($id > 0) {
                $message = "Resource updated successfully.";
            }
            $message_class = "success";
        }else {
            if ($id == 0) {
                $message = "Error in adding Resource. Please try again.";
            } else if ($id > 0) {
                $message = "Error in updating Resource. Please try again.";
            }
            $message_class = "danger";
        }

        return redirect()->route('invoice-resources')->with($message_class, $message);
    }

    public function deleteResource($id) {
        $resource = InvoiceResource::find($id);
        File::deleteDirectory(public_path('uploads/invoice-resources/' . $id));
       
        $response = $resource->delete();
        if ($response) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function deleteInvoiceResourceImage($id) {
        $invoice_resource = InvoiceResourceImage::find($id);
        if($invoice_resource) {
            unlink(public_path('uploads/invoice-resources/' . $invoice_resource->invoice_resource_id . '/' . $invoice_resource->resource_image));
        }
        $response = $invoice_resource->delete();
        if ($response) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function deleteSelectedResourceRecords(Request $request) {
        $post_array = $request->post();
        foreach($post_array['ids'] as $id) {
            File::deleteDirectory(public_path('uploads/invoice-resources/' . $id));
            InvoiceResourceImage::whereInvoiceResourceId($id)->delete();
        }
        $response = InvoiceResource::whereIn('id', $post_array['ids'])->delete();
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

    public function downloadInvoiceResources($id) {
        $invoice_resource = InvoiceResource::find($id);
        $fileName = $invoice_resource->resource_name.'.zip';
        $zip_file = public_path('uploads/'.$fileName);
        $zip = new \ZipArchive();        
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = File::files(public_path('uploads/invoice-resources/'.$id));
        foreach ($files as $key => $value) {
            $relativeName = basename($value);
            $zip->addFile($value, $relativeName);
        }
        $zip->close();

        return response()->download($zip_file)->deleteFileAfterSend(true);
    }
}
