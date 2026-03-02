<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\ExpenseCategory;

class SuppliersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $categories = explode(",",$row['supplierexpensecategory']);
        $result = "'" . implode ( "', '", $categories ) . "'";
        $expense_categories = ExpenseCategory::whereRaw('name IN ('.$result.')')->select('id')->get();
        $single_arr = array_column($expense_categories->toArray(), 'id');
        $expense_categories_id = implode(",",$single_arr);

        if(isset($row['is_shipping_address_same']) && $row['is_shipping_address_same']) {
            $add_shipping_address = 'same_as_billing';
            $shipping_street_address_1 = $row['billing_street_address_1'];
            $shipping_city = $row['billing_city'];
            $shipping_state = $row['billing_state'];
            $shipping_postalcode = $row['billing_postalcode'];
        }else {
            $add_shipping_address = 'shipping_address_diff';
            $shipping_street_address_1 = $row['shipping_street_address_1'];
            $shipping_city = $row['shipping_city'];
            $shipping_state = $row['shipping_state'];
            $shipping_postalcode = $row['shipping_postalcode'];
        }
     
        return new Supplier([
            'supplier_business_name' => $row['businessname'], 
            'supplier_expense_category' => $expense_categories_id, 
            'supplier_first_name' => $row['firstname'], 
            'supplier_last_name' => $row['lastname'], 
            'supplier_mobile' => $row['mobile'], 
            'supplier_street_address_1' => $row['billing_street_address_1'],
            'supplier_city' => $row['billing_city'], 
            'supplier_state' => $row['billing_state'], 
            'supplier_postalcode' => $row['billing_postalcode'], 
            'add_shipping_address' => $add_shipping_address, 
            'shipping_street_address_1' => $shipping_street_address_1, 
            'shipping_city' => $shipping_city,
            'shipping_state' => $shipping_state, 
            'shipping_postalcode' => $shipping_postalcode, 
            'supplier_email' => $row['remittance_email'], 
            'supplier_notes' => $row['notes'], 
            'supplier_tags' => $row['tags'],
            'is_status' => 1
        ]);
    }
}
