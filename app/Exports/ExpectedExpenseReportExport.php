<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Models\ExpectedExpense;
use App\Models\Invoice;
use App\Models\Subscription;
use Carbon\Carbon;

class ExpectedExpenseReportExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithStyles, WithEvents
{
    
    use Exportable;
    
    function __construct($params) {
        $this->params = $params;
    }
    
    public function collection() {
        $expenses = ExpectedExpense::with(['epxected_expense_list'])->where('expected_expense_year', $this->params['expected_expense_year'])->first();
        $annual_expense = 0;
        $total_fixed_expense = 0;
        $final_arr = [];
        $final_arr[0] = ['Expenses', 'Amount (Annually)'];
    
        $year_split = explode('- ', $this->params['expected_expense_year']);
        $current_year = $expenses->selected_year;
        $next_year = $current_year + 1;
        $q1_start_date = $current_year.'-07-01';
        $q4_end_date = $next_year.'-06-30';


        foreach($expenses->epxected_expense_list as $key => $expense_list) {
            if($expense_list->expected_annual_expense) {
                $annual_expense = $expense_list->expected_annual_expense;
                $total_fixed_expense += $annual_expense;
            }else {
                $annual_expense = $expense_list->expected_july_expense + $expense_list->expected_aug_expense + $expense_list->expected_sept_expense + 
                $expense_list->expected_oct_expense + $expense_list->expected_nov_expense + $expense_list->expected_desc_expense +
                $expense_list->expected_jan_expense + $expense_list->expected_feb_expense + $expense_list->expected_mar_expense +
                $expense_list->expected_apr_expense + $expense_list->expected_may_expense + $expense_list->expected_june_expense;
                $total_fixed_expense += $annual_expense;
            }
            
            $final_arr[$key +1]['expense_name'] = $expense_list->expected_expense_name;
            $final_arr[$key + 1]['annual_expense'] = getPrice($annual_expense, 'N');
        }
        
        $next_subscriptions = Subscription::whereBetween('subscription_next_date', [$q1_start_date, $q4_end_date])->whereSubscriptionCycle('yearly')->get();
        $next_subscription_total = 0;
        foreach($next_subscriptions as $key => $val) {
            $next_subscription_total += getNextAmountForSubscription($val->id);
        }
        $recurring_invoice_total = $next_subscription_total;         
        $final_arr[0][2] = '';
        $final_arr[0][3] = '';
        $final_arr[0][4] = 'Recurring';
        $final_arr[0][5] = getPrice($recurring_invoice_total, 'N');

        $final_arr[1][2] = '';
        $final_arr[1][3] = '';
        $final_arr[1][4] = 'Fixed Expense';
        $final_arr[1][5] = getPrice($total_fixed_expense, 'N');

        $final_arr[2][2] = '';
        $final_arr[2][3] = '';
        $final_arr[2][4] = 'EOFY (Balance)';
        $final_arr[2][5] = getPrice($recurring_invoice_total - $total_fixed_expense, 'N');

       return collect($final_arr);
    }

    public function columnFormats(): array {
        return [
            'B' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'F' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $sheet->getStyle('E1:F1')->getFont()->setBold(true);
        $sheet->getStyle('E2:F2')->getFont()->setBold(true);
        $sheet->getStyle('E3:F3')->getFont()->setBold(true);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:B1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFC000');

                $event->sheet->getDelegate()->getStyle('E1:F1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('ED7D31');

                $event->sheet->getDelegate()->getStyle('E2:F2')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('70AD47');
                
                $event->sheet->getDelegate()->getStyle('E3:F3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('729FCF');
            },
        ];
    }
}
