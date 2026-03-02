<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailLog;
use App\Exports\EmailLogsExport;
use Session;
use Excel;

class EmailLogController extends Controller
{
    function __construct() {
        $this->middleware('permission:email-log-list|email-log-delete', ['only' => ['index','deleteEmailLog','deleteSelectedEmailRecords']]);
        $this->middleware('permission:email-log-delete', ['only' => ['deleteEmailLog','deleteSelectedEmailRecords']]);
    }

    public function index() {
        $data = EmailLog::orderBy('id', 'desc')->get();

        return view("email-logs.index", compact('data'));
    }

    public function deleteEmailLog($id) {
        $email_log = EmailLog::find($id);
        $response = $email_log->delete();
        if ($response) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function deleteSelectedEmailRecords(Request $request) {
        $post_array = $request->post();
        $response = EmailLog::whereIn('id', $post_array['ids'])->delete();
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

    public function exportEmailLogs() {
        return Excel::download(new EmailLogsExport, 'email-logs.xlsx');
    }

    public function deleteOlderEmailLogs() {
        $email_logs = EmailLog::whereDate('email_send_date', '<=', now()->subDays(60))->delete();
        if(!$email_logs) {
            return "Error in deleting email logs.";    
        }
        
        return "Email logs are deleted successfully.";
    }
}
