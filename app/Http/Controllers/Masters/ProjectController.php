<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Imports\ProjectImport;
use Excel;
use Session;
use App\Models\Expense;

class ProjectController extends Controller
{
    function __construct(){
        $this->middleware('permission:project-list|project-create|project-edit|project-delete', ['only' => ['index','show']]);
        $this->middleware('permission:project-create', ['only' => ['create','createProject']]);
        $this->middleware('permission:project-edit', ['only' => ['edit','updateProject']]);
        $this->middleware('permission:project-delete', ['only' => ['deleteProject']]);
    }
    
    public function index() {
        $data = Project::orderBy('id','desc')->get();

        return view('masters.projects.index', compact('data'));
    }

    public function createProject(Request $request) {
        $projects_arr = explode(",", $request->project_name);
        foreach($projects_arr as $project_name) {
            $project = new Project;
            $project->name = trim($project_name);
            $project->is_status = $request->is_status;
            $project->save();
        }
        
        return redirect()->route('projects')->with('success','Project created successfully');
    }
    public function editProject($id) {
        $project = Project::find($id);
        $return = [
            'project' => $project,
            'success' => 1
        ];
       
        return response()->json($return);
    }

    public function updateProject(Request $request) {
        $project = Project::find($request->project_id);
        $project->name = $request->project_name;
        $project->is_status = $request->is_status;
        $response = $project->save();
        if($response) {
            $message = "Project updated successfully.";
            $message_class = "success";
        }else {
            $message = "Error in updating Project. Please try again.";
            $message_class = "danger";
        }

        return redirect()->route('projects')->with($message_class,$message);
    }

    public function deleteProject($id){
        $project = Project::find($id);
        $is_project = Expense::whereProjectId($id)->first();
        
        if($is_project) {
            $success = 2;
            $return['success'] = $success;
        }else {
            $response = $project->delete();
            if ($response) {
                $success = 1;
            } else {
                $success = 0;
            }
            $return['success'] = $success;
        }

        return response()->json($return);
    }

    public function deleteSelectedProjectRecords(Request $request) {
        $post_array = $request->post();
        $is_projects = Expense::whereIn('project_id', $post_array['ids'])->get();
        if(!empty($is_projects->toArray())) {
            $success = 2;
            $return['success'] = $success;
            return response()->json($return);
        }
        
        $response = Project::whereIn('id', $post_array['ids'])->delete();
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

    public function importProjects(Request $request) {
        Excel::import(new ProjectImport, request()->file('import_projects_file'));

        return redirect()->route('projects')->with('success','Project are imported successfully');
    }

    



}
