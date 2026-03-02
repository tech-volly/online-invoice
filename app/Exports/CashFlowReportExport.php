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
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\ExpenseCategory;
use App\Models\OpeningBalance;
use DB;
use DateTime;
use DateInterval;
use DatePeriod;

class CashFlowReportExport implements FromCollection, ShouldAutoSize, WithStyles, WithEvents, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    public $first_row_font_style;
    public $row_range;
    public $first_row_opening_val_bg;
    public $first_total_cell_header_bg;
    public $inc_row_range;
    public $category_heading_range;
    public $category_text_header;
    public $expense_total_header_cell;
    public $total_income_val_cell_color;
    public $last_payment_row_styling;
    public $last_payment_row_header;
    public $footer_row_styling;
    public $netcash_row_styling;
    public $total_full_col_style;

    // Set currency format for cell
    public $total_income_format;
    public $total_full_row_format;
    public $loop_range;
    public $total_cash_pay_out_format;
    public $total_net_cash_format;
    public $closing_bal_format;
    
    function __construct($params) {
        $this->params = $params;
    }

    public function collection() {
        $from_year_month = splitYearMonth($this->params['from_year_month']);
        $to_year_month = splitYearMonth($this->params['to_year_month']);
        $from_date = $from_year_month.'-01';
        $to_date =  date("Y-m-t", strtotime($to_year_month));
        $from_year = splitYear($this->params['from_year_month']);
        $to_year = splitYear($this->params['to_year_month']);

        $final_arr = [];    
        $start = new DateTime($from_date);
        $end = new DateTime($to_date);
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        $opening_year = explode('-', $this->params['from_year_month']);
        $opening_balance = OpeningBalance::whereOpeningYear($opening_year[1])->whereIsStatus(1)->first();
        $final_arr[0]= [$from_year.'-'.$to_year];
        foreach ($period as $key => $dt) {
            $final_arr[0][$key+1] = $dt->format("m-Y");
        }
        $final_arr[0][count($final_arr[0])] = 'Total';
        $final_arr_zero_count = count($final_arr[0]);
        $final_arr[0][count($final_arr[0])] = 'Opening Balance';
        $final_arr[0][count($final_arr[0])] = getPrice($opening_balance->opening_balance_value);

        $final_arr[1] = ['Start Date'];
        foreach ($period as $key => $dt) {
            $final_arr[1][$key+1] = $dt->format("d-m-Y");
        }
        $incomes_gst = DB::table('invoices')->whereBetween('invoice_payment_date', [$from_date, $to_date])
            ->select(DB::raw("SUM(invoice_grand_gst) as invoice_grand_gst"))
            ->get();
        $final_arr[1][count($final_arr[1])] = '';
        $final_arr[1][count($final_arr[1])+1] = 'GST Earned';
        $final_arr[1][count($final_arr[1])+1] = getPrice(round($incomes_gst[0]->invoice_grand_gst, 2));
        

        $expenses_gst = Expense::whereBetween('expense_date', [$from_date, $to_date])->get();
        $expense_total_gst = 0;
        foreach($expenses_gst as $key => $value) {
            if ($value->expense_tax == 'GST Inclusive') {
                $expense_total_gst += $value->expense_amount / 11;
            }else if($value->expense_tax == 'No GST') {
                $expense_total_gst +=  0;
            }
            
        }
        $final_arr[2] = ['End Date'];
        foreach ($period as $key => $dt) {
            $final_arr[2][$key+1] = $dt->format("t-m-Y");
        }
        $final_arr[2][count($final_arr[2])] = '';
        $final_arr[2][count($final_arr[2])+ 1] = 'GST Paid';
        $final_arr[2][count($final_arr[2])+ 1] = getPrice(round($expense_total_gst, 2));
        
        //Set the dynamic range for row styling
        $final_arr_count =  count($final_arr[0]) - 3;
        $alphabet_range = range('A', 'Z');
        $next_alpha = $alphabet_range[$final_arr_count-1];
        $this->row_range =  'B1:'.$next_alpha.'1';
        //Set first row bold header
        $first_header = count($final_arr[0]);
        $this->first_row_font_style = 'B1:'.$alphabet_range[$first_header-1].'1';
        // Set opening balance val bg        
        $this->first_row_opening_val_bg = $alphabet_range[$first_header-1].'1';
        //Style the Total cell 
        $final_arr_zero_cnt_total = count($final_arr[0]);
        $next_alpha_for_total = $alphabet_range[$final_arr_zero_cnt_total-3];
        $this->first_total_cell_header_bg = $next_alpha_for_total.'1';

        //Retrived total income with groupBy Month
        $incomes = Invoice::select( 
                \DB::raw('SUM(invoice_grand_total) as total'), 
                \DB::raw("EXTRACT(MONTH FROM `invoice_payment_date`) as month"),
                \DB::raw("EXTRACT(YEAR FROM `invoice_payment_date`) as year")
            )->whereBetween('invoice_payment_date', [$from_date, $to_date])
            ->groupBy('month')
            ->get();
    
        $income_arr = [];
        foreach($incomes->toArray() as $key => $value) {
            $num_padded = sprintf("%02d", $value['month']);
            $setter_key = $num_padded.'-'.$value['year'];
            $income_arr[$setter_key] = $value['total'];
        }
        
        $count = count($final_arr);
        $final_arr[$count] = [''];
        $income_total = 0;
        $income_arr_counter = 0;
        foreach($final_arr[0] as $key => $value) {
            $income_arr_counter++;
            if($final_arr_zero_count == $income_arr_counter){
                break;
            }
            $final_arr[$count+1][$key] = 'Total Income';
            if($key != 0) {
                if(isset($income_arr[$value])) {
                    $income_total += $income_arr[$value];
                    $final_arr[$count+1][$key] = getPrice($income_arr[$value], 'N');
                }else {
                    $final_arr[$count+1][$key] = getPrice(0, 'N');
                }
            }
        }

        //Set income row color
        $final_arr[$count+1][count($final_arr[0])-3] = getPrice($income_total);
        $this->inc_row_range = 'B5:'.$next_alpha.'5';
        $total_inc_cell_alpha = $alphabet_range[count($final_arr[$count+1]) - 1];
        $this->total_income_val_cell_color = $total_inc_cell_alpha.'5';
        
        //Retrived total expense with groupBy cateogry and month
        $expenses = DB::table('expenses')
            ->whereBetween('expense_date', [$from_date, $to_date])
            ->where('expenses.deleted_at', '=', null)
            ->select( 
                \DB::raw('SUM(expense_amount) as total'), 
                \DB::raw("EXTRACT(MONTH FROM `expense_date`) as month"),
                \DB::raw("EXTRACT(YEAR FROM `expense_date`) as year"),
                'expenses.supplier_expense_category as category_id',
                'expenses.id as expense_id'
            )
            ->groupBy('category_id', 'month')
            ->orderBy('category_id', 'asc')
            ->get();
        $expense_arr = [];   
        foreach($expenses->toArray() as $key => $value) {
            $num_padded = sprintf("%02d", $value->month);
            $setter_key = $num_padded.'-'.$value->year.'-'.$value->category_id;
            $expense_arr[$setter_key] = [
                $value->total,
                $value->category_id,
            ];
            
        }

        //Set Category header (month row)
        $final_arr_prev_count = count($final_arr);
        $final_arr[$final_arr_prev_count] = [''];
        $final_arr[$final_arr_prev_count+1][0] = '';
        foreach ($period as $key => $dt) {
            $final_arr[$final_arr_prev_count+1][$key+1] = $dt->format("m-Y");            
        }
        $final_arr[$final_arr_prev_count+1][count($final_arr[0])] = 'Total';            
        $this->category_heading_range = 'B'.count($final_arr).':'.$next_alpha.''.count($final_arr);
        $expense_arr_count = count($final_arr[$final_arr_prev_count+1]) - 1;
        $expense_total_header = $alphabet_range[count($final_arr[$final_arr_prev_count+1]) - 1];
        $this->expense_total_header_cell = $expense_total_header.'7';

        //Set Categories with expense
        $expense_final_arr_count = count($final_arr);
        $categories = ExpenseCategory::whereIsStatus(1)->get();
        $total_arr = [];
        $total_arr[0] = 'Total Payments / Outgoings';
        foreach($categories as $cat_key => $cat_val) {
            $expense_arr_row_counter = 0;
            $total = 0;
            // $final_arr[$expense_final_arr_count][0][0] = $cat_val->name;
            foreach($final_arr[0] as $key => $value) { 
                $expense_arr_row_counter++;
                if($final_arr_zero_count == $expense_arr_row_counter) {
                    break;
                }
                $final_arr[$expense_final_arr_count][0][0] = $cat_val->name;
                if($key != 0 && isset($expense_arr[$value.'-'.$cat_val->id])) {  
                    $total += $expense_arr[$value.'-'.$cat_val->id][0]; 
                    $final_arr[$expense_final_arr_count][0][$key] = getPrice($expense_arr[$value.'-'.$cat_val->id][0], 'N');
                    if(isset($total_arr[$key])) {
                        $total_arr[$key] += $expense_arr[$value.'-'.$cat_val->id][0];
                    }else {
                        $total_arr[$key] = $expense_arr[$value.'-'.$cat_val->id][0];
                    }
                }else {
                    $final_arr[$expense_final_arr_count][0][$key] = '$ 0';
                    if(!isset($total_arr[$key])) {
                        $total_arr[$key] = 0;
                    }
                }
            }
            $final_arr[$expense_final_arr_count][0][count($final_arr[0])-3] = getPrice($total, 'N');
            $expense_final_arr_count += 1;
        }
        $count_of_total_arr = count($total_arr);
        unset($total_arr[$count_of_total_arr]);
        unset($total_arr[$count_of_total_arr+1]);
        unset($total_arr[$count_of_total_arr+2]);

        //Set the category name in bold style
        $count_categories_matric = count($final_arr);
        $this->category_text_header = 'A8:A'.$count_categories_matric;
        
        //Set Total Payment row
        foreach($total_arr as $key => $value) {
            if($key != 0) {
                $total_arr[$key] = getPrice($value, 'N');
            }
        }
        $final_arr[count($final_arr)][0] = $total_arr;

        //Set color and font style of Total Payments / Outgoings row
        $count_of_final_arr_after_total_pay = count($final_arr); 
        $total_pay_arr_count = count($final_arr[$count_of_final_arr_after_total_pay - 1][0]);
        $last_alpha_of_pay_row = $alphabet_range[$total_pay_arr_count - 1];
        $this->last_payment_row_styling = 'A'.$count_of_final_arr_after_total_pay.':'.$last_alpha_of_pay_row.''.$count_of_final_arr_after_total_pay;
        $this->last_payment_row_header = 'A'.$count_of_final_arr_after_total_pay;

        //Set Footer Row for month-year
        $final_arr_count_for_footer = count($final_arr);
        foreach ($period as $key => $dt) {
            $final_arr[$final_arr_count_for_footer][0] = '';
            $final_arr[$final_arr_count_for_footer][$key+1] = $dt->format("m-Y");            
        }

        //Set Footer row styling
        $final_arr_count_for_styling = count($final_arr);
        $final_arr_footer_count = count($final_arr[$final_arr_count_for_styling - 1]);
        $get_alphabet_for_footer_row = $alphabet_range[$final_arr_footer_count - 1];
        $this->footer_row_styling = 'B'.$final_arr_count_for_styling.':'.$get_alphabet_for_footer_row.''.$final_arr_count_for_styling;

        //Set NetCashFlow
        $final_arr_count_netcash = count($final_arr);
        $total_expense_arr = $final_arr[$final_arr_count_netcash- 2][0];
        $final_arr[$final_arr_count_netcash][0] = 'Net Cashflow';
        foreach($final_arr[4] as $key => $value) {
            if($key != 0 && isset($total_expense_arr[$key])) {
                $netcashflow = formatPrice($value) - formatPrice($total_expense_arr[$key]);
                $final_arr[$final_arr_count_netcash][$key] = $netcashflow == 0 ? '$ 0' : getPrice($netcashflow, 'N');
            }
        }
        
        //Set NetCashFlow Styling
        $count_of_final_arr_after_netcash = count($final_arr);
        $total_netcash_arr_count = count($final_arr[$count_of_final_arr_after_netcash -1]);
        $last_alpha_of_netcash = $alphabet_range[$total_netcash_arr_count - 1];
        $this->netcash_row_styling = 'A'.$count_of_final_arr_after_netcash.':'.$last_alpha_of_netcash.''.$count_of_final_arr_after_netcash;

        //Closing Balance Row
        $count_for_closing_bal = count($final_arr);
        $final_arr[$count_for_closing_bal][0] = 'Closing Balance';
        $closing_bal_index1 = removedExtraSymbolsFromPrice($final_arr[count($final_arr) - 2][1]) + removedExtraSymbolsFromPrice($final_arr[0][count($final_arr[0]) - 1]);
        $final_arr[$count_for_closing_bal][1] = getPrice($closing_bal_index1, 'N');
        $net_cashflow_arr = $final_arr[count($final_arr) - 2];
        foreach($net_cashflow_arr as $key => $value) {
            if($key != 0 && $key != 1) {
                if($key == 2) {
                    $final_arr[$count_for_closing_bal][$key] = $closing_bal_index1 + removedExtraSymbolsFromPrice($value); 
                }else {
                    $final_arr[$count_for_closing_bal][$key] = $final_arr[$count_for_closing_bal][$key-1] + removedExtraSymbolsFromPrice($value); 
                }
            }
        }
        $count_final_arr_closing_bal = count($final_arr) - 1;
        foreach($final_arr[$count_final_arr_closing_bal] as $key => $value) {
            if($key != 0 && $key != 1) {
                $final_arr[$count_final_arr_closing_bal][$key] = getPrice($value, 'N');
            }
        }
        $end_final_arr_count = count($final_arr);
        
        // Total full column styling
        $this->total_full_col_style = $this->expense_total_header_cell.':'.$expense_total_header.'1000';
        
        //Set the currency format for cell
        $total_inc_last_cell_format = $alphabet_range[count($final_arr[4]) -2];
        $this->total_income_format = 'B5:'.$total_inc_last_cell_format.'5'; 
        $this->total_full_row_format = $expense_total_header.'8:'.$expense_total_header.''.$count_categories_matric;        
        $static_range = range('B', $next_alpha);
        $start_index = 8;
        $this->loop_range = '';
        foreach($static_range as $key => $val) {   
            $this->loop_range = $val.$start_index.':'.$val.$count_categories_matric;
        }
        $this->total_cash_pay_out_format = 'B'.$count_of_final_arr_after_total_pay.':'.$total_inc_last_cell_format.$count_of_final_arr_after_total_pay;
        $this->total_net_cash_format = 'B'.$count_of_final_arr_after_netcash.':'.$total_inc_last_cell_format.$count_of_final_arr_after_netcash;
        $this->closing_bal_format = 'B'.$end_final_arr_count.':'.$total_inc_last_cell_format.$end_final_arr_count;

        return collect($final_arr);    
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle($this->first_row_font_style)->getFont()->setBold(true);
        $sheet->getStyle('A5')->getFont()->setBold(true);
        $sheet->getStyle($this->total_income_val_cell_color)->getFont()->setBold(true);
        $sheet->getStyle($this->category_heading_range)->getFont()->setBold(true);
        $sheet->getStyle($this->expense_total_header_cell)->getFont()->setBold(true);
        $sheet->getStyle($this->category_text_header)->getFont()->setBold(true);
        $sheet->getStyle($this->last_payment_row_styling)->getFont()->setBold(true);
        $sheet->getStyle($this->footer_row_styling)->getFont()->setBold(true);
        $sheet->getStyle($this->netcash_row_styling)->getFont()->setBold(true);
        $sheet->getStyle($this->total_full_col_style)->getFont()->setBold(true);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // First row styling
                $event->sheet->getDelegate()->getStyle('A1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFF00');
                
                $event->sheet->getDelegate()->getStyle('A5')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('70AD47');
        
                $event->sheet->getDelegate()->getStyle($this->row_range)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('ED7D31');

                $event->sheet->getDelegate()->getStyle($this->first_row_opening_val_bg)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('ED7D31');
                
                $event->sheet->getDelegate()->getStyle($this->first_total_cell_header_bg)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFC000');

                // Income Row styling
                $event->sheet->getDelegate()->getStyle($this->inc_row_range)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFE699'); 
                
                $event->sheet->getDelegate()->getStyle($this->total_income_val_cell_color)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFC000');

                // Cate
                $event->sheet->getDelegate()->getStyle($this->category_heading_range)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('ED7D31');
                    
                $event->sheet->getDelegate()->getStyle($this->expense_total_header_cell)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFC000');

                //Total Payment/Outgoing styling
                $event->sheet->getDelegate()->getStyle($this->last_payment_row_header)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFC000'); 

                // Footer styling
                $event->sheet->getDelegate()->getStyle($this->footer_row_styling)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('ED7D31'); 

                // Netcashflow styling
                $event->sheet->getDelegate()->getStyle($this->netcash_row_styling)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('F8CBAD'); 
            }
        ];
    }

    public function columnFormats(): array {
        return [
            $this->total_income_format => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            $this->total_full_row_format => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            $this->loop_range => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            $this->total_cash_pay_out_format => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            $this->total_net_cash_format =>  NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            $this->closing_bal_format => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
        ];
    }
}
