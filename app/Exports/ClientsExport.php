<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ClientsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    
    public function collection() {
        $clients = Client::select('client_number', 'client_business_name', 'client_first_name', 'client_last_name', 'client_mobile',
            'client_email as accounts_email', 'client_street_address_1', 'client_city', 'client_state', 'client_postalcode', 'client_country', 'shipping_street_address_1', 
            'shipping_city', 'shipping_state', 'shipping_postalcode', 'shipping_country', 'client_invoicing_method', 'client_currency',
            'client_notes'
        )->get();

        foreach($clients as $d=>$val) {
            $data[$d] = [];
            $data[$d]['client_number'] = $val->client_number;
            $data[$d]['client_business_name'] = $val->client_business_name;
            $data[$d]['client_first_name'] = $val->client_first_name;
            $data[$d]['client_last_name'] = $val->client_last_name;
            $data[$d]['client_mobile'] = $val->client_mobile;
            $data[$d]['accounts_email'] = $val->accounts_email;
            $data[$d]['client_street_address_1'] = $val->client_street_address_1;
            $data[$d]['client_city'] = $val->client_city;
            $data[$d]['client_state'] = $val->client_state;
            $data[$d]['client_postalcode'] = $val->client_postalcode;
            $data[$d]['client_country'] = $val->client_country;
            $data[$d]['shipping_street_address_1'] = $val->shipping_street_address_1;
            $data[$d]['shipping_city'] = $val->shipping_city;
            $data[$d]['shipping_state'] = $val->shipping_state;
            $data[$d]['shipping_postalcode'] = $val->shipping_postalcode;
            $data[$d]['shipping_country'] = $val->shipping_country;
            $data[$d]['client_invoicing_method'] = $val->client_invoicing_method;
            $data[$d]['client_currency'] = $val->client_currency;
            $string = strip_tags($val->client_notes);
            $content = str_replace("&nbsp;", " ", $string);
            $content = html_entity_decode($content);
            $data[$d]['client_notes'] = $content;
        }
        
        return collect($data);
    }

    public function headings(): array {
        return [
            'Client Number',
            'Client Business Name',
            'Client First Name',
            'Client Last Name',
            'Client Mobile',
            'Accounts Email',
            'Client Street Address 1',
            'Client City',
            'Client State',
            'Client Postalcode',
            'Client Country',
            'Shipping Street Address 1',
            'Shipping City',
            'Shipping State',
            'Shipping Postalcode',
            'Shipping Country',
            'Client Invoice Method',
            'Client Currency',
            'Client Notes'
        ];
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:S1')->getFont()->setBold(true);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:S1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('70AD47');
            },
        ];
    }
}
