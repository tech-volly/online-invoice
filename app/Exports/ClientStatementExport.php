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

class ClientStatementExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithEvents
{
    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {

        if (!isset($this->params['client_id']) || empty($this->params['client_id'])) {
            throw new \Exception('Client ID is required for Client Statement export.');
        }

        $from_year_month = splitYearMonth($this->params['from_date']);
        $to_year_month = splitYearMonth($this->params['to_date']);
        $from_date = $from_year_month . '-01';
        $to_date =  date("Y-m-t", strtotime($to_year_month));
        // $query = DB::table('invoices')
        //     ->leftjoin('clients', 'clients.id', '=', 'invoices.client_id')
        //     ->leftjoin('payment_statuses', 'payment_statuses.id', '=', 'invoices.payment_status_id')
        //     ->where('invoices.deleted_at', '=', null)
        //     ->where('invoices.client_id', '=', $this->params['client_id']);
        $query = DB::table('invoices')
            ->leftJoin('clients', 'clients.id', '=', 'invoices.client_id')
            ->leftJoin('payment_statuses', 'payment_statuses.id', '=', 'invoices.payment_status_id')
            ->leftJoin('invoice_payments', 'invoice_payments.invoice_id', '=', 'invoices.id')
            ->whereNull('invoices.deleted_at')
            ->where('invoices.client_id', $this->params['client_id']);


        if (!empty($this->params['from_date'])) {
            $query->where('invoices.invoice_due_date', '>=', $from_date);
        }

        if (!empty($this->params['to_date'])) {
            $query->where('invoices.invoice_due_date', '<=', $to_date);
        }

        $invoices = $query->select(
            'invoices.id as invoice_id',
            'invoices.invoice_number',
            'clients.client_business_name',
            'clients.client_first_name',
            'clients.client_last_name',
            'invoices.invoice_due_date',
            'payment_statuses.name as payment_status',
            'invoices.invoice_payment_date',
            'invoices.invoice_grand_total'
        )
            ->orderBy('invoices.invoice_due_date', 'asc')
            ->get();

        $data = [];
        foreach ($invoices as $key => $value) {
            $data[$key] = [];
            $data[$key]['client_name'] = $value->client_business_name;
            $data[$key]['shipping_name'] = trim($value->client_first_name . ' ' . $value->client_last_name);
            $data[$key]['invoice_number'] = $value->invoice_number;
            $data[$key]['due_date'] = $value->invoice_due_date ? changeDateFormatAtExport($value->invoice_due_date) : '';
            $data[$key]['payment_status'] = $value->payment_status;
            $data[$key]['payment_date'] = $value->invoice_payment_date ? changeDateFormatAtExport($value->invoice_payment_date) : '';
            $outstanding = 0;
            if (strtolower($value->payment_status) != 'paid') {
                $outstanding = $value->invoice_grand_total;
            }
            $data[$key]['outstanding_amount'] = getPrice($outstanding, 'N');
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Client Name',
            'Shipping Name',
            'Invoice Number',
            'Due Date',
            'Payment Status',
            'Payment Date',
            'Outstanding Amount'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:G1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('70AD47');
            },
        ];
    }
}
