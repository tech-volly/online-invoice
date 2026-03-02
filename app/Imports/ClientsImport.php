<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
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
        
        if(isset($row['customernumber']) && $row['customernumber']) {
            $customer_number = $row['customernumber'];
        }else {
            $customer_number = null;
        }

        return new Client([
            'client_number' => $customer_number,
            'client_business_name' => $row['clientbusinessname'], 
            'client_first_name' => $row['firstname'], 
            'client_last_name' => $row['lastname'], 
            'client_mobile' => $row['mobile'], 
            'client_street_address_1' => $row['billing_street_address_1'],
            'client_city' => $row['billing_city'], 
            'client_state' => $row['billing_state'], 
            'client_postalcode' => $row['billing_postalcode'], 
            'add_shipping_address' => $add_shipping_address, 
            'shipping_street_address_1' => $shipping_street_address_1, 
            'shipping_city' => $shipping_city,
            'shipping_state' => $shipping_state, 
            'shipping_postalcode' => $shipping_postalcode, 
            'client_email' => $row['accounts_email'], 
            'client_notes' => $row['notes'],
            'client_tags' => $row['tags'],
            'client_invoicing_method' => 'send_via_email',
            'is_status' => 1
        ]);
    }
}
