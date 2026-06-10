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
        $suppliers       = Supplier::whereNull('deleted_at')->orderBy('supplier_business_name', 'asc')->get();
        $categories      = ExpenseCategory::whereNull('deleted_at')->orderBy('name', 'asc')->get();
        $payment_methods = PaymentMethod::orderBy('payment_method_name', 'asc')->whereIsStatus(1)->get();

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

        Log::info('ImportExpenseFromKey save started.', [
            'total_rows' => count($rows),
        ]);

        foreach ($rows as $i => $row) {

            // Row was manually skipped by user
            if (!empty($row['skip']) && $row['skip'] == 1) {
                Log::info('ImportExpenseFromKey row skipped by user.', [
                    'row' => $i + 1,
                    'description' => $row['description'] ?? null,
                ]);
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
                $expenseAmount = abs($amount);
                $date   = $this->parseDate($row['date'] ?? '');
                $description = $this->cleanDescriptionSpacing($row['description'] ?? '');
                $supplierId = !empty($row['supplier_id']) ? $row['supplier_id'] : null;
                $duplicateKey = $date ? $this->duplicateKey($description, $date, $expenseAmount) : null;

                Log::info('ImportExpenseFromKey row duplicate check.', [
                    'row' => $i + 1,
                    'supplier_id' => $supplierId,
                    'date' => $date,
                    'amount' => $expenseAmount,
                    'raw_description' => $row['description'] ?? null,
                    'normalized_description' => $description,
                    'duplicate_key' => $duplicateKey,
                ]);

                if ($this->isDuplicateExpense($supplierId, $date, $expenseAmount, $seenImportRows, $description, $i + 1)) {
                    $duplicateRows[] = $i + 1;
                    $skipped++;
                    continue;
                }

                if ($date) {
                    $seenImportRows[] = $this->duplicateKey($description, $date, $expenseAmount);
                }
                $createdKeys += $this->createExpenseKeyFromUnmatchedRow($row, $supplierId);

                // Use new Expense() + individual assignment + save()
                // This is identical to how ExpenseController::addExpenseAction works
                // and avoids any $fillable issues entirely.
                $expense = new Expense();
                $expense->supplier_invoice_number   = substr($description, 0, 100);
                $expense->payment_method_id         = $row['payment_method_id'];
                $expense->supplier_id               = $supplierId;
                $expense->supplier_expense_category = $row['category_id'];
                $expense->expense_tax               = $row['tax'] ?? 'GST Inclusive';
                $expense->expense_amount            = $expenseAmount;
                $expense->expense_date              = $date;
                $expense->expense_description       = $description ?: null;
                $expense->is_status                 = 1;

                $expense->save();
                Log::info('ImportExpenseFromKey row saved.', [
                    'row' => $i + 1,
                    'expense_id' => $expense->id,
                    'supplier_id' => $supplierId,
                    'date' => $date,
                    'amount' => $expenseAmount,
                    'description' => $description,
                ]);
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

        Log::info('ImportExpenseFromKey save completed.', [
            'saved' => $saved,
            'skipped' => $skipped,
            'duplicate_rows' => $duplicateRows,
            'created_keys' => $createdKeys,
            'row_errors' => $rowErrors,
        ]);

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

            $description = $this->cleanDescriptionSpacing($line[$map['description']] ?? '');
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
        return strtolower($this->cleanDescriptionSpacing($value));
    }

    private function cleanDescriptionSpacing(string $value): string
    {
        $value = str_replace(["\xc2\xa0", "\xe2\x80\xaf"], ' ', $value);

        return trim(preg_replace('/\s+/u', ' ', $value));
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

        $key = substr($this->cleanDescriptionSpacing($row['description']), 0, 255);
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

    private function isDuplicateExpense(?int $supplierId, ?string $date, float $amount, array $seenImportRows, string $description = '', ?int $rowNumber = null): bool
    {
        if (!$date) {
            Log::info('ImportExpenseFromKey duplicate check skipped because date is empty.', [
                'row' => $rowNumber,
            ]);
            return false;
        }

        if (!$description && !$supplierId) {
            Log::info('ImportExpenseFromKey duplicate check skipped because supplier and description are empty.', [
                'row' => $rowNumber,
            ]);
            return false;
        }

        $duplicateKey = $this->duplicateKey($description ?: $supplierId, $date, $amount);
        if (in_array($duplicateKey, $seenImportRows)) {
            Log::info('ImportExpenseFromKey duplicate found within uploaded rows.', [
                'row' => $rowNumber,
                'duplicate_key' => $duplicateKey,
            ]);
            return true;
        }

        $normalizedDescription = $this->normalizeMatchText($description);
        $amountFrom = round($amount - 0.01, 2);
        $amountTo = round($amount + 0.01, 2);

        $candidates = Expense::where('expense_date', $date)
            ->whereBetween('expense_amount', [$amountFrom, $amountTo])
            ->get()
            ->filter(function ($expense) use ($supplierId, $normalizedDescription) {
                $descriptionMatches = $normalizedDescription !== ''
                    && (
                        $this->normalizeMatchText((string) $expense->expense_description) === $normalizedDescription
                        || $this->normalizeMatchText((string) $expense->supplier_invoice_number) === $normalizedDescription
                    );

                if ($descriptionMatches) {
                    return true;
                }

                return $supplierId && (int) $expense->supplier_id === (int) $supplierId;
            });

        $existingExpense = $candidates->first();

        Log::info('ImportExpenseFromKey normalized duplicate DB check.', [
            'row' => $rowNumber,
            'supplier_id' => $supplierId,
            'date' => $date,
            'amount' => $amount,
            'amount_from' => $amountFrom,
            'amount_to' => $amountTo,
            'normalized_description' => $normalizedDescription,
            'candidate_ids' => $candidates->pluck('id')->values()->all(),
            'existing_expense_id' => $existingExpense ? $existingExpense->id : null,
        ]);

        return $existingExpense !== null;
    }

    private function duplicateKey($matchValue, string $date, float $amount): string
    {
        return $this->normalizeMatchText((string) $matchValue) . '|' . $date . '|' . number_format($amount, 2, '.', '');
    }
}
