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
use App\Models\Quote;

class QuotesByFilterExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;
    
    function __construct($params) {
        $this->params = $params;
    }

    public function collection() {
        if($this->params['client'] || $this->params['quote_status']) { 
            $where_query = "1";
            if($this->params['quote_status'] && $this->params['quote_status'] != 'null') {
                $quote_status = $this->params['quote_status'];
                $where_query .= " AND (quote_payment_status = '" .$quote_status. "' ";
                $where_query .= ')';
            }
            $where_has = "1";
            if($this->params['client'] && $this->params['client'] != 'null') {
                $client = $this->params['client'];
                $where_has .= " AND ( id = '" . $client . "' ";
                $where_has .= ')';
            }
            $quotes = Quote::with(['client'])->whereRaw($where_query)->whereHas('client', function($query) use ($where_has) {
                $query->whereRaw($where_has);
            })->orderBy('id', 'desc')->get();
        }
        $data = [];
        foreach($quotes as $key => $value) {
            $data[$key] = [];
            $data[$key]['quote_number'] = $value->quote_number;
            $data[$key]['client_number'] = $value->client->client_business_name;
            $data[$key]['quote_date'] = $value->quote_date ? changeDateFormatAtExport($value->quote_date) : '';
            $data[$key]['amount'] = getPrice($value->quote_grand_total, 'N');
            $data[$key]['quote_status'] = $value->quote_payment_status;
        }

        return collect($data);
    }

    public function headings(): array {
        return [
            'Quote Number',
            'Client',
            'Quote Date',
            'Amount',
            'Quote Status'
        ];
    }

    public function columnFormats(): array {
        return [
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
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
                    ->setARGB('70AD47');
            },
        ];
    }
}
