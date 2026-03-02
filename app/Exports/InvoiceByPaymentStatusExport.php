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
use App\Models\Invoice;


class InvoiceByPaymentStatusExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    
    function __construct($params) {
        $this->params = $params;

    }

    public function collection() {
        if($this->params['client'] || $this->params['payment_status'] || $this->params['from_payment_date'] || $this->params['to_payment_date']) {
            $where_query = "1";
            if(!empty($this->params['from_payment_date'])) {
                $from_payment_date = chnageDateFormat($this->params['from_payment_date']);
                $to_payment_date = chnageDateFormat($this->params['to_payment_date']);
                $where_query .= " AND (invoice_payment_date between '".$from_payment_date."' and '".$to_payment_date."' ";
                $where_query .= ')';
            }
            $where_has = "1";
            if($this->params['client'] && $this->params['client'] != 'null') {
                $client = $this->params['client'];
                $where_has .= " AND ( id = '" . $client . "' ";
                $where_has .= ')';
            }
            $where_has_payment = "1";
            if($this->params['payment_status'] && $this->params['payment_status'] != 'null') {
                $payment_status = getPaymentStatusName($this->params['payment_status']);
                $where_has_payment .= " AND (name = '" .$payment_status. "' ";
                $where_has_payment .= ')';
            }
            // $invoices = Invoice::with(['client', 'payment_status'])->whereRaw($where_query)
            //     ->whereHas('client', function($query) use ($where_has) {
            //         $query->whereRaw($where_has);
            //     })->whereHas('payment_status', function($q) use ($where_has_payment) {
            //         $q->whereRaw($where_has_payment);
            //     })->orderBy('id', 'desc')
            //     ->get();

                $invoices = Invoice::with(['client', 'payment_status'])->whereRaw($where_query)
                ->leftJoin('projects', 'invoices.project_id', '=', 'projects.id')
                ->whereHas('client', function($query) use ($where_has) {
                    $query->whereRaw($where_has);
                })->whereHas('payment_status', function($q) use ($where_has_payment) {
                    $q->whereRaw($where_has_payment);
                })->orderBy('id', 'desc')
                ->select('invoices.*', 'projects.name as project_name')
                ->get();

                
        }

        $data = [];
        foreach($invoices as $key=>$value) {
            $data[$key] = [];
            $categories = getInvoiceProductCategories($value->id);
            $data[$key]['payment_date'] = $value->invoice_payment_date ? changeDateFormatAtExport($value->invoice_payment_date) : '';
            $data[$key]['project_name']=$value->project_name;
            $data[$key]['client'] = $value->client->client_business_name;
            $data[$key]['notes'] = $value->notes;
            $data[$key]['invoice_number'] = $value->invoice_number;
            $data[$key]['amount'] = getPrice($value->invoice_grand_total, 'N');
            $data[$key]['gst_collected'] = getPrice($value->invoice_grand_gst, 'N');
            $data[$key]['categories'] = $categories;
            $data[$key]['payment_status'] = $value->payment_status->name;
            $data[$key]['invoice_date'] = changeDateFormatAtExport($value->invoice_date);
            $data[$key]['due_date'] = changeDateFormatAtExport($value->invoice_due_date);
        }

        return collect($data);
    }

    public function headings(): array {
        return [
            'Payment Date',
            'Project Name',
            'Client',
            'Notes',
            'Invoice Number',
            'Amount',
            'GST Collected',
            'Categories',
            'Payment Status',
            'Invoice Date',
            'Due Date'
        ];
    }

    public function columnFormats(): array {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'J' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'K' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'G' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
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
                    ->setARGB('70AD47');
            },
        ];
    }
}
