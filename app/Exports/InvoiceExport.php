<?php

namespace App\Exports;

use App\Models\Invoice;
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

class InvoiceExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        
        $invoices = DB::table('invoices')
            ->leftjoin('clients', 'clients.id', '=', 'invoices.client_id')
            ->leftjoin('payment_statuses', 'payment_statuses.id', '=', 'invoices.payment_status_id')
            ->leftJoin('projects', 'invoices.project_id', '=', 'projects.id')
            ->where('invoices.deleted_at', '=', null)
            ->select('invoices.id as invoice_id', 'invoices.invoice_number', 'clients.client_business_name as client', 'invoices.invoice_date', 
                'invoices.invoice_due_date as due_date', 'invoices.invoice_grand_total as amount', 'invoices.invoice_grand_gst as gst_collected','payment_statuses.name as payment_status', 
                'invoices.invoice_payment_date as payment_date', 'invoices.invoice_notes as notes','projects.name as project_name')
            ->orderByRaw("CASE WHEN invoices.invoice_payment_date IS NULL THEN 1 ELSE 0 END DESC, invoices.invoice_payment_date ASC")
            ->get();

        $data = [];
        foreach($invoices as $key=>$value) {
            $data[$key] = [];            
            $categories = getInvoiceProductCategories($value->invoice_id);
            $data[$key]['payment_date'] = $value->payment_date ? changeDateFormatAtExport($value->payment_date) : '';
            $data[$key]['project_name']=$value->project_name;
            $data[$key]['client'] = $value->client;
            $data[$key]['notes'] = $value->notes;
            $data[$key]['invoice_number'] = $value->invoice_number;
            $data[$key]['amount'] = getPrice($value->amount, 'N');
            $data[$key]['gst_collected'] = getPrice($value->gst_collected, 'N');
            $data[$key]['categories'] = $categories;
            $data[$key]['payment_status'] = $value->payment_status;
            $data[$key]['invoice_date'] = changeDateFormatAtExport($value->invoice_date);
            $data[$key]['due_date'] = changeDateFormatAtExport($value->due_date);
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
