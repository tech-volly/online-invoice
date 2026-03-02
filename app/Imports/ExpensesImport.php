<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExpensesImport implements ToCollection, WithHeadingRow
{
    
    public $data;

    public function collection(Collection $rows) {
        $this->data = $rows;

        foreach($this->data as $key => $value) {

            $this->data[$key]['payment_date'] = isset($this->data[$key]['payment_date']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($this->data[$key]['payment_date'])->format('Y-m-d') : null;
        }
        
    }
}
