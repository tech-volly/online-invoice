<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Supplier;
use App\Models\SupplierContact;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Imports\SuppliersImport;
use App\Imports\SupplierTempImport;
use App\Exports\SuppliersExport;
use Excel;
use Session;

class SupplierController extends Controller
{
    function __construct() {
        $this->middleware('permission:supplier-list|supplier-create|supplier-edit|supplier-delete', ['only' => ['index','store']]);
        $this->middleware('permission:supplier-create', ['only' => ['addSupplier','store']]);
        $this->middleware('permission:supplier-edit', ['only' => ['addSupplier','update']]);
        $this->middleware('permission:supplier-delete', ['only' => ['deleteSupplier']]);
    }

    public function index() {
        $data = Supplier::with(['contacts'])->orderBy('id', 'desc')->get();
        
        return view('suppliers.index', compact('data'));
    }

    public function addSupplier($id = "") {
        $countries = Country::orderBy('name', 'asc')->get();
        $categories = ExpenseCategory::orderBy('name','asc')->whereIsStatus(1)->get();
        if ($id == "") {
            $data = new Supplier;
        } else if ($id > 0) {
            $data = Supplier::with(['contacts'])->where('id', $id)->first();
        }

        return view('suppliers.add', compact('countries', 'categories' ,'data'));
    }

    public function addSupplierAction(Request $request) {   
        $post_array = $request->post();
        $id = (isset($post_array['id']) && $post_array['id'] > 0) ? $post_array['id'] : 0;
        $action_id = $request->contacts_id;
        $all_contacts_id = Supplier::with(['contacts'])->whereHas('contacts', function($query) use ($request) {
            $query->where('supplier_id', '=', $request->id);
        })->first();

        if($id == 0) {
            $supplier = new Supplier();
        }else if ($id > 0) {
            $supplier = Supplier::find($id);
        }
        
        $supplier->supplier_business_name = $post_array['supplier_business_name'];
        $supplier->supplier_expense_category = implode(',',$post_array['supplier_expense_category']);
        $supplier->supplier_first_name = $post_array['supplier_first_name'];
        $supplier->supplier_last_name = $post_array['supplier_last_name'];
        $supplier->supplier_mobile = $post_array['supplier_mobile'];
        $supplier->supplier_street_address_1 = $post_array['supplier_street_address_1'];
        $supplier->supplier_city = $post_array['supplier_city'];
        $supplier->supplier_state = $post_array['supplier_state'];
        $supplier->supplier_postalcode = $post_array['supplier_postalcode'];

        if(isset($post_array['add_shipping_address']) && $post_array['add_shipping_address']) {
            $supplier->add_shipping_address = 'same_as_billing';
            $supplier->shipping_street_address_1 = $post_array['supplier_street_address_1'];
            $supplier->shipping_city = $post_array['supplier_city'];
            $supplier->shipping_state = $post_array['supplier_state'];
            $supplier->shipping_postalcode = $post_array['supplier_postalcode'];
        }else {
            $supplier->add_shipping_address = 'shipping_address_diff';
            $supplier->shipping_street_address_1 = $post_array['street_address_1'];
            $supplier->shipping_city = $post_array['city'];
            $supplier->shipping_state = $post_array['state'];
            $supplier->shipping_postalcode = $post_array['postalcode'];
        }
        $supplier->supplier_tags = $post_array['supplier_tags'];
        $supplier->supplier_notes = $post_array['supplier_notes'];
        $supplier->supplier_email = $post_array['supplier_email'];
        $supplier->is_status = $post_array['is_status'];
        $response = $supplier->save();

        if(!$response) {
            return redirect()->route('suppliers')->with('danger', 'Error in adding Supplier. Please try again after sometime.'); 
        }
       
        if(!empty($request->contact_first_name[0])) {
            $contact_first_name_req = $request->contact_first_name;
            $count = count($contact_first_name_req);

            for ($i = 0; $i < $count; $i++) {
                if(isset($request->contacts_id[$i]) || !empty($request->contacts_id[$i])) {
                    $contact_id = $request->contacts_id[$i];
                    $contact = SupplierContact::find($contact_id);
                }else {
                    $contact = new SupplierContact();
                }
                $contact->supplier_id = $supplier->id;
                $contact->contact_first_name = $request->contact_first_name[$i];
                $contact->contact_last_name = $request->contact_last_name[$i];
                $contact->contact_email = $request->contact_email[$i];
                $contact->contact_telephone = $request->contact_telephone[$i];
                $contact->contact_mobile = $request->contact_mobile[$i];
                $contact->save();
            }

            if (!empty($all_contacts_id)) {
                foreach ($all_contacts_id->contacts as $cid) {
                    if (in_array($cid->id, $action_id)) {
                    } else {
                        $delete = SupplierContact::find($cid->id);
                        $delete->delete();
                    }
                }
            }
        }
        if (empty($action_id) && !empty($request->another_id[0])) {
            foreach ($all_contacts_id->contacts as $cid) {
                $delete = SupplierContact::find($cid->id);
                $delete->delete();
            }
        }

        if($response) {
            if($id == 0) {
                $message = "Supplier added successfully.";
            }else if ($id > 0) {
                $message = "Supplier updated successfully.";
            }
            $message_class = "success";
        }else {
            if ($id == 0) {
                $message = "Error in adding Supplier. Please try again.";
            } else if ($id > 0) {
                $message = "Error in updating Supplier. Please try again.";
            }
            $message_class = "danger";
        }

        return redirect()->route('suppliers')->with($message_class, $message);
    }

