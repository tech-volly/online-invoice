<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Models\Expense;
use App\Models\Invoice;

class PNLReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    use Exportable;

    function __construct($params) {
        $this->params = $params;
    }

    public function collection() {
        $from_date = chnageDateFormat($this->params['from_date']);
        $to_date = chnageDateFormat($this->params['to_date']);
        $income_expense_arr = [];
        //$incomes = Invoice::with(['client', 'payment_status'])->whereBetween('invoice_payment_date', [$from_date, $to_date])->orderBy('invoice_payment_date', 'asc')->get();
        $incomes = Invoice::with(['client', 'payment_status'])
        ->leftJoin('projects', 'invoices.project_id', '=', 'projects.id')
        ->whereBetween('invoice_payment_date', [$from_date, $to_date])
        ->select('invoices.*', 'projects.name as inc_project_name')
        ->orderBy('invoice_payment_date', 'asc')
        ->get();
       // $expenses = Expense::with(['supplier', 'payment_method'])->whereBetween('expense_date', [$from_date, $to_date])->get();
       $expenses = Expense::with(['supplier', 'payment_method'])
                        ->leftJoin('projects', 'expenses.project_id', '=', 'projects.id')
                        ->whereBetween('expense_date', [$from_date, $to_date])
                        ->select('expenses.*', 'projects.name as exp_project_name')
                        ->get();
        
        if(count($incomes) > count($expenses)) {
            $income_expense_arr = $incomes->toArray();
        }else {
            $income_expense_arr = $expenses->toArray();
        }
        $final_arr = [];
        foreach($income_expense_arr as $key=>$value) {
            $final_arr[$key]['expense_payment_date'] = isset($expenses[$key]) ? ($expenses[$key]->expense_date ? changeDateFormatAtExport($expenses[$key]->expense_date) : '') : '';
            $final_arr[$key]['supplier'] = isset($expenses[$key]) ? $expenses[$key]->supplier->supplier_business_name : '';
            $final_arr[$key]['expense_description'] = isset($expenses[$key]) ? $expenses[$key]->expense_description : '';
            $final_arr[$key]['supplier_invoice_number'] = isset($expenses[$key]) ? $expenses[$key]->supplier_invoice_number : '';
            $final_arr[$key]['expense_amount'] = isset($expenses[$key]) ? getPrice($expenses[$key]->expense_amount, 'N') : '';
            $final_arr[$key]['expense_gst'] = isset($expenses[$key]) ? ($expenses[$key]->expense_tax == 'GST Inclusive' || $expenses[$key]->expense_tax == 'GST' ? 'Yes' : 'No')  : '' ;
            $final_arr[$key]['expense_gst_paid'] = isset($expenses[$key]) ? getGstPriceForExpense($expenses[$key]->expense_tax, $expenses[$key]->expense_amount, 'N') : null;
            $final_arr[$key]['expense_payment_method'] = isset($expenses[$key]) ? $expenses[$key]->payment_method->payment_method_name : '';
            $final_arr[$key]['tax_type'] = isset($expenses[$key]) ? $expenses[$key]->expense_tax : '';
            $final_arr[$key]['expense_category'] = isset($expenses[$key]) ? getExpenseCategory($expenses[$key]->supplier_expense_category) : '';
            $final_arr[$key]['expense_project_name'] = isset($expenses[$key]) ? $expenses[$key]->exp_project_name : '';
            $final_arr[$key]['blank_col'] = null;
            $final_arr[$key]['income_payment_date'] = isset($incomes[$key]) ? ($incomes[$key]->invoice_payment_date ? changeDateFormatAtExport($incomes[$key]->invoice_payment_date) : '') : '';
            $final_arr[$key]['client'] = isset($incomes[$key]) ? $incomes[$key]->client->client_business_name : '';
            $final_arr[$key]['notes'] = isset($incomes[$key]) ? $incomes[$key]->invoice_notes : '';
            $final_arr[$key]['invoice_number'] = isset($incomes[$key]) ? $incomes[$key]->invoice_number : '';
            $final_arr[$key]['invoice_grand_total'] = isset($incomes[$key]) ? getPrice($incomes[$key]->invoice_grand_total, 'N') : '';
            $final_arr[$key]['invoice_gst'] = isset($incomes[$key]) ? getPrice($incomes[$key]->invoice_grand_gst, 'N') : '';
            $final_arr[$key]['invoice_product_category'] = isset($incomes[$key]) ? getInvoiceProductCategories($incomes[$key]->id) : '';
            $final_arr[$key]['invoice_project_name'] = isset($incomes[$key]) ? $incomes[$key]->inc_project_name : '' ;
            $final_arr[$key]['invoice_date'] = isset($incomes[$key]) ? ($incomes[$key]->invoice_date ? changeDateFormatAtExport($incomes[$key]->invoice_date) : '') : '';
            $final_arr[$key]['invoice_due_date'] = isset($incomes[$key]) ? ($incomes[$key]->invoice_due_date ? changeDateFormatAtExport($incomes[$key]->invoice_due_date) : '') : '';
        }

        return collect($final_arr);
    }

    public function headings(): array {
        return [
            'Expense Payment Date',
            'Supplier',
            'Expense Description',
            'Supplier Invoice No',
            'Amount',
            'GST',
            'GST Paid',
            'Payment Method',
            'Tax Type',
            'Expense Category',
            'Project Name',
            '',
            'Invoice Payment Date',
            'Client',
            'Notes',
            'Invoice No',
            'Grand Total',
            'GST Component',
            'Product Category',
            'Project Name',
            'Invoice Date',
            'Invoice Due Date'
        ];
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:V1')->getFont()->setBold(true);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:V1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('548135');
                    $event->sheet->getDelegate()->getStyle('L1:L1000')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('CE181E');
            },
        ];
    }

    public function columnFormats(): array {
        return [
            'E' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'G' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'Q' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'R' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'M' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'U' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'V' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }
}
