<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientTempImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public $data;
    
    public function collection(Collection $row) {
        $this->data = $row;
    }
}
