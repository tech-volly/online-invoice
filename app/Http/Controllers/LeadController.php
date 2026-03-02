<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Country;
use App\Models\LeadContact;
use App\Models\LeadFollowUp;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\LeadReminderMail;
use App\Mail\AddEditLeadNotifyMail;
use App\Jobs\AddEditLeadReminderJob;
use App\Jobs\LeadReminderJob;
use App\Exports\LeadsExport;
use Auth;
use Mail;
use Session;
use Exception;
use Excel;

class LeadController extends Controller
{
    function __construct() {
        $this->middleware('permission:lead-list|lead-create|lead-edit|lead-delete', ['only' => ['index','store']]);
        $this->middleware('permission:lead-create', ['only' => ['addLead','store']]);
        $this->middleware('permission:lead-edit', ['only' => ['addLead','update']]);
        $this->middleware('permission:lead-delete', ['only' => ['deleteLead']]);
    }
    
    public function index(){
        $data = Lead::with(['lead_follow_ups'])->orderBy('id','desc')->get();

        return view('leads.index', compact('data'));
    }

    public function addLead($id = "") {
        $countries = Country::orderBy('name', 'asc')->get();
        if ($id == "") {
            $data = new Lead;
        } else if ($id > 0) {
            $data = Lead::where('id', $id)->with(['contacts', 'lead_follow_ups'])->first();
        }

        return view('leads.add', compact('countries', 'data'));
    }

    public function addLeadAction(Request $request) {
        $post_array = $request->post();

        if(isset($request->follow_up_datetime)) {
            $followup_datetime = chnageDateFormat($request->follow_up_datetime);
        }
        if(isset($request->lead_discussion_date)) {
            $discussion_date = chnageDateFormat($request->lead_discussion_date);
        }

        $id = (isset($post_array['id']) && $post_array['id'] > 0) ? $post_array['id'] : 0;
        if($id == 0) {
            $lead = new Lead();
            $action = 'added';
        }else if ($id > 0) {
            $lead = Lead::find($id);
            $action = 'edited';
        }
        
        $lead->client_first_name = $post_array['client_first_name'];
        $lead->client_last_name = $post_array['client_last_name'];
        $lead->company_name = $post_array['company_name'];
        $lead->client_mobile = $post_array['client_mobile'];
        $lead->client_email = $post_array['client_email'];
        $lead->is_status = $post_array['is_status'];
        $response = $lead->save();
        
        if(!empty($request->follow_up_datetime)) {
            $followup = new LeadFollowUp;
            $followup->lead_id = $lead->id;
            $followup->followup_datetime = $followup_datetime;
            $followup->lead_discussion_date = $discussion_date;
            $followup->followup_notes = $post_array['followup_notes'];
            $followup->lead_created_by = Auth::user()->first_name.' '.Auth::user()->last_name;
            $followup->save();

            LeadFollowUp::where('lead_id', $lead->id)->where('followup_datetime', '<', $followup_datetime)->update([
                'followup_reminder_status' => 1
            ]);
        }
        
        //Send Email to all operation role type users
        $operation_role_users = User::role('Operations')->whereIsStatus(1)->get();
        $all_user_emails = array_column($operation_role_users->toArray(),'email');
        $lead_Details = Lead::whereId($lead->id)->first();
        $lead_followup_details = LeadFollowUp::whereLeadId($lead->id)->latest()->first();
        $details = [
            'companyName' => $lead_Details->company_name ,
            'clientName' => $lead_Details->client_first_name.' '.$lead_Details->client_last_name,
            'lead_followup_details' => $lead_followup_details,
            'action' => $action,
            'loggedin_user' => Auth::user()->first_name.' '.Auth::user()->last_name,
            'all_user_emails' => $all_user_emails
        ];
        #dispatch(new AddEditLeadReminderJob($details));
        Mail::to(config('app.cc_admin_email'))->cc($details['all_user_emails'])->send(new AddEditLeadNotifyMail($details));
        $save_activity = [
            'email_sender' => config('app.from_email_address'),
            'email_receiver' => implode(", ",$details['all_user_emails']),
            'email_content' => 'Lead is '.$details['action'].' by '.$details['loggedin_user'],
            'email_send_date' => Carbon::now()->format('Y-m-d H:i:s')
        ];
        $create_log = saveEmailActivity($save_activity);

        if($response) {
            if($id == 0) {
                $message = "Lead added successfully.";
            }else if ($id > 0) {
                $message = "Lead updated successfully.";
            }
            $message_class = "success";
        }else {
            if ($id == 0) {
                $message = "Error in adding Lead. Please try again.";
            } else if ($id > 0) {
                $message = "Error in updating Lead. Please try again.";
            }
            $message_class = "danger";
        }

        return redirect()->route('leads')->with($message_class, $message);
    }

    public function deleteLead($id) {
        $lead = Lead::find($id);
        $response = $lead->delete();
        if ($response) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function leadFollowUpDetails($id) {
        $lead_followup_details = LeadFollowUp::where('lead_id', $id)->get();
        return view('leads.lead-followup-details', compact('lead_followup_details'));
    }

    public function leadFollowUpCron() {
        $leads = LeadFollowUp::where('followup_reminder_status', 0)->get();
        $operation_role_users = User::role('Operations')->whereIsStatus(1)->get();
        $all_user_emails = array_column($operation_role_users->toArray(),'email');
        if(count($leads) > 0) {
            foreach($leads as $lead) {
                $current_time = Carbon::now()->format('Y-m-d');
                if($current_time == $lead->followup_datetime) {
                    $leadDetails = Lead::whereId($lead->lead_id)->first();
                    $details = [
                        'clientName' => $leadDetails->client_first_name.' '.$leadDetails->client_last_name,
                        'companyName' => $leadDetails->company_name,
                        'followUpTime' => $lead->followup_datetime,
                        'followUpDetails' => $lead,
                        'all_user_emails' => $all_user_emails
                    ];
                    #dispatch(new LeadReminderJob($details));
                    Mail::to(config('app.cc_admin_email'))->cc($details['all_user_emails'])->send(new LeadReminderMail($details));
                    $save_activity = [
                        'email_sender' => config('app.from_email_address'),
                        'email_receiver' => implode(", ",$details['all_user_emails']),
                        'email_content' => 'Lead Reminder from cron',
                        'email_send_date' => Carbon::now()->format('Y-m-d H:i:s')
                    ];
                    $create_log = saveEmailActivity($save_activity);
                    LeadFollowUp::where('followup_datetime', $lead->followup_datetime)->update([
                        'followup_reminder_status' => 1
                    ]);
                }
            }
        }
    }

    public function deleteSelectedLeadRecords(Request $request) {
        $post_array = $request->post();
        $collection = Lead::whereIn('id', $post_array['ids'])->get(['id']);
        $response = Lead::destroy($collection->toArray());
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

    public function exportLeads() {
        return Excel::download(new LeadsExport, 'leads.xlsx');
    }
}
