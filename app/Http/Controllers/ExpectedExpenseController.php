<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpectedExpense;
use App\Models\ExpectedExpenseList;
use Carbon\Carbon;
use Session;

class ExpectedExpenseController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:expected-expense-list|expected-expense-create|expected-expense-edit|expected-expense-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:expected-expense-create', ['only' => ['createExpectedExpense', 'store']]);
        $this->middleware('permission:expected-expense-edit', ['only' => ['editExpectedExpense', 'updateExpectedExpense']]);
        $this->middleware('permission:expected-expense-delete', ['only' => ['deleteExpectedExpense']]);
    }

    public function index()
    {
        // $data = ExpectedExpense::orderBy("id", "desc")->get();

        // return view('expected-expenses.index', compact('data'));
        return view('expected-expenses.index');
    }

    public function getExpectedExpenses(Request $request)
    {
        $columns = [
            0 => 'expected_expenses.id',
            1 => 'expected_expenses.expected_expense_year',
            2 => 'expected_expenses.is_status',
        ];

        $length = $request->input('length');
        $start  = $request->input('start');
        $search = $request->input('search.value');

        $query = ExpectedExpense::select('expected_expenses.*');

        $totalData = $query->count();

        // 🔍 SEARCH
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('expected_expenses.expected_expense_year', 'LIKE', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();

        // 📌 ORDERING
        if ($request->has('order')) {
            $orderColumnIndex = $request->input('order.0.column');
            $orderDir = $request->input('order.0.dir');

            if (isset($columns[$orderColumnIndex])) {
                $query->orderBy($columns[$orderColumnIndex], $orderDir);
            }
        } else {
            $query->orderBy('expected_expenses.id', 'desc');
        }

        $expectedExpenses = $query->skip($start)->take($length)->get();

        $data = [];

        foreach ($expectedExpenses as $v) {

            $statusHtml = $v->is_status == 1
                ? '<span class="btn btn-white btn-sm btn-rounded"><i class="fa fa-dot-circle-o text-success"></i> Active</span>'
                : '<span class="btn btn-white btn-sm btn-rounded"><i class="fa fa-dot-circle-o text-danger"></i> Inactive</span>';

            $action = '<div class="dropdown dropdown-action">
            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="material-icons">more_vert</i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="' . route('expected-expenses.clone', $v->id) . '">
                    <i class="fa fa-clone m-r-5"></i> Clone
                </a>';

            if (auth()->user()->can('expected-expense-edit')) {
                $action .= '<a class="dropdown-item" href="' . route('expected-expenses.edit', $v->id) . '">
                            <i class="fa fa-pencil m-r-5"></i> Edit
                        </a>';
            }

            if (auth()->user()->can('expected-expense-delete')) {
                $action .= '<a class="dropdown-item deleteExpectedExpenseBtn" 
                            href="javascript:void(0)" 
                            data-id="' . $v->id . '">
                            <i class="fa fa-trash-o m-r-5"></i> Delete
                        </a>';
            }

            $action .= '</div></div>';

            $data[] = [
                'id'       => $v->id,
                'year'     => $v->expected_expense_year,
                'status'   => $statusHtml,
                'action'   => $action,
            ];
        }

        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        ]);
    }

    public function createExpectedExpense(Request $request)
    {
        $expected_expense = new ExpectedExpense;
        $split_year = explode('-', $request->expected_expense_year);
        $expected_expense->expected_expense_year = $request->expected_expense_year;
        $expected_expense->selected_year = trim($split_year[0]);
        $expected_expense->is_status = $request->is_status;
        $expected_expense->save();

        return redirect()->route('expected-expenses')->with('success', 'Expected expense created successfully');
    }

    public function editExpectedExpense($id)
    {
        $data = ExpectedExpense::with(['epxected_expense_list'])->whereId($id)->first();

        $data_year = explode(" - ", $data->expected_expense_year);
        $data_year[0] = substr($data_year[0], 2);
        $data_year[1] = substr($data_year[1], 2);

        return view('expected-expenses.edit', compact('data', 'data_year'));
    }

    public function updateExpectedExpense(Request $request)
    {
        $action_id = $request->expected_expense_list_id;
        $all_expected_expense_list_id = ExpectedExpense::with(['epxected_expense_list'])->whereHas('epxected_expense_list', function ($query) use ($request) {
            $query->where('expected_expense_id', '=', $request->id);
        })->first();

        $expected_expense = ExpectedExpense::whereId($request->id)->first();
        $split_year = explode('-', $request->expected_expense_year);
        $expected_expense->expected_expense_year = $expected_expense->expected_expense_year;
        $expected_expense->selected_year = $expected_expense->selected_year;
        $response = $expected_expense->save();
        if (!empty($request->expected_expense_name[0])) {
            $count = count($request->expected_expense_name);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->expected_expense_list_id[$i]) || !empty($request->expected_expense_list_id[$i])) {
                    $expectedExpenseList_id = $request->expected_expense_list_id[$i];
                    $expected_expense_list = ExpectedExpenseList::find($expectedExpenseList_id);
                } else {
                    $expected_expense_list = new ExpectedExpenseList;
                }
                $expected_expense_list->expected_expense_id = $expected_expense->id;
                $expected_expense_list->expected_expense_name = $request->expected_expense_name[$i];
                $expected_expense_list->expected_july_expense = $request->expected_july_expense[$i];
                $expected_expense_list->expected_aug_expense = $request->expected_aug_expense[$i];
                $expected_expense_list->expected_sept_expense = $request->expected_sept_expense[$i];
                $expected_expense_list->expected_oct_expense = $request->expected_oct_expense[$i];
                $expected_expense_list->expected_nov_expense = $request->expected_nov_expense[$i];
                $expected_expense_list->expected_desc_expense = $request->expected_desc_expense[$i];
                $expected_expense_list->expected_jan_expense = $request->expected_jan_expense[$i];
                $expected_expense_list->expected_feb_expense = $request->expected_feb_expense[$i];
                $expected_expense_list->expected_mar_expense = $request->expected_mar_expense[$i];
                $expected_expense_list->expected_apr_expense = $request->expected_apr_expense[$i];
                $expected_expense_list->expected_may_expense = $request->expected_may_expense[$i];
                $expected_expense_list->expected_june_expense = $request->expected_june_expense[$i];
                $expected_expense_list->expected_annual_expense = $request->expected_annual_expense[$i];
                $expected_expense_list->save();
            }
            if (!empty($all_expected_expense_list_id)) {
                foreach ($all_expected_expense_list_id->epxected_expense_list as $e_id) {
                    if (in_array($e_id->id, $action_id)) {
                    } else {
                        $delete = ExpectedExpenseList::find($e_id->id);
                        $delete->delete();
                    }
                }
            }
        }
        if (empty($action_id) && !empty($request->another_id[0])) {
            foreach ($all_expected_expense_list_id->epxected_expense_list as $e_id) {
                $delete = ExpectedExpenseList::find($e_id->id);
                $delete->delete();
            }
        }

        if ($response) {
            $message = "Expected expense updated successfully.";
            $message_class = "success";
        } else {
            $message = "Error in updating Expected expense. Please try again.";
            $message_class = "danger";
        }

        return redirect()->route('expected-expenses')->with($message_class, $message);
    }

    public function cloneExpectedExpense($id)
    {
        $expected_expense = ExpectedExpense::find($id);
        $split_fin_year = explode("-", $expected_expense->expected_expense_year);
        $fin_year_start = trim($split_fin_year[1]);
        $fin_year_end = $fin_year_start + 1;
        $financial_year = $fin_year_start . ' - ' . $fin_year_end;

        $clone_expected_expense = $expected_expense->replicate();
        $clone_expected_expense->expected_expense_year = $financial_year;
        $clone_expected_expense->selected_year = $fin_year_start;
        $clone_expected_expense->created_at = Carbon::now();
        $clone_expected_expense->save();
        $expected_expense_list = ExpectedExpenseList::whereExpectedExpenseId($id)->get();
        foreach ($expected_expense_list as $expense_list) {
            $clone_expense_list = new ExpectedExpenseList();
            $clone_expense_list->expected_expense_id = $clone_expected_expense->id;
            $clone_expense_list->expected_expense_name = $expense_list->expected_expense_name;
            $clone_expense_list->expected_july_expense = $expense_list->expected_july_expense;
            $clone_expense_list->expected_aug_expense = $expense_list->expected_aug_expense;
            $clone_expense_list->expected_sept_expense = $expense_list->expected_sept_expense;
            $clone_expense_list->expected_oct_expense = $expense_list->expected_oct_expense;
            $clone_expense_list->expected_nov_expense = $expense_list->expected_nov_expense;
            $clone_expense_list->expected_desc_expense = $expense_list->expected_desc_expense;
            $clone_expense_list->expected_jan_expense = $expense_list->expected_jan_expense;
            $clone_expense_list->expected_feb_expense = $expense_list->expected_feb_expense;
            $clone_expense_list->expected_mar_expense = $expense_list->expected_mar_expense;
            $clone_expense_list->expected_apr_expense = $expense_list->expected_apr_expense;
            $clone_expense_list->expected_may_expense = $expense_list->expected_may_expense;
            $clone_expense_list->expected_june_expense = $expense_list->expected_june_expense;
            $clone_expense_list->expected_annual_expense = $expense_list->expected_annual_expense;
            $clone_expense_list->save();
        }

        return redirect()->route('expected-expenses.edit', $clone_expected_expense->id)->with('success', 'Expected expense clonned successfully.');
    }

    public function deleteExpectedExpense($id)
    {
        $expense = ExpectedExpense::find($id);
        $response = $expense->delete();
        if ($response) {
            $success = 1;
        } else {
            $success = 0;
        }
        $return['success'] = $success;

        return response()->json($return);
    }

    public function deleteSelectedExpectedExpenseRecords(Request $request)
    {
        $post_array = $request->post();
        $collection = ExpectedExpense::whereIn('id', $post_array['ids'])->get(['id']);
        $response = ExpectedExpense::destroy($collection->toArray());
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
