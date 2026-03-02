<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DB;

class ProductsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        
        $products = DB::table('products')
            ->leftjoin('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.product_name', 'categories.name as product_category', 'products.product_price', 'products.product_purchase_price', 'products.product_margin', 'products.product_tax as product_tax_type', 
                'products.product_description')
            ->where('products.deleted_at', '=', null)
            ->get();

        $data = [];
        foreach($products as $key => $value) {
            $data[$key] = [];
            $data[$key]['product_name'] = $value->product_name;
            $data[$key]['product_category'] = $value->product_category;
            $data[$key]['product_price'] = getPrice($value->product_price, 'N');
            $data[$key]['product_purchase_price'] = getPrice($value->product_purchase_price, 'N');
            $data[$key]['product_margin'] = getPrice($value->product_margin, 'N');
            $data[$key]['product_tax_type'] = $value->product_tax_type;
            $data[$key]['product_description'] = $value->product_description;

        }
     
        return collect($data);
    }

    public function headings(): array {
        return [
            'Product Name',
            'Product Category',
            'Product Price',
            'Purchase Price',
            'Margin',
            'Product Tax Type',
            'Product Description'
        ];
    }

    public function columnFormats(): array {
        return [
            'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'D' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'E' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:G1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('70AD47');
            },
        ];
    }
}
