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
use App\Models\Subscription;
use Illuminate\Support\Str;

class SubscriptionExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        $subscriptions = Subscription::with(['client'])->orderBy('id', 'desc')->get();
        $data = [];
        foreach($subscriptions as $key => $value) {
            $data[$key] = [];
            $data[$key]['subscription_name'] = $value->subscription_name;
            $data[$key]['client_name'] = $value->client->client_business_name;
            $data[$key]['cycle'] = Str::ucfirst($value->subscription_cycle);
            $data[$key]['subscription_start_date'] = $value->subscription_start_date ? changeDateFormatAtExport($value->subscription_start_date) : '';
            $data[$key]['subscription_next_date'] = $value->subscription_next_date ? changeDateFormatAtExport($value->subscription_next_date) : '';
            $data[$key]['generated'] = getGeneratedSubscriptionCount($value->id);
            $data[$key]['%_increase'] = $value->is_subscription_next_increment ? $value->subscription_incremented_percentage : 'N/A';
            $data[$key]['amount'] = getPrice(getNextAmountForSubscription($value->id), 'N');
        }

        return collect($data);
    }

    public function headings(): array {
        return [
            'Name',
            'Client Name',
            'Cycle',
            'Start Date',
            'Next Date',
            'Generated',
            '% Increase',
            'Amount'
        ];
    }

    public function columnFormats(): array {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
        ];
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:H1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('70AD47');
            },
        ];
    }


}
