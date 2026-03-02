<?php

namespace App\Imports;

use App\Models\PaymentMethod;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PaymentMethodsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new PaymentMethod([
            'payment_method_name' => $row['payment_method_name'],
            'is_status'    => $row['is_status']
        ]);
    }
}
