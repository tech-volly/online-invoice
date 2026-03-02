<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Country;
use App\Models\Currency;
use App\Models\ClientContact;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\Quote;
use App\Imports\ClientsImport;
use App\Imports\ClientTempImport;
use App\Exports\ClientsExport;
use Excel;
use Session;

class ClientController extends Controller
{
    function __construct() {
        $this->middleware('permission:client-list|client-create|client-edit|client-delete', ['only' => ['index','store']]);
        $this->middleware('permission:client-create', ['only' => ['addClient','store']]);
        $this->middleware('permission:client-edit', ['only' => ['addClient','update']]);
        $this->middleware('permission:client-delete', ['only' => ['deleteClient']]);
    }

    public function index() {
        $data = Client::with(['contacts'])->orderBy('id','desc')->get();
        
        return view('clients.index', compact('data'));
    }

    public function addClient($id = "") {
        $countries = Country::orderBy('name', 'asc')->get();
        $currencies = Currency::orderBy('currency_name', 'asc')->get();
        if ($id == "") {
            $data = new Client;
        } else if ($id > 0) {
            $data = Client::with(['contacts'])->where('id', $id)->first();
        }

        return view('clients.add', compact('countries', 'currencies', 'data'));
    }

    public function addClientAction(Request $request) {        
        $post_array = $request->post();
        $id = (isset($post_array['id']) && $post_array['id'] > 0) ? $post_array['id'] : 0;
        $action_id = $request->contacts_id;
        $all_contacts_id = Client::with(['contacts'])->whereHas('contacts', function($query) use ($request) {
            $query->where('client_id', '=', $request->id);
        })->first();

        if($id == 0) {
            $client = new Client();
        }else if ($id > 0) {
            $client = Client::find($id);
        }
        
        $client->client_number = $post_array['client_number'];
        $client->client_business_name = $post_array['client_business_name'];
        $client->client_first_name = $post_array['client_first_name'];
        $client->client_last_name = $post_array['client_last_name'];
        $client->client_mobile = $post_array['client_mobile'];
        $client->client_street_address_1 = $post_array['client_street_address_1'];
        $client->client_city = $post_array['client_city'];
        $client->client_state = $post_array['client_state'];
        $client->client_postalcode = $post_array['client_postalcode'];

        if(isset($post_array['add_shipping_address']) && $post_array['add_shipping_address']) {
            $client->add_shipping_address = 'same_as_billing';
            $client->shipping_street_address_1 = $post_array['client_street_address_1'];
            $client->shipping_city = $post_array['client_city'];
            $client->shipping_state = $post_array['client_state'];
            $client->shipping_postalcode = $post_array['client_postalcode'];
        }else {
            $client->add_shipping_address = 'shipping_address_diff';
            $client->shipping_street_address_1 = $post_array['street_address_1'];
            $client->shipping_city = $post_array['city'];
            $client->shipping_state = $post_array['state'];
            $client->shipping_postalcode = $post_array['postalcode'];
        }
        $client->client_invoicing_method = 'send_via_email';
        $client->client_notes = $post_array['client_notes'];
        $client->client_tags = $post_array['client_tags'];
        $client->client_email = $post_array['client_email'];
        $client->is_status = $post_array['is_status'];
        $response = $client->save();

        if(!empty($request->contact_first_name[0])) {
            $contact_first_name_req = $request->contact_first_name;
            $count = count($contact_first_name_req);

            for ($i = 0; $i < $count; $i++) {
                if(isset($request->contacts_id[$i]) || !empty($request->contacts_id[$i])) {
                    $contact_id = $request->contacts_id[$i];
                    $contact = ClientContact::find($contact_id);
                }else {
                    $contact = new ClientContact();
                }
                $contact->client_id = $client->id;
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
                        $delete = ClientContact::find($cid->id);
                        $delete->delete();
                    }
                }
            }
        }
        if (empty($action_id) && !empty($request->another_id[0])) {
            foreach ($all_contacts_id->contacts as $cid) {
                $delete = ClientContact::find($cid->id);
                $delete->delete();
            }
        }

        if($response) {
            if($id == 0) {
                $message = "Client added successfully.";
            }else if ($id > 0) {
                $message = "Client updated successfully.";
            }
            $message_class = "success";
        }else {
            if ($id == 0) {
                $message = "Error in adding Client. Please try again.";
            } else if ($id > 0) {
                $message = "Error in updating Client. Please try again.";
            }
            $message_class = "danger";
        }

        return redirect()->route('clients')->with($message_class, $message);
    }

    public function deleteClient($id) {
        $client = Client::find($id);
        $is_client_invoice = Invoice::whereClientId($id)->first();
        $is_client_subscription = Subscription::whereClientId($id)->first();
        $is_client_quote = Quote::whereClientId($id)->first();

        if($is_client_invoice || $is_client_subscription || $is_client_quote) {
            $success = 2;
            $return['success'] = $success;
        }else {
            $response = $client->delete();
            if ($response) {
                $success = 1;
            } else {
                $success = 0;
            }
            $return['success'] = $success;
        }

        return response()->json($return);
    }

    public function deleteSelectedClientRecords(Request $request) {
        $post_array = $request->post();

        $is_client_invoice = Invoice::whereIn('client_id', $post_array['ids'])->get();
        $is_client_subscription = Subscription::whereIn('client_id', $post_array['ids'])->get();
        $is_client_quote = Quote::whereIn('client_id', $post_array['ids'])->get();
        if(!empty($is_client_invoice->toArray()) || !empty($is_client_subscription->toArray()) || !empty($is_client_quote->toArray()) ) {
            $success = 2;
            $return['success'] = $success;
            return response()->json($return);
        }
        $response = Client::whereIn('id', $post_array['ids'])->delete();
        if ($response) {
            $delete_client_contacts = ClientContact::whereIn('client_id', $post_array['ids'])->delete();
            Session::flash('success', 'Selected record(s) deleted successfully.');
            $success = 1;
        } else {
            Session::flash('danger', 'Error in deleting selected record(s). Please try again.');
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function viewClientNote($id) {
        $client = Client::find($id);
        if($client) {
            $return = [
                'client_notes' => $client->client_notes,
                'success' => 1
            ];
        }else {
            $return = [
                'client_notes' => null,
                'success' => 0
            ];
        }
       
        return response()->json($return);
    }

    public function importClients(Request $request) {
        Excel::import(new ClientsImport, request()->file('import_clients_file'));

        return redirect()->route('clients')->with('success','Clients are imported successfully');
    }

    public function exportClients() {
        return Excel::download(new ClientsExport, 'clients.xlsx');
    }

    public function importTemporaryClient(Request $request) {
        $import = new ClientTempImport;
        Excel::import($import, request()->file('import_clients_file'));
        $rows_data = $import->data->toArray();

        foreach($rows_data as $key => $data) {
            if($data['tags'] != '') {
                $client_tags = $data['tags'];
            }else {
                $client_tags = NULL;
            }
            $client = Client::where([
                'client_business_name' => $data['clientbusinessname'],
                'client_first_name' => $data['firstname'],
                'client_last_name' => $data['lastname'],
            ])->update([
                'client_tags' => $client_tags
            ]);

        }
    
        return redirect()->route('clients')->with('success','Clients are imported successfully');
    }
}
