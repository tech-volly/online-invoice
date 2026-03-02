<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Department;
use App\Mail\SendLoginDetailsMail;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use Carbon\Carbon;
use Excel;
use Hash;
use Mail;
use Session;

class UserController extends Controller
{
    function __construct() {
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
        $this->middleware('permission:user-create', ['only' => ['addUser','store']]);
        $this->middleware('permission:user-edit', ['only' => ['addUser','update']]);
        $this->middleware('permission:user-delete', ['only' => ['deleteUser']]);
    }

    public function index() {
        $data = User::with(['department'])->orderBy('id', 'desc')->get();

        return view('users.index',compact('data'));
    }

    public function addUser($id = ''){
        $departments = Department::whereIsStatus(1)->orderby('name','asc')->get();
        $roles = Role::orderBy('name', 'asc')->get();
        if ($id == "") {
            $data = new User;
        } else if ($id > 0) {
            $data = User::find($id);
        }
        return view('users.add',compact('data', 'departments', 'roles'));
    }

    public function addUserAction(Request $request) {
        $post_array = $request->post();
        $id = (isset($post_array['id']) && $post_array['id'] > 0) ? $post_array['id'] : 0;
        $random_password = generateRandomPassword();        
        if($id == 0) {
            $user = new User();
            $password = Hash::make($random_password);
        }else if ($id > 0) {
            $user = User::find($id);
            if (trim($post_array['user_password']) != "") { 
                $password = Hash::make($post_array['user_password']);
            }else {
                $password = $user->password;
            }
        }

        $user->first_name = $post_array['first_name'];
        $user->last_name = $post_array['last_name'];
        $user->email = $post_array['email'];
        $user->phone_number = $post_array['phone_number'];
        $user->department_id = $post_array['department_id'];
        $user->password = $password;
        $user->is_status = $post_array['is_status'];
        $user->is_admin = 0;
        $user->is_verified = 1;
        $user->failed_login_attempts = $post_array['failed_login_attempts'] ? $post_array['failed_login_attempts'] : 0;
        $response = $user->save();
        // $user->assignRole($post_array['is_role']);
        $user->syncRoles($post_array['is_role']);


        if($response) {
            if($id == 0) {
                $loginUrl = url('/');    
                $details = [
                    'userName' => $user->first_name.' '.$user->last_name,
                    'userEmail' => $user->email,
                    'password' => $random_password,
                    'loginUrl' => $loginUrl,
                ];
                Mail::to($user->email)->cc(config('app.cc_admin_email'))->send(new SendLoginDetailsMail($details));
                $save_activity = [
                    'email_sender' => config('app.from_email_address'),
                    'email_receiver' => $user->email,
                    'email_content' => 'Send login details to newly created user',
                    'email_send_date' => Carbon::now()->format('Y-m-d H:i:s')
                ];
                $create_log = saveEmailActivity($save_activity);
                if (count(Mail::failures()) > 0) {
                    return back()->with('danger', 'Error in sending email to user email address.');
                }
                $message = "User added successfully.";
            }else if ($id > 0) {
                $message = "User updated successfully.";
            }
            $message_class = "success";
        }else {
            if ($id == 0) {
                $message = "Error in adding User. Please try again.";
            } else if ($id > 0) {
                $message = "Error in updating User. Please try again.";
            }
            $message_class = "danger";
        }

        return redirect()->route('users')->with($message_class, $message);
    }

    public function deleteUser($id) {
        $user = User::find($id);
        $response = $user->delete();
        if ($response) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function deleteSelectedUserRecords(Request $request) {
        $post_array = $request->post();
        $response = User::whereIn('id', $post_array['ids'])->delete();
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

    public function importUsers(Request $request) {
        Excel::import(new UsersImport, request()->file('import_users_file'));

        return redirect()->route('users')->with('success','Users are imported successfully');
    }

    public function exportUsers() {
        return Excel::download(new UsersExport, 'users.xlsx');
    }
}
