<?php

namespace App\Imports;

use App\Models\ExpenseCategory;
use App\Models\ExpenseKey;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExpenseKeysImport implements WithMultipleSheets
{
    public $imported = 0;
    public $updated = 0;
    public $skipped = 0;
    public $createdCategories = 0;
    public $createdSuppliers = 0;

    public function sheets(): array
    {
        return [
            // 0 => new ExpenseKeysSupplierSummarySheetImport($this),
            'Supplier Summary' => new ExpenseKeysSupplierSummarySheetImport($this),
        ];
    }

    public function importSupplierSummary(Collection $rows)
    {
        foreach ($rows as $row) {
            $supplierName = $this->value($row, ['supplier', 'supiler', 'vendor']);
            $keywords     = $this->keywords($this->value($row, ['possible_key_words', 'key_words', 'keywords', 'key_word']));
            $categoryName = $this->value($row, ['default_expense_categories', 'expense_category', 'category']);

            if ($supplierName === '' || empty($keywords) || $categoryName === '') {
                $this->skipped++;
                continue;
            }

            $category = $this->firstOrCreateCategory($categoryName);
            $supplier = $this->firstOrCreateSupplier($supplierName, $category->id);

            foreach ($keywords as $keyword) {
                $this->createOrUpdateExpenseKey($keyword, $category->id, $supplier->id);
            }
        }
    }

    private function createOrUpdateExpenseKey(string $key, int $categoryId, int $supplierId): void
    {
        $expenseKey = ExpenseKey::withTrashed()
            ->where('key', $key)
            ->where('supplier_id', $supplierId)
            ->first();

        if ($expenseKey) {
            $expenseKey->category_id = $categoryId;
            $expenseKey->supplier_id = $supplierId;
            if ($expenseKey->trashed()) {
                $expenseKey->restore();
            }
            $expenseKey->save();
            $this->updated++;
        } else {
            ExpenseKey::create([
                'key' => $key,
                'category_id' => $categoryId,
                'supplier_id' => $supplierId,
            ]);
        }

        $this->imported++;
    }

    private function firstOrCreateCategory(string $name): ExpenseCategory
    {
        $category = ExpenseCategory::withTrashed()
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();

        if ($category) {
            if ($category->trashed()) {
                $category->restore();
            }
            if ((int) $category->is_status !== 1) {
                $category->is_status = 1;
                $category->save();
            }
            return $category;
        }

        $this->createdCategories++;

        return ExpenseCategory::create([
            'name' => $name,
            'is_status' => 1,
        ]);
    }

    private function firstOrCreateSupplier(string $name, int $categoryId): Supplier
    {
        $supplier = Supplier::withTrashed()
            ->whereRaw('LOWER(supplier_business_name) = ?', [strtolower($name)])
            ->first();

        if ($supplier) {
            if ($supplier->trashed()) {
                $supplier->restore();
            }
        } else {
            $supplier = Supplier::create([
                'supplier_business_name' => $name,
                'supplier_expense_category' => (string) $categoryId,
                'supplier_first_name' => '',
                'supplier_last_name' => '',
                'is_status' => 1,
            ]);
            $this->createdSuppliers++;
            return $supplier;
        }

        $categoryIds = array_filter(explode(',', (string) $supplier->supplier_expense_category));
        if (!in_array((string) $categoryId, $categoryIds, true)) {
            $categoryIds[] = (string) $categoryId;
            $supplier->supplier_expense_category = implode(',', $categoryIds);
        }
        $supplier->is_status = 1;
        $supplier->save();

        return $supplier;
    }

    private function value(Collection $row, array $keys): string
    {
        foreach ($keys as $key) {
            $value = $row->get($key);
            if ($value !== null && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        return '';
    }

    private function keywords(string $value): array
    {
        $keywords = array_map('trim', explode(',', $value));
        $keywords = array_filter($keywords, function ($keyword) {
            return $keyword !== '';
        });

        return array_values(array_unique($keywords));
    }

}

class ExpenseKeysSupplierSummarySheetImport implements ToCollection, WithHeadingRow
{
    private $import;

    public function __construct(ExpenseKeysImport $import)
    {
        $this->import = $import;
    }

    public function collection(Collection $rows)
    {
        $this->import->importSupplierSummary($rows);
    }
}
