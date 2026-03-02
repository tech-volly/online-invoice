<?php

namespace App\Exports;

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
use Carbon\Carbon;

class QuarterComparisonExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;
    
    function __construct($params) {
        $this->params = $params;
    }

    public function collection() {
        $year = $this->params;
        $next_year = $year + 1;
        
        $q1_start_date = $year.'-07-01';
        $q1_end_date = $year.'-09-30';
        $q2_start_date = $year.'-10-01';
        $q2_end_date = $year.'-12-31';

        $q3_start_date = $next_year.'-01-01';
        $q3_end_date = $next_year.'-03-31';
        $q4_start_date = $next_year.'-04-01';
        $q4_end_date = $next_year.'-06-30';

        $quarter1_income = DB::table('invoices')->whereBetween('invoice_payment_date', [$q1_start_date, $q1_end_date])
            ->where('invoices.deleted_at', '=', null)->select(DB::raw('sum(invoices.invoice_grand_total) as q1_total_income'))->get();

        $quarter2_income = DB::table('invoices')->whereBetween('invoice_payment_date', [$q2_start_date, $q2_end_date])
            ->where('invoices.deleted_at', '=', null)->select(DB::raw('sum(invoices.invoice_grand_total) as q2_total_income'))->get();

        $quarter3_income = DB::table('invoices')->whereBetween('invoice_payment_date', [$q3_start_date, $q3_end_date])
            ->where('invoices.deleted_at', '=', null)->select(DB::raw('sum(invoices.invoice_grand_total) as q3_total_income'))->get();

        $quarter4_income = DB::table('invoices')->whereBetween('invoice_payment_date', [$q4_start_date, $q4_end_date])
            ->where('invoices.deleted_at', '=', null)->select(DB::raw('sum(invoices.invoice_grand_total) as q4_total_income'))->get();

        
        $quarter1_expense = DB::table('expenses')->whereBetween('expense_date', [$q1_start_date, $q1_end_date])
            ->where('expenses.deleted_at', '=', null)->select(DB::raw('sum(expenses.expense_amount) as q1_total_expense'))->get();

        $quarter2_expense = DB::table('expenses')->whereBetween('expense_date', [$q2_start_date, $q2_end_date])
            ->where('expenses.deleted_at', '=', null)->select(DB::raw('sum(expenses.expense_amount) as q2_total_expense'))->get();

        $quarter3_expense = DB::table('expenses')->whereBetween('expense_date', [$q3_start_date, $q3_end_date])
            ->where('expenses.deleted_at', '=', null)->select(DB::raw('sum(expenses.expense_amount) as q3_total_expense'))->get();

        $quarter4_expense = DB::table('expenses')->whereBetween('expense_date', [$q4_start_date, $q4_end_date])
            ->where('expenses.deleted_at', '=', null)->select(DB::raw('sum(expenses.expense_amount) as q4_total_expense'))->get();
        
        $final_arr[0] = new \stdClass();
        $q1_income = $quarter1_income[0]->q1_total_income;
        $q1_expense = $quarter1_expense[0]->q1_total_expense;
        $net_profit_q1 = $q1_income - $q1_expense;
        $profit_q1 = getProfitForQuarterReport($net_profit_q1, $q1_income);
        $final_arr[0]->quarter = 'Quarter 1';
        $final_arr[0]->q1_total_income = $q1_income ? getPrice($q1_income, 'N') : getPrice(0);
        $final_arr[0]->q1_total_expense = $q1_expense ? getPrice($q1_expense, 'N') : getPrice(0);
        $final_arr[0]->net_profit = $net_profit_q1 ? getPrice($net_profit_q1, 'N') : getPrice(0);
        $final_arr[0]->profit_margin = $profit_q1.'%';

        $final_arr[1] = new \stdClass();
        $q2_income = $quarter2_income[0]->q2_total_income;
        $q2_expense = $quarter2_expense[0]->q2_total_expense;
        $net_profit_q2 = $q2_income - $q2_expense;
        $profit_q2 = getProfitForQuarterReport($net_profit_q2, $q2_income);
        $final_arr[1]->quarter = 'Quarter 2';
        $final_arr[1]->q2_total_income = $q2_income ? getPrice($q2_income, 'N') : getPrice(0);
        $final_arr[1]->q2_total_expense = $q2_expense ? getPrice($q2_expense, 'N') : getPrice(0);
        $final_arr[1]->net_profit = $net_profit_q2 ?  getPrice($net_profit_q2, 'N') : getPrice(0);
        $final_arr[1]->profit_margin = $profit_q2.'%';

        $final_arr[2] = new \stdClass();
        $q3_income = $quarter3_income[0]->q3_total_income;
        $q3_expense = $quarter3_expense[0]->q3_total_expense;
        $net_profit_q3 = $q3_income - $q3_expense;
        $profit_q3 = getProfitForQuarterReport($net_profit_q3, $q3_income);
        $final_arr[2]->quarter = 'Quarter 3';
        $final_arr[2]->q3_total_income = $q3_income ? getPrice($q3_income, 'N') : getPrice(0);
        $final_arr[2]->q3_total_expense = $q3_expense ? getPrice($q3_expense, 'N') : getPrice(0);
        $final_arr[2]->net_profit = $net_profit_q3 ? getPrice($net_profit_q3, 'N') : getPrice(0);
        $final_arr[2]->profit_margin = $profit_q3.'%';

        $q4_income = $quarter4_income[0]->q4_total_income;
        $q4_expense = $quarter4_expense[0]->q4_total_expense;
        $net_profit_q4 = $q4_income - $q4_expense;
        $profit_q4 = getProfitForQuarterReport($net_profit_q4, $q4_income);
        $final_arr[3] = new \stdClass();
        $final_arr[3]->quarter = 'Quarter 4';
        $final_arr[3]->q4_total_income = $q4_income ? getPrice($q4_income, 'N') : getPrice(0);
        $final_arr[3]->q4_total_expense = $q4_expense ? getPrice($q4_expense, 'N') : getPrice(0);
        $final_arr[3]->net_profit = $net_profit_q4 ? getPrice($net_profit_q4, 'N') : getPrice(0);
        $final_arr[3]->profit_margin = $profit_q4.'%';
       
        return collect($final_arr);
    }

    public function headings(): array {
        return [
            'Quarter',
            'Income',
            'Expense',
            'Net Profit',
            'Profit Margin'
        ];
    }

    public function columnFormats(): array {
        return [
            'B' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'D' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
        ];
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:E1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('D0CECE');

                $event->sheet->getDelegate()->getStyle('A2:E2')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFD965');

                $event->sheet->getDelegate()->getStyle('A3:E3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('BDD6EE');
                
                $event->sheet->getDelegate()->getStyle('A4:E4')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('F4B083');

                $event->sheet->getDelegate()->getStyle('A5:E5')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('A8D08D');
            },
        ];
    }
}
