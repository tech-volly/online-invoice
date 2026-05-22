<?php

namespace App\Http\Controllers;

use App\Imports\ExpenseKeysImport;
use App\Models\ExpenseCategory;
use App\Models\ExpenseKey;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseKeyController extends Controller
{

    public function index()
    {
        $keys       = ExpenseKey::latest()->get();
        $categories = ExpenseCategory::whereNull('deleted_at')->orderBy('name')->get();
        $suppliers  = Supplier::whereNull('deleted_at')->get();

        return view('expense_keys.index', compact('keys', 'categories', 'suppliers'));
    }

    public function getData(Request $request)
    {
        $columns = [
            0 => 'expense_keys.id',
            1 => 'expense_keys.key',
            2 => 'expense_categories.name',
            3 => 'suppliers.supplier_business_name'
        ];

        $length = $request->input('length');
        $start  = $request->input('start');
        $search = $request->input('search.value');

        $query = ExpenseKey::leftJoin('expense_categories', 'expense_keys.category_id', '=', 'expense_categories.id')
            ->leftJoin('suppliers', 'expense_keys.supplier_id', '=', 'suppliers.id')
            ->select(
                'expense_keys.*',
                'expense_categories.name as category_name',
                'suppliers.supplier_business_name as supplier_name'
            );

        $totalData = $query->count();

        // SEARCH
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('expense_keys.key', 'LIKE', "%{$search}%")
                    ->orWhere('expense_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('suppliers.supplier_business_name', 'LIKE', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();

        // ORDER
        if ($request->has('order')) {
            $orderColumnIndex = $request->input('order.0.column');
            $orderDir         = $request->input('order.0.dir');
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
                // Pass all field values as data-* attributes so JS can populate the edit modal
                $action .= '<a class="dropdown-item editKeyBtn"
                                href="javascript:void(0)"
                                data-id="'       . $k->id          . '"
                                data-key="'      . e($k->key)      . '"
                                data-category="' . ($k->category_id ?? '') . '"
                                data-supplier="' . ($k->supplier_id ?? '') . '">
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
                'key'      => e($k->key),
                'category' => $k->category_name ?? '-',
                'supplier' => $k->supplier_name  ?? '-',
                'action'   => $action,
            ];
        }

        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }

    public function create()
    {
        return view('expense_keys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key'         => 'required',
            'category_id' => 'required|exists:expense_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        ExpenseKey::create($request->only('key', 'category_id', 'supplier_id'));

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
            'key'         => 'required',
            'category_id' => 'required|exists:expense_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $key = ExpenseKey::findOrFail($id);
        $key->update($request->only('key', 'category_id', 'supplier_id'));

        // Return JSON for AJAX form submit, or redirect for normal submit
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => 1, 'message' => 'Key updated successfully']);
        }

        return redirect()->route('expense-keys')->with('success', 'Key updated successfully');
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_expense_keys_file' => 'required|file|mimes:xls,xlsx,csv,txt|max:10240',
        ]);

        $import = new ExpenseKeysImport();
        Excel::import($import, $request->file('import_expense_keys_file'));

        $message = "{$import->imported} expense key(s) imported successfully.";
        if ($import->createdCategories > 0) {
            $message .= " {$import->createdCategories} expense categor" . ($import->createdCategories === 1 ? 'y' : 'ies') . " created.";
        }
        if ($import->createdSuppliers > 0) {
            $message .= " {$import->createdSuppliers} supplier(s) created.";
        }
        if ($import->updated > 0) {
            $message .= " {$import->updated} existing key(s) updated.";
        }
        if ($import->skipped > 0) {
            $message .= " {$import->skipped} row(s) skipped.";
        }

        return redirect()->route('expense-keys')->with('success', $message);
    }

    public function destroy($id)
    {
        $key = ExpenseKey::findOrFail($id);
        $key->delete();

        return response()->json(['success' => 1]);
    }
}
