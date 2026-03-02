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
use App\Models\Subscription;
use App\Models\Quote;
use Carbon\Carbon;

class ForecastReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    protected $heading_str;
    protected $from_date;
    protected $to_date;
    
    function __construct($params) {
        $this->params = $params;
        $this->from_date = Carbon::now()->format('Y-m-d');
        $this->to_date = chnageDateFormat($this->params['to_date']);
        $subscription_count = Subscription::with(['client'])->whereBetween('subscription_next_date', [$this->from_date, $this->to_date])->count();
        $sub_count = $subscription_count+3;
        $this->heading_str = 'A'.$sub_count.':C'.$sub_count;
    }

    public function collection() {
        $subscriptions = Subscription::with(['client'])->whereBetween('subscription_next_date', [$this->from_date, $this->to_date])
            ->orderBy('subscription_next_date', 'asc')->get();
        $quotes = Quote::with(['client'])->whereQuotePaymentStatus('Open')->get();
        $sub_arr = [];
        $quote_arr = [];
        foreach($subscriptions as $key => $value) {
            $invoice_count = isset($subscriptions[$key]) ? getGeneratedSubscriptionCount($subscriptions[$key]->id) : '';      
            $sub_arr[$key]['subscription_name'] = isset($subscriptions[$key]) ? $subscriptions[$key]->subscription_name : '';
            $sub_arr[$key]['client_name'] = isset($subscriptions[$key]) ? $subscriptions[$key]->client->client_business_name : '';
            $sub_arr[$key]['subscription_cycle'] = isset($subscriptions[$key]) ? $subscriptions[$key]->subscription_cycle : '';
            $sub_arr[$key]['subscription_start_date'] = isset($subscriptions[$key]) ? ($subscriptions[$key]->subscription_start_date ? changeDateFormatAtExport($subscriptions[$key]->subscription_start_date) : '') : '';
            $sub_arr[$key]['subscription_next_date'] = isset($subscriptions[$key]) ? ($subscriptions[$key]->subscription_next_date ? changeDateFormatAtExport($subscriptions[$key]->subscription_next_date) : '') : '';
            $sub_arr[$key]['next_increment'] = isset($subscriptions[$key]) ? ($subscriptions[$key]->subscription_incremented_percentage ? $subscriptions[$key]->subscription_incremented_percentage : 'N/A') : '';
            $sub_arr[$key]['subscription_amount'] = isset($subscriptions[$key]) ? getPrice(getNextAmountForSubscription($subscriptions[$key]->id), 'N') : '';
            $sub_arr[$key]['subscription_status'] = isset($subscriptions[$key]) ? ($subscriptions[$key]->is_status == 1 ? 'Active' : 'Inactive') : '';
        }
        $sub_arr_count = count($sub_arr);
        $sub_arr[$sub_arr_count] = [''];
        $quote_arr[0] = [
            'Quote Number',
            'Quote Client',
            'Quote Amount',
        ];

        foreach($quotes as $key => $value) {
            $quote_arr[$key+1]['quote_number'] = isset($quotes[$key]) ? $quotes[$key]->quote_number : '';
            $quote_arr[$key+1]['quote_client'] = isset($quotes[$key]) ? $quotes[$key]->client->client_business_name : '';
            $quote_arr[$key+1]['quote_amount'] = isset($quotes[$key]) ? getPrice($quotes[$key]->quote_grand_total, 'N') : '';
        }
        $final_arr = array_merge($sub_arr, $quote_arr);

        return collect($final_arr);
    }

    public function headings(): array {
        return [
            'Subscription Name',
            'Client Name',
            'Cycle',
            'Subscription Start Date',
            'Subscription Next Date',
            '% Increase',
            'Amount',
            'Subscription Status'
        ];
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle($this->heading_str)->getFont()->setBold(true);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:H1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('548135');

                $event->sheet->getDelegate()->getStyle($this->heading_str)
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('548135');
            }
        ];
    }

    public function columnFormats(): array {
        return [
            'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'G' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

}
