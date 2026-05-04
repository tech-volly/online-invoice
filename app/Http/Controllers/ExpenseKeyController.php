<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ExpenseKey;
use Illuminate\Http\Request;

class ExpenseKeyController extends Controller
{

    public function index()
    {
        $keys = ExpenseKey::latest()->get();
        $categories = Category::whereNull('deleted_at')->get();

        return view('expense_keys.index', compact('keys', 'categories'));
    }

    public function getData(Request $request)
    {
        $columns = [
            0 => 'expense_keys.id',
            1 => 'expense_keys.key',
            2 => 'categories.name'
        ];

        $length = $request->input('length');
        $start  = $request->input('start');
        $search = $request->input('search.value');

        $query = ExpenseKey::leftJoin('categories', 'expense_keys.category_id', '=', 'categories.id')
            ->select('expense_keys.*', 'categories.name as category_name');

        $totalData = $query->count();

        // 🔍 SEARCH
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('expense_keys.key', 'LIKE', "%{$search}%")
                    ->orWhere('categories.name', 'LIKE', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();

        // ORDER
        if ($request->has('order')) {
            $orderColumnIndex = $request->input('order.0.column');
            $orderDir = $request->input('order.0.dir');

            if (isset($columns[$orderColumnIndex])) {
                $query->orderBy($columns[$orderColumnIndex], $orderDir);
            }
        } else {
            $query->orderBy('expense_keys.id', 'desc');
        }

        $keys = $query->skip($start)->take($length)->get();

        $data = [];

        foreach ($keys as $k) {

            $action = '<div class="dropdown dropdown-action">
            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                <i class="material-icons">more_vert</i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">';

            if (auth()->user()->can('expense-edit')) {
                $action .= '<a class="dropdown-item" href="' . route('expense-keys.edit', $k->id) . '">
                            <i class="fa fa-pencil m-r-5"></i> Edit
                        </a>';
            }

            if (auth()->user()->can('expense-delete')) {
                $action .= '<a class="dropdown-item deleteKeyBtn" 
                            href="javascript:void(0)" 
                            data-id="' . $k->id . '">
                            <i class="fa fa-trash-o m-r-5"></i> Delete
                        </a>';
            }

            $action .= '</div></div>';

            $data[] = [
                'checkbox' => '<input type="checkbox" class="cb-element" value="' . $k->id . '">',
                'key' => $k->key,
                'category' => $k->category_name ?? '-',
                'blank' => '',
                'action' => $action
            ];
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ]);
    }

    public function create()
    {
        return view('expense_keys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required',
            'category_id' => 'required'
        ]);

        ExpenseKey::create($request->all());

        return redirect()->route('expense-keys')->with('success', 'Key added successfully');
    }

    public function edit($id)
    {
        $key = ExpenseKey::findOrFail($id);
        return view('expense_keys.edit', compact('key'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'key' => 'required',
            'category_id' => 'required'
        ]);

        $key = ExpenseKey::findOrFail($id);
        $key->update($request->all());

        return redirect()->route('expense-keys')->with('success', 'Key updated successfully');
    }

    public function destroy($id)
    {
        $key = ExpenseKey::findOrFail($id);
        $key->delete();

        return response()->json([
            'success' => 1
        ]);
    }
}
