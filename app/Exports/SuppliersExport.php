<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DB;

class SuppliersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function collection() {
        $suppliers = DB::table("suppliers")
            ->leftjoin("expense_categories",\DB::raw("FIND_IN_SET(expense_categories.id,suppliers.supplier_expense_category)"),">",DB::raw("'0'"))
            ->select('supplier_business_name', 'supplier_first_name', 'supplier_last_name', DB::raw("GROUP_CONCAT(expense_categories.name) as expense_categories"), 'supplier_mobile',
                'supplier_email', 'supplier_street_address_1', 'supplier_city', 'supplier_state', 'supplier_postalcode', 'supplier_country', 
                'shipping_street_address_1', 'shipping_city', 'shipping_state', 'shipping_postalcode', 'shipping_country', 'supplier_currency', 
                'supplier_notes', 'supplier_tags')
            ->where('suppliers.deleted_at', '=', null)
            ->groupBy('suppliers.id')
            ->get();
     
        $data = [];
        foreach($suppliers as $d=>$val) {
            $data[$d] = [];
            $data[$d]['supplier_business_name'] = $val->supplier_business_name;
            $data[$d]['supplier_first_name'] = $val->supplier_first_name;
            $data[$d]['supplier_last_name'] = $val->supplier_last_name;
            $data[$d]['expense_categories'] = $val->expense_categories;
            $data[$d]['supplier_mobile'] = $val->supplier_mobile;
            $data[$d]['supplier_email'] = $val->supplier_email;
            $data[$d]['supplier_street_address_1'] = $val->supplier_street_address_1;
            $data[$d]['supplier_city'] = $val->supplier_city;
            $data[$d]['supplier_state'] = $val->supplier_state;
            $data[$d]['supplier_postalcode'] = $val->supplier_postalcode;
            $data[$d]['supplier_country'] = $val->supplier_country;
            $data[$d]['shipping_street_address_1'] = $val->shipping_street_address_1;
            $data[$d]['shipping_city'] = $val->shipping_city;
            $data[$d]['shipping_state'] = $val->shipping_state;
            $data[$d]['shipping_postalcode'] = $val->shipping_postalcode;
            $data[$d]['shipping_country'] = $val->shipping_country;
            $data[$d]['supplier_currency'] = $val->supplier_currency;
            $string = strip_tags($val->supplier_notes);
            $content = str_replace("&nbsp;", " ", $string);
            $content = html_entity_decode($content);
            $data[$d]['supplier_notes'] = $content;
            $data[$d]['supplier_tags'] = $val->supplier_tags;
        }

        return collect($data);
    }

    public function headings(): array {
        return [
            'Supplier Business Name',
            'Supplier First Name',
            'Supplier Last Name',
            'Expense Categories',
            'Supplier Mobile',
            'Supplier Email',
            'Supplier Street Address 1',
            'Supplier City',
            'Supplier State',
            'Supplier Postalcode',
            'Supplier Country',
            'Shipping Street Address 1',
            'Shipping City',
            'Shipping State',
            'Shipping Postalcode',
            'Shipping Country',
            'Supplier Currency',
            'Supplier Notes',
            'Supplier Tags'
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
