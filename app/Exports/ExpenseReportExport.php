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
use Illuminate\Support\Facades\DB;


class ExpenseReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    use Exportable;

    function __construct($params) {
        $this->params = $params;
    }

    public function collection() {
        
      
        $expense_year=explode(" - ",$this->params['expense_year']);
        $expense_arr = [];
        $start_date =  $expense_year[0].'-07-01';
        $end_date =  $expense_year[1].'-06-30';

        $totalAmount = 0;


        $expensesQuery = Expense::with(['supplier', 'payment_method'])
        ->leftJoin('projects', 'expenses.project_id', '=', 'projects.id')
        ->whereBetween('expense_date', [$start_date, $end_date])
        ->select('expenses.*', 'projects.name as exp_project_name');

        if(isset($this->params['category_id']) && !empty($this->params['category_id'])){
            $category_ids=$this->params['category_id'];
            $category_ids_array = explode(",", $category_ids);
            $expensesQuery->whereIn('supplier_expense_category', $category_ids_array);
        }
        // if ($category_ids_array !== null && is_array($category_ids_array) && count($category_ids_array) > 0) {
        //     $expensesQuery->whereIn('supplier_expense_category', $category_ids_array);
        // }
        //->get();
        $expenses = $expensesQuery->get();
        $expense_arr = $expenses->toArray();
        $final_arr = [];
        $categoryNames = [];
        $categoryWiseTotal = [];
        foreach($expense_arr as $key=>$value) {
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

            $categoryIds = explode(",", $expenses[$key]->supplier_expense_category);
            $categories = DB::table('expense_categories')
                ->whereIn('id', $categoryIds)
                ->pluck('name')
                ->toArray();

            $categoryNames[$key] = implode(', ', $categories);
            
             // Add category names to each row
            $final_arr[$key]['category_label'] = implode(', ', $categories);
            $final_arr[$key]['category_label'] = 'Your Category Label Here';

            $amount = isset($expenses[$key]) ? $expenses[$key]->expense_amount : 0;
            $totalAmount += $amount;
           
            foreach ($categories as $category) {
                if (!isset($categoryWiseTotal[$category])) {
                    $categoryWiseTotal[$category] = 0;
                }
                $categoryWiseTotal[$category] += $amount;
            }
          
        }
        $totalRow = [
            'Expense Payment Date' => 'Total Amount',
            'Supplier' => '',
            'Expense Description' => '',
            'Supplier Invoice No' => '',
            'Amount' => getPrice($totalAmount, 'N'), // Format the total amount
            'GST' => '',
            'GST Paid' => '',
            'Payment Method' => '',
            'Tax Type' => '', // Label for the total row
            'Expense Category' => '',
            'Project Name' => '',
        ];

        $final_arr[] = $totalRow;
        //echo count($final_arr); exit;
        
        $totalRow = [
            'Expense Payment Date' => '',
            'Supplier' => '',
            'Expense Description' => '',
            'Supplier Invoice No' => '',
            'Amount' =>'' , // Format the total amount
            'GST' => '',
            'GST Paid' => '',
            'Payment Method' => '',
            'Tax Type' => '', // Label for the total row
            'Expense Category' => '',
            'Project Name' => '',
        ];
        $final_arr[] = $totalRow;

       //$allCategoryNames = implode(', ', array_unique($categoryNames));
       $totalRow = [
        'Expense Payment Date' => 'Category',
        'Supplier' => '',
        'Expense Description' => '',
        'Supplier Invoice No' => '',
        'Amount' =>'' , // Format the total amount
        'GST' => '',
        'GST Paid' => '',
        'Payment Method' => '',
        'Tax Type' => '', // Label for the total row
        'Expense Category' => '',
        'Project Name' => '',
    ];
    $final_arr[] = $totalRow;
        foreach(array_unique($categoryNames) as $key=>$value) {
            $totalRow = [
                'Expense Payment Date' => $value,
                'Supplier' => $categoryWiseTotal[$value],
                'Expense Description' => '',
                'Supplier Invoice No' => '',
                'Amount' =>'' , // Format the total amount
                'GST' => '',
                'GST Paid' => '',
                'Payment Method' => '',
                'Tax Type' => '', // Label for the total row
                'Expense Category' => '',
                'Project Name' => '',
            ];
            $final_arr[] = $totalRow;
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
        ];
    }

    public function columnFormats(): array {
        return [
            'E' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'G' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            
        ];
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:K1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('548135');

                
                    
            },
        ];
    }
}