    public function deleteSupplier($id) {
        $supplier = Supplier::find($id);
        $is_supplier = Expense::whereSupplierId($id)->first();
        if($is_supplier) {
            $success = 2;
            $return['success'] = $success;
        }else {
            $response = $supplier->delete();
            if ($response) {
                $success = 1;
            } else {
                $success = 0;
            }
            $return['success'] = $success;
        }

        return response()->json($return);
    }

    public function deleteSelectedSupplierRecords(Request $request) {
        $post_array = $request->post();
        $is_supplier = Expense::whereIn('supplier_id', $post_array['ids'])->get();
        if(!empty($is_supplier->toArray())) {
            $success = 2;
            $return['success'] = $success;
            return response()->json($return);
        }
        $response = Supplier::whereIn('id', $post_array['ids'])->delete();
        if ($response) {
            $delete_supplier_contacts = SupplierContact::whereIn('supplier_id', $post_array['ids'])->delete();
            Session::flash('success', 'Selected record(s) deleted successfully.');
            $success = 1;
        } else {
            Session::flash('danger', 'Error in deleting selected record(s). Please try again.');
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function importSuppliers(Request $request) {
        Excel::import(new SuppliersImport, request()->file('import_suppliers_file'));

        return redirect()->route('suppliers')->with('success','Suppliers are imported successfully');
    }

    public function exportSuppliers() {
        return Excel::download(new SuppliersExport, 'suppliers.xlsx');
    }

    public function viewSupplierNote($id) {    
        $supplier = Supplier::find($id);
        if($supplier) {
            $return = [
                'supplier_notes' => $supplier->supplier_notes,
                'success' => 1
            ];
        }else {
            $return = [
                'supplier_notes' => null,
                'success' => 0
            ];
        }
       
        return response()->json($return);
    }

    public function importTemporarySupplier(Request $request) {
        $import = new SupplierTempImport;
        Excel::import($import, request()->file('import_suppliers_file'));
        $rows_data = $import->data->toArray();

        foreach($rows_data as $key => $data) {
            if($data['tags'] != '') {
                $supplier_tags = $data['tags'];
            }else {
                $supplier_tags = NULL;
            }
            $supplier = Supplier::where([
                'supplier_business_name' => $data['businessname'],
                'supplier_first_name' => $data['firstname'],
                'supplier_last_name' => $data['lastname'],
            ])->update([
                'supplier_tags' => $supplier_tags
            ]);

        }

        return redirect()->route('suppliers')->with('success','Suppliers are imported successfully');
    }

}
