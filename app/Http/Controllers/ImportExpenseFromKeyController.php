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

        // Load all expense keys for matching. Longer keys should win before short keys.
        $expenseKeys = ExpenseKey::whereNotNull('key')->get()
            ->sortByDesc(function ($expenseKey) {
                return strlen($this->normalizeMatchText($expenseKey->key));
            });

        // Dropdowns for the review table
        $suppliers       = Supplier::whereNull('deleted_at')->orderBy('supplier_business_name')->get();
        $categories      = ExpenseCategory::whereNull('deleted_at')->orderBy('name')->get();
        $payment_methods = PaymentMethod::orderBy('payment_method_name')->whereIsStatus(1)->get();

        // Match each CSV row description against expense keys
        foreach ($rows as &$row) {
            $row['matched_key']         = null;
            $row['matched_category_id'] = null;
            $row['matched_supplier_id'] = null;

            $description = $this->normalizeMatchText($row['description']);

            foreach ($expenseKeys as $ek) {
                $keyword = $this->normalizeMatchText($ek->key);
                if (empty($keyword)) continue;

                if ($this->matchesExpenseKey($description, $keyword)) {
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
        $duplicateRows = [];
        $seenImportRows = [];
        $createdKeys = 0;
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
                $supplierId = !empty($row['supplier_id']) ? $row['supplier_id'] : null;
                $supplierBusinessName = $this->supplierBusinessName($supplierId);
                $createdKeys += $this->createExpenseKeyFromUnmatchedRow($row, $supplierId);

                if ($this->isDuplicateExpense($supplierBusinessName, $date, $seenImportRows)) {
                    $duplicateRows[] = $i + 1;
                    $skipped++;
                    continue;
                }

                if ($supplierBusinessName && $date) {
                    $seenImportRows[] = $this->duplicateKey($supplierBusinessName, $date);
                }

                // Use new Expense() + individual assignment + save()
                // This is identical to how ExpenseController::addExpenseAction works
                // and avoids any $fillable issues entirely.
                $expense = new Expense();
                $expense->supplier_invoice_number   = substr($row['description'] ?? '', 0, 100);
                $expense->payment_method_id         = $row['payment_method_id'];
                $expense->supplier_id               = $supplierId;
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
        if (!empty($duplicateRows)) $message .= " Duplicate entry found in row " . implode(', ', $duplicateRows) . ".";
        if ($createdKeys)       $message .= " {$createdKeys} new expense key(s) added.";
        if (!empty($rowErrors)) $message .= " " . count($rowErrors) . " row(s) had errors.";

        $messageClass = !empty($duplicateRows) ? 'danger' : 'success';

        return redirect()->route('expenses')->with($messageClass, $message);
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

        $hasStarted = false;

        while (($line = fgetcsv($handle)) !== false) {
            if (empty(array_filter($line))) {
                if ($hasStarted) {
                    break;
                }
                continue;
            }

            $description = trim($line[$map['description']] ?? '');
            $date        = trim($line[$map['date']] ?? '');
            $amount      = (float) str_replace([',', '$', '"'], '', trim($line[$map['amount']] ?? '0'));

            if ($description === '' || $amount == 0 || $this->parseDate($date) === null) {
                continue;
            }

            $hasStarted = true;
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

    private function normalizeMatchText(string $value): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', $value)));
    }

    private function matchesExpenseKey(string $description, string $keyword): bool
    {
        if ($keyword === '') {
            return false;
        }

        $pattern = '/(?<![a-z0-9])' . preg_quote($keyword, '/') . '(?![a-z0-9])/i';

        return preg_match($pattern, $description) === 1;
    }

    private function createExpenseKeyFromUnmatchedRow(array $row, ?int $supplierId): int
    {
        if (!empty($row['matched_key']) || empty($row['description']) || empty($row['category_id'])) {
            return 0;
        }

        $key = substr(trim($row['description']), 0, 255);
        if ($key === '' || $this->expenseKeyExists($key)) {
            return 0;
        }

        ExpenseKey::create([
            'key' => $key,
            'category_id' => $row['category_id'],
            'supplier_id' => $supplierId,
        ]);

        return 1;
    }

    private function expenseKeyExists(string $key): bool
    {
        $normalizedKey = $this->normalizeMatchText($key);

        return ExpenseKey::whereNotNull('key')
            ->get()
            ->contains(function ($expenseKey) use ($normalizedKey) {
                return $this->normalizeMatchText($expenseKey->key) === $normalizedKey;
            });
    }

    private function supplierBusinessName(?int $supplierId): ?string
    {
        if (!$supplierId) {
            return null;
        }

        $supplier = Supplier::find($supplierId);

        return $supplier ? $supplier->supplier_business_name : null;
    }

    private function isDuplicateExpense(?string $supplierBusinessName, ?string $date, array $seenImportRows): bool
    {
        if (!$supplierBusinessName || !$date) {
            return false;
        }

        $duplicateKey = $this->duplicateKey($supplierBusinessName, $date);
        if (in_array($duplicateKey, $seenImportRows)) {
            return true;
        }

        return Expense::where('expense_date', $date)
            ->whereHas('supplier', function ($query) use ($supplierBusinessName) {
                $query->where('supplier_business_name', $supplierBusinessName);
            })
            ->exists();
    }

    private function duplicateKey(string $supplierBusinessName, string $date): string
    {
        return $this->normalizeMatchText($supplierBusinessName) . '|' . $date;
    }
}
