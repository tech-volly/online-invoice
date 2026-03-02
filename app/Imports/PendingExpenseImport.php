<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\PendingExpense;

class PendingExpenseImport implements ToCollection, ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */

    public function model(array $row) { 
        return new PendingExpense([
            'expense_amount' => $row['expense_amount'],
            'expense_description' => $row['expense_description'], 
            'expense_date' => chnageDateFormat($row['expense_date']), 
        ]);


    }
    
}
