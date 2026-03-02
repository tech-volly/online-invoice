<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use DB;
use Session;

class RoleController extends Controller
{
    function __construct() {
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
        $this->middleware('permission:role-create', ['only' => ['create','store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    public function index() {
        $permission = Permission::get();
        $roles = $roles = Role::orderBy('id','desc')->get();
       
        return view('auth.roles.index',compact('permission', 'roles'));
    }

    public function createRole(Request $request) {
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));
    
        return redirect()->route('roles')->with('success','Role created successfully');
    }

    public function editRole($id) {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        $return = [
            'role' => $role,
            'permission' => $permission,
            'rolePermissions' => $rolePermissions,
            'success' => 1
        ];
       
        return response()->json($return);
    }

    public function updateRole(Request $request) {
        $role = Role::find($request->role_id);
        $role->name = $request->name;
        $response = $role->save();
        $role->syncPermissions($request->permission);
        if($response) {
            $message = "Role updated successfully.";
            $message_class = "success";
        }else {
            $message = "Error in updating Role. Please try again.";
            $message_class = "danger";
        }

        return redirect()->route('roles')->with($message_class,$message);
    }

    public function deleteRole($id)
    {
        $role = Role::find($id);
        $response = $role->delete();
        if ($response) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function deleteSelectedRoleRecords(Request $request) {
        $post_array = $request->post();
        $response = Role::whereIn('id', $post_array['ids'])->delete();
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
