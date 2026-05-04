<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;

class ClientRevenueReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting, WithEvents
{
    use Exportable;

    protected $year;
    protected $compareYear;
    protected $format;

    public function __construct($year = null, $compareYear = null, $format = 'xlsx')
    {
        $this->year = $year ?? date('Y');
        $this->compareYear = $compareYear;
        $this->format = $format;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $data = [];

        // PDF/Excel exports must include all clients ordered by highest paid amount.
        $clients = \DB::table('invoices')
            ->join('clients', 'invoices.client_id', '=', 'clients.id')
            ->whereNull('invoices.deleted_at')
            ->where('invoices.payment_status_id', '=', '2') 
            ->whereNull('clients.deleted_at')
            ->whereBetween('invoices.invoice_payment_date', [($this->year - 1) . '-07-01', $this->year . '-06-30'])
            ->selectRaw("
                clients.id,
                clients.client_business_name,
                clients.client_number,
                SUM(invoices.invoice_grand_total) as current_year_revenue
            ")
            ->groupBy('clients.id', 'clients.client_business_name', 'clients.client_number')
            ->orderByDesc('current_year_revenue')
            ->get();

        foreach ($clients as $client) {
            $previousRevenue = $this->compareYear ? $this->getClientRevenueByYear($client->id, $this->compareYear) : 0;
            $difference = $client->current_year_revenue - $previousRevenue;
            $percentageChange = $previousRevenue > 0 ? (($difference / $previousRevenue) * 100) : 0;

            $row = [
                'client_number' => $client->client_number,
                'client_name' => $client->client_business_name,
                'current_year_revenue' => round($client->current_year_revenue, 2),
            ];

            if ($this->compareYear) {
                $row['previous_year_revenue'] = round($previousRevenue, 2);
                $row['difference'] = round($difference, 2);
                $row['percentage_change'] = round($percentageChange, 2) . '%';
            }

            $data[] = $row;
        }

        return collect($data);
    }

    /**
     * Get client revenue for a specific financial year (July to June)
     */
    private function getClientRevenueByYear($clientId, $year)
    {
        $startDate = ($year - 1) . '-07-01';
        $endDate = $year . '-06-30';

        $revenue = Invoice::where('client_id', $clientId)
            ->whereBetween('invoice_payment_date', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('invoice_grand_total');

        return $revenue ?? 0;
    }

    public function headings(): array
    {
        $headings = [
            'Client Number',
            'Client Name',
            'Current Year Revenue (FY ' . $this->year . '-' . ($this->year + 1) . ')',
        ];

        if ($this->compareYear) {
            $headings[] = 'Previous Year Revenue (FY ' . $this->compareYear . '-' . ($this->compareYear + 1) . ')';
            $headings[] = 'Difference';
            $headings[] = 'Change %';
        }

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
            'A' => ['alignment' => ['horizontal' => 'left']],
            'B' => ['alignment' => ['horizontal' => 'left']],
        ];
    }

    public function columnFormats(): array
    {
        $formats = [
            'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];

        if ($this->compareYear) {
            $formats['D'] = NumberFormat::FORMAT_CURRENCY_USD_SIMPLE;
            $formats['E'] = NumberFormat::FORMAT_CURRENCY_USD_SIMPLE;
        }

        return $formats;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getParent()->getDefaultStyle()
                    ->getAlignment()
                    ->setVertical('center');
            },
        ];
    }

    public function download($filename)
    {
        if ($this->format === 'pdf') {
            return $this->downloadPDF($filename);
        }
        
        // Default to Excel export using Excel facade
        return Excel::download($this, $filename);
    }

    /**
     * Generate and download PDF with proper formatting
     */
    private function downloadPDF($filename)
    {
        $data = [];

        // PDF export includes all clients ordered by highest paid amount.
        $clients = \DB::table('invoices')
            ->join('clients', 'invoices.client_id', '=', 'clients.id')
            ->whereNull('invoices.deleted_at')
            ->whereNull('clients.deleted_at')
            ->where('invoices.payment_status_id', '=', '2') // ✅ ADD THIS
            ->whereBetween('invoices.invoice_payment_date', [($this->year - 1) . '-07-01', $this->year . '-06-30'])
            ->selectRaw("
                clients.id,
                clients.client_business_name,
                clients.client_number,
                SUM(invoices.invoice_grand_total) as current_year_revenue
            ")
            ->groupBy('clients.id', 'clients.client_business_name', 'clients.client_number')
            ->orderByDesc('current_year_revenue')
            ->get();

        $totalCurrentRevenue = 0;
        $totalPreviousRevenue = 0;

        foreach ($clients as $client) {
            $previousRevenue = $this->compareYear ? $this->getClientRevenueByYear($client->id, $this->compareYear) : 0;
            $difference = $client->current_year_revenue - $previousRevenue;
            $percentageChange = $previousRevenue > 0 ? (($difference / $previousRevenue) * 100) : 0;

            $data[] = [
                'client_number' => $client->client_number,
                'client_name' => $client->client_business_name,
                'current_year_revenue' => round($client->current_year_revenue, 2),
                'previous_year_revenue' => round($previousRevenue, 2),
                'difference' => round($difference, 2),
                'percentage_change' => round($percentageChange, 2)
            ];

            $totalCurrentRevenue += $client->current_year_revenue;
            $totalPreviousRevenue += $previousRevenue;
        }

        $pdfData = [
            'title' => 'Client Revenue Report',
            'report_date' => now()->format('F j, Y'),
            'current_year' => $this->year,
            'previous_year' => $this->compareYear ?: ($this->year - 1),
            'current_fy' => 'FY ' . $this->year . '-' . ($this->year + 1),
            'previous_fy' => 'FY ' . ($this->compareYear ?: ($this->year - 1)) . '-' . (($this->compareYear ?: ($this->year - 1)) + 1),
            'clients' => $data,
            'totalCurrentRevenue' => round($totalCurrentRevenue, 2),
            'totalPreviousRevenue' => round($totalPreviousRevenue, 2),
            'totalDifference' => round($totalCurrentRevenue - $totalPreviousRevenue, 2),
            'compareYear' => $this->compareYear ? true : false
        ];

        $pdf = PDF::loadView('exports.client-revenue-pdf', $pdfData);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOptions(['defaultFont' => 'sans-serif', 'margin-top' => 10, 'margin-bottom' => 10]);

        return $pdf->download($filename);
    }
}
