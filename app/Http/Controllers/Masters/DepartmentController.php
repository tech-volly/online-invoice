<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;
use App\Imports\DepartmentImport;
use Session;
use Excel;

class DepartmentController extends Controller
{
    function __construct(){
        $this->middleware('permission:department-list|department-create|department-edit|department-delete', ['only' => ['index','show']]);
        $this->middleware('permission:department-create', ['only' => ['create','createDepartment']]);
        $this->middleware('permission:department-edit', ['only' => ['edit','updateDepartment']]);
        $this->middleware('permission:department-delete', ['only' => ['deleteDepartment']]);
    }
    
    public function index() {
        $data = Department::orderBy('id','desc')->get();

        return view('masters.departments.index', compact('data'));
    }

    public function createDepartment(Request $request) {
        $department_arr = explode(",", $request->department_name);
        foreach($department_arr as $department_name) {
            $department = new Department;
            $department->name = trim($department_name);
            $department->is_status = $request->is_status;
            $department->save();
        }

        return redirect()->route('departments')->with('success','Department created successfully');
    }

    public function editDepartment($id) {
        $category = Department::find($id);
        $return = [
            'category' => $category,
            'success' => 1
        ];
       
        return response()->json($return);
    }

    public function updateDepartment(Request $request) {
        $department = Department::find($request->department_id);
        $department->name = $request->department_name;
        $department->is_status = $request->is_status;
        $response = $department->save();
        if($response) {
            $message = "Department updated successfully.";
            $message_class = "success";
        }else {
            $message = "Error in updating Department. Please try again.";
            $message_class = "danger";
        }

        return redirect()->route('departments')->with($message_class,$message);
    }

    public function deleteDepartment($id){
        $department = Department::find($id);
        $is_department = User::whereDepartmentId($id)->first();
        if($is_department) {
            $success = 2;
            $return['success'] = $success;
        }else {
            $response = $department->delete();
            if ($response) {
                $success = 1;
            } else {
                $success = 0;
            }
            $return['success'] = $success;
        }


        return response()->json($return);
    }

    public function deleteSelectedDepartmentRecords(Request $request) {
        $post_array = $request->post();
        
        $is_departments = User::whereIn('department_id', $post_array['ids'])->get();
        if(!empty($is_departments->toArray())) {
            $success = 2;
            $return['success'] = $success;
            return response()->json($return);
        }
        
        $response = Department::whereIn('id', $post_array['ids'])->delete();
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

    public function importDepartments(Request $request) {
        Excel::import(new DepartmentImport, request()->file('import_department_file'));

        return redirect()->route('departments')->with('success','Departments are imported successfully');
    }
}
