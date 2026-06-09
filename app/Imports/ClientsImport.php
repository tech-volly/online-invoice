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
            $shipping_street_address_1 = $this->rowValue($row, ['billing_street_address_1', 'client_street_address_1']);
            $shipping_city = $this->rowValue($row, ['billing_city', 'client_city']);
            $shipping_state = $this->rowValue($row, ['billing_state', 'client_state']);
            $shipping_postalcode = $this->rowValue($row, ['billing_postalcode', 'client_postalcode']);
        }else {
            $add_shipping_address = 'shipping_address_diff';
            $shipping_street_address_1 = $this->rowValue($row, ['shipping_street_address_1']);
            $shipping_city = $this->rowValue($row, ['shipping_city']);
            $shipping_state = $this->rowValue($row, ['shipping_state']);
            $shipping_postalcode = $this->rowValue($row, ['shipping_postalcode']);
        }
        
        $customer_number = $this->rowValue($row, ['customernumber', 'client_number']);
        if(!$customer_number) {
            $customer_number = null;
        }

        $reminder_day = $this->rowValue($row, ['reminder_days', 'reminder_day']);
        if(!$reminder_day) {
            $reminder_day = 15;
        }

        $client_notes = $this->rowValue($row, ['notes', 'client_notes']);
        $client_tags = $this->rowValue($row, ['tags', 'client_tags']);
        if(!$client_tags) {
            $client_tags = null;
        }

        $client_currency = $this->rowValue($row, ['client_currency']) ?: null;

        return new Client([
            'client_number' => $customer_number,
            'client_business_name' => $this->rowValue($row, ['clientbusinessname', 'client_business_name']), 
            'client_first_name' => $this->rowValue($row, ['firstname', 'client_first_name']), 
            'client_last_name' => $this->rowValue($row, ['lastname', 'client_last_name']), 
            'client_mobile' => $this->rowValue($row, ['mobile', 'client_mobile']), 
            'client_street_address_1' => $this->rowValue($row, ['billing_street_address_1', 'client_street_address_1']),
            'client_city' => $this->rowValue($row, ['billing_city', 'client_city']), 
            'client_state' => $this->rowValue($row, ['billing_state', 'client_state']), 
            'client_postalcode' => $this->rowValue($row, ['billing_postalcode', 'client_postalcode']), 
            'add_shipping_address' => $add_shipping_address, 
            'shipping_street_address_1' => $shipping_street_address_1, 
            'shipping_city' => $shipping_city,
            'shipping_state' => $shipping_state, 
            'shipping_postalcode' => $shipping_postalcode, 
            'client_email' => $this->rowValue($row, ['accounts_email', 'invoice_email', 'client_email']), 
            'client_quotes_email' => $this->rowValue($row, ['quote_email', 'quotes_email', 'client_quotes_email']),
            'client_statement_email' => $this->rowValue($row, ['statement_email', 'client_statement_email']),
            'reminder_day' => $reminder_day,
            'client_notes' => $client_notes,
            'client_tags' => $client_tags,
            'client_invoicing_method' => 'send_via_email',
            'client_currency' => $client_currency,
            'is_status' => 1
        ]);
    }

    private function rowValue(array $row, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && $row[$key] !== '') {
                return $row[$key];
            }
        }

        return null;
    }
}
