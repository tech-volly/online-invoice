<?php

namespace App\Imports;

use App\Models\ExpenseCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExpenseCategoryImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ExpenseCategory([
            'name'     => $row['name'],
            'is_status'    => $row['is_status']
        ]);
    }
}
