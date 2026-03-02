<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPExcel_Cell_DataValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use DB;

class SampleExpenseExport implements WithHeadings, WithEvents, WithStrictNullComparison, ShouldAutoSize, WithColumnFormatting, WithStyles
{
    protected $results;

    public function registerEvents(): array {
        $this->results = $this->getDropDownValues();
        return [
            AfterSheet::class => function(AfterSheet $event) {

                /** Set dropdown list for PaymentMethods starts */
                $payment_methods = array_column($this->results['payment_methods']->toArray(), 'payment_method_name');
                $payment_method_drop_col = 'B';
                $this->setDropDownVales($event, $payment_methods, $payment_method_drop_col);
                /** Set dropdown list for PaymentMethods ends */
                
                /** Set dropdown list for Suppliers starts */ 
                $supplier_drop_column = 'C';
                $suppliers = array_column($this->results['suppliers']->toArray(), 'supplier_business_name');
                $this->setDropDownVales($event, $suppliers, $supplier_drop_column);
                /** Set dropdown list for Suppliers ends */ 

                /** Set dropdown list for ExpenseCategory starts */
                $expense_cat_drop_col = 'D';
                $expense_cat = array_column($this->results['expense_cat']->toArray(), 'name');
                $this->setDropDownVales($event, $expense_cat, $expense_cat_drop_col);
                /** Set dropdown list for ExpenseCategory ends */

                // Set dropdownlist for Project name
                 $project_name_drop_col = 'E';
                 $project_name = array_column($this->results['project_name']->toArray(), 'name');
                 $this->setDropDownVales($event, $project_name, $project_name_drop_col);
                 /** Set dropdown list for ProjectName ends */

                /** Set dropdown list for Tax starts */
                $tax_drop_col = 'F';
                $tax_options = ['GST Inclusive', 'No GST'];
                $this->setDropDownVales($event, $tax_options, $tax_drop_col);
                /** Set dropdown list for Tax ends */
                
            },
        ];
    }

    private function setDropDownVales($event, $options, $drop_col) {
        $validation = $event->sheet->getCell("{$drop_col}1")->getDataValidation();
        $validation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST );
        $validation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Value is not in list.');
        $validation->setPromptTitle('Pick from list');
        $validation->setPrompt('Please pick a value from the drop-down list.');
        $str = sprintf('%s',implode(',',$options));
        //$str = '"'.str_replace(',', '","', $str).'"';
        $str = '"'. $str .'"';
        $validation->setFormula1($str);
        for ($i = 2; $i <= 100; $i++) {
            $event->sheet->getCell("{$drop_col}{$i}")->setDataValidation(clone $validation);
        }
        $column = Coordinate::stringFromColumnIndex(1);
        $event->sheet->getColumnDimension($column)->setAutoSize(true);
    }

    private function getDropDownValues() {
        $payment_methods = DB::table('payment_methods')->select('payment_method_name')->orderBy('payment_method_name', 'asc')->where('is_status', 1)->where('deleted_at', '=', null)->get();
        $suppliers = DB::table('suppliers')->select('supplier_business_name')->orderBy('supplier_business_name', 'asc')->where('is_status', 1)->where('deleted_at', '=', null)->get();
        $expense_cat = DB::table('expense_categories')->select('name')->orderBy('name')->where('is_status', 1)->where('deleted_at', '=', null)->get();
        $project=DB::table('projects')->select('name')->orderBy('id')->where('is_status',1)->where('deleted_at',"=",null)->get();
        $return = [
            'payment_methods' => $payment_methods,
            'suppliers' => $suppliers,
            'expense_cat' => $expense_cat,
            'project_name'=>$project
        ];

        return $return;
    }
    
    public function columnFormats(): array {
        return [
            'G' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
    }

    public function headings(): array {
        return [
            'Supplier Invoice Number',
            'Select Payment Method',
            'Select Supplier',
            'Select Supplier Category',
            'Project Name',
            'Select Tax',
            'Amount',
            'Payment Date',
            'Description'
        ];
    }
}
