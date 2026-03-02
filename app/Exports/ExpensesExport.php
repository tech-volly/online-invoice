<?php

namespace App\Exports;

use App\Models\Expense;
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

class ExpensesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $expenses = DB::table('expenses')
            ->leftjoin('payment_methods', 'payment_methods.id', '=', 'expenses.payment_method_id')
            ->leftjoin('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')
            ->leftjoin('expense_categories', 'expense_categories.id', '=', 'expenses.supplier_expense_category')
            ->leftJoin('projects', 'expenses.project_id', '=', 'projects.id')
            ->where('expenses.deleted_at', '=', null)
            ->select('suppliers.supplier_business_name as business_name', 'expenses.supplier_invoice_number as invoice_number', 
                DB::raw('DATE_FORMAT(expenses.expense_date, "%d-%m-%Y") as payment_date'),
                'expenses.expense_amount as amount', 'payment_methods.payment_method_name as payment_method', 'expense_categories.name as expense_category_name',
                'expenses.expense_tax as tax_type', 'expenses.expense_description','projects.name as project_name')
            ->orderBy('expenses.expense_date', 'ASC')
            ->get();

        $data = [];
        foreach($expenses as $key=>$value) { 
            $data[$key] = [];
            $data[$key]['payment_date'] = changeDateFormatAtExport($value->payment_date);
            $data[$key]['business_name'] = $value->business_name;
            $data[$key]['expense_description'] = $value->expense_description;
            $data[$key]['project_name'] = $value->project_name;
            $data[$key]['invoice_number'] = $value->invoice_number;
            $data[$key]['amount'] = $value->amount;
            $data[$key]['gst_paid'] = getGstPriceForExpense($value->tax_type, $value->amount, "N");
            $data[$key]['payment_method'] = $value->payment_method;
            $data[$key]['tax_type'] = $value->tax_type;
            $data[$key]['expense_category'] = $value->expense_category_name;
        }   


        return collect($data);
    }

    public function headings(): array {
        return [
            'Payment Date',
            'Business Name',
            'Expense Description',
            'Project Name',
            'Invoice Number',
            'Amount',
            'GST Paid',
            'Payment Method',
            'Tax Type',
            'Expense Category'
        ];
    }

    public function columnFormats(): array {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'G' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
        ];
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:J1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('70AD47');
            },
        ];
    }
}
