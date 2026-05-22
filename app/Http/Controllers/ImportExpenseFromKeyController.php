<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseKey;
use App\Models\PaymentMethod;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportExpenseFromKeyController extends Controller
{
    /**
     * Process uploaded CSV, match keys, show review page.
     */
    public function review(Request $request)
    {
        $request->validate([
            'import_keys_csv' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('import_keys_csv');
        $rows = $this->parseCsv($file->getRealPath());

        if (empty($rows)) {
            return redirect()->route('expenses')
                ->with('error', 'CSV file is empty or has no valid rows.');
        }

        // Load all expense keys for matching
        $expenseKeys = ExpenseKey::whereNotNull('key')->get();

        // Dropdowns for the review table
        $suppliers       = Supplier::whereNull('deleted_at')->orderBy('supplier_business_name')->get();
        $categories      = ExpenseCategory::whereNull('deleted_at')->orderBy('name')->get();
        $payment_methods = PaymentMethod::orderBy('payment_method_name')->whereIsStatus(1)->get();

        // Match each CSV row description against expense keys
        foreach ($rows as &$row) {
            $row['matched_key']         = null;
            $row['matched_category_id'] = null;
            $row['matched_supplier_id'] = null;

            $description = strtolower(trim($row['description']));

            foreach ($expenseKeys as $ek) {
                $keyword = strtolower(trim($ek->key));
                if (empty($keyword)) continue;

                if (str_contains($description, $keyword)) {
                    $row['matched_key']         = $ek->key;
                    $row['matched_category_id'] = $ek->category_id;
                    $row['matched_supplier_id'] = $ek->supplier_id;
                    break; // first match wins
                }
            }
        }
        unset($row);

        $defaults = [
            'tax' => $request->input('default_tax', 'GST Inclusive'),
        ];

        return view('expenses.import_review', compact(
            'rows',
            'suppliers',
            'categories',
            'payment_methods',
            'defaults'
        ));
    }

    /**
     * Save all active (non-skipped) rows as Expense records.
     * Uses the same pattern as ExpenseController::addExpenseAction()
     * to avoid any $fillable or column mismatch issues.
     */
    public function save(Request $request)
    {
        $rows = $request->input('rows', []);

        if (empty($rows)) {
            return redirect()->route('expenses')->with('error', 'No rows to save.');
        }

        $saved   = 0;
        $skipped = 0;
        $rowErrors = [];

        foreach ($rows as $i => $row) {

            // Row was manually skipped by user
            if (!empty($row['skip']) && $row['skip'] == 1) {
                $skipped++;
                continue;
            }

            // Validate required fields
            if (empty($row['category_id'])) {
                $rowErrors[] = "Row " . ($i + 1) . " missing Category.";
                continue;
            }
            if (empty($row['payment_method_id'])) {
                $rowErrors[] = "Row " . ($i + 1) . " missing Payment Method.";
                continue;
            }

            try {
                $amount = (float) ($row['amount'] ?? 0);
                $date   = $this->parseDate($row['date'] ?? '');

                // Use new Expense() + individual assignment + save()
                // This is identical to how ExpenseController::addExpenseAction works
                // and avoids any $fillable issues entirely.
                $expense = new Expense();
                $expense->supplier_invoice_number   = substr($row['description'] ?? '', 0, 100);
                $expense->payment_method_id         = $row['payment_method_id'];
                $expense->supplier_id               = !empty($row['supplier_id']) ? $row['supplier_id'] : null;
                $expense->supplier_expense_category = $row['category_id'];
                $expense->expense_tax               = $row['tax'] ?? 'GST Inclusive';
                $expense->expense_amount            = abs($amount);
                $expense->expense_date              = $date;
                $expense->expense_description       = $row['description'] ?? null;
                $expense->is_status                 = 1;

                $expense->save();
                $saved++;

            } catch (\Exception $e) {
                Log::error('ImportExpenseFromKey row ' . ($i + 1) . ' error: ' . $e->getMessage());
                $rowErrors[] = "Row " . ($i + 1) . " failed: " . $e->getMessage();
            }
        }

        if ($saved === 0 && !empty($rowErrors)) {
            // Nothing saved at all — send back with errors
            return redirect()->route('expenses')
                ->with('error', 'No expenses saved. Errors: ' . implode(' | ', array_slice($rowErrors, 0, 5)));
        }

        $message = "{$saved} expense(s) imported successfully.";
        if ($skipped)           $message .= " {$skipped} skipped.";
        if (!empty($rowErrors)) $message .= " " . count($rowErrors) . " row(s) had errors.";

        return redirect()->route('expenses')->with('success', $message);
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    private function parseCsv(string $filePath): array
    {
        $rows   = [];
        $handle = fopen($filePath, 'r');
        if (!$handle) return $rows;

        $header = fgetcsv($handle);
        if (!$header) { fclose($handle); return $rows; }

        $map = [];
        foreach ($header as $idx => $col) {
            $map[strtolower(trim($col))] = $idx;
        }

        if (!isset($map['date'], $map['description'], $map['amount'])) {
            fclose($handle);
            return $rows;
        }

        while (($line = fgetcsv($handle)) !== false) {
            if (empty(array_filter($line))) continue;

            $description = trim($line[$map['description']] ?? '');
            $amount      = (float) str_replace([',', '$', '"'], '', trim($line[$map['amount']] ?? '0'));
            $date        = trim($line[$map['date']] ?? '');

            if (empty($description) && $amount == 0) continue;

            $rows[] = compact('date', 'description', 'amount');
        }

        fclose($handle);
        return $rows;
    }

    private function parseDate(string $raw): ?string
    {
        if (empty($raw)) return null;
        $raw = trim($raw);

        foreach (['d/m/Y', 'j/n/Y', 'd/m/y', 'Y-m-d', 'm/d/Y'] as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $raw);
            if ($dt) return $dt->format('Y-m-d');
        }

        $ts = strtotime($raw);
        return $ts ? date('Y-m-d', $ts) : null;
    }
}
