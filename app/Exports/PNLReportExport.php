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
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Expense;
use App\Models\Invoice;

class PNLReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents, WithColumnFormatting
{
    /**
     * @return \Illuminate\Support\Collection
     */

    use Exportable;

    // ── NEW: properties to carry data into AfterSheet ─────────────────────────
    protected $categoryData  = [];
    protected $totalDataRows = 0;
    // ─────────────────────────────────────────────────────────────────────────

    function __construct($params)
    {
        $this->params = $params;
    }

    public function collection()
    {
        $from_date = chnageDateFormat($this->params['from_date']);
        $to_date = chnageDateFormat($this->params['to_date']);
        $income_expense_arr = [];
        //$incomes = Invoice::with(['client', 'payment_status'])->whereBetween('invoice_payment_date', [$from_date, $to_date])->orderBy('invoice_payment_date', 'asc')->get();
        $incomes = Invoice::with(['client', 'payment_status'])
            ->leftJoin('projects', 'invoices.project_id', '=', 'projects.id')
            ->whereBetween('invoice_payment_date', [$from_date, $to_date])
            ->select('invoices.*', 'projects.name as inc_project_name')
            ->orderBy('invoice_payment_date', 'asc')
            ->get();
        // $expenses = Expense::with(['supplier', 'payment_method'])->whereBetween('expense_date', [$from_date, $to_date])->get();
        $expenses = Expense::with(['supplier', 'payment_method'])
            ->leftJoin('projects', 'expenses.project_id', '=', 'projects.id')
            ->whereBetween('expense_date', [$from_date, $to_date])
            ->select('expenses.*', 'projects.name as exp_project_name')
            ->get();

        if (count($incomes) > count($expenses)) {
            $income_expense_arr = $incomes->toArray();
        } else {
            $income_expense_arr = $expenses->toArray();
        }
        $final_arr = [];
        foreach ($income_expense_arr as $key => $value) {
            $final_arr[$key]['expense_payment_date'] = isset($expenses[$key]) ? ($expenses[$key]->expense_date ? changeDateFormatAtExport($expenses[$key]->expense_date) : '') : '';
            $final_arr[$key]['supplier'] = isset($expenses[$key]) ? $expenses[$key]->supplier->supplier_business_name : '';
            $final_arr[$key]['expense_description'] = isset($expenses[$key]) ? $expenses[$key]->expense_description : '';
            $final_arr[$key]['supplier_invoice_number'] = isset($expenses[$key]) ? $expenses[$key]->supplier_invoice_number : '';
            $final_arr[$key]['expense_amount'] = isset($expenses[$key]) ? getPrice($expenses[$key]->expense_amount, 'N') : '';
            $final_arr[$key]['expense_gst'] = isset($expenses[$key]) ? ($expenses[$key]->expense_tax == 'GST Inclusive' || $expenses[$key]->expense_tax == 'GST' ? 'Yes' : 'No')  : '';
            $final_arr[$key]['expense_gst_paid'] = isset($expenses[$key]) ? getGstPriceForExpense($expenses[$key]->expense_tax, $expenses[$key]->expense_amount, 'N') : null;
            // Guard against null payment_method relationship
            $final_arr[$key]['expense_payment_method'] = isset($expenses[$key]) ? optional($expenses[$key]->payment_method)->payment_method_name : '';
            $final_arr[$key]['tax_type'] = isset($expenses[$key]) ? $expenses[$key]->expense_tax : '';
            $final_arr[$key]['expense_category'] = isset($expenses[$key]) ? getExpenseCategory($expenses[$key]->supplier_expense_category) : '';
            $final_arr[$key]['expense_project_name'] = isset($expenses[$key]) ? $expenses[$key]->exp_project_name : '';
            $final_arr[$key]['blank_col'] = null;
            $final_arr[$key]['income_payment_date'] = isset($incomes[$key]) ? ($incomes[$key]->invoice_payment_date ? changeDateFormatAtExport($incomes[$key]->invoice_payment_date) : '') : '';
            $final_arr[$key]['client'] = isset($incomes[$key]) ? $incomes[$key]->client->client_business_name : '';
            $final_arr[$key]['notes'] = isset($incomes[$key]) ? $incomes[$key]->invoice_notes : '';
            $final_arr[$key]['invoice_number'] = isset($incomes[$key]) ? $incomes[$key]->invoice_number : '';
            $final_arr[$key]['invoice_grand_total'] = isset($incomes[$key]) ? getPrice($incomes[$key]->invoice_grand_total, 'N') : '';
            $final_arr[$key]['invoice_gst'] = isset($incomes[$key]) ? getPrice($incomes[$key]->invoice_grand_gst, 'N') : '';
            $final_arr[$key]['invoice_product_category'] = isset($incomes[$key]) ? getInvoiceProductCategories($incomes[$key]->id) : '';
            $final_arr[$key]['invoice_project_name'] = isset($incomes[$key]) ? $incomes[$key]->inc_project_name : '';
            $final_arr[$key]['invoice_date'] = isset($incomes[$key]) ? ($incomes[$key]->invoice_date ? changeDateFormatAtExport($incomes[$key]->invoice_date) : '') : '';
            $final_arr[$key]['invoice_due_date'] = isset($incomes[$key]) ? ($incomes[$key]->invoice_due_date ? changeDateFormatAtExport($incomes[$key]->invoice_due_date) : '') : '';
        }

        // ── NEW: build category summary data while we still have $incomes / $expenses ──
        $this->totalDataRows = count($final_arr);

        $expenseByCategory = [];
        foreach ($expenses as $exp) {
            $cat = getExpenseCategory($exp->supplier_expense_category) ?: 'Uncategorised';
            $expenseByCategory[$cat] = ($expenseByCategory[$cat] ?? 0) + (float) $exp->expense_amount;
        }

        // $incomeByCategory = [];
        // foreach ($incomes as $inv) {
        //     $cat  = getInvoiceProductCategories($inv->id) ?: 'Uncategorised';
        //     $cats = array_map('trim', explode(',', $cat));
        //     foreach ($cats as $c) {
        //         $incomeByCategory[$c] = ($incomeByCategory[$c] ?? 0) + (float) $inv->invoice_grand_total;
        //     }
        // }

        // echo "<pre>"; print_r($incomeByCategory); die;
        $allCategories = array_unique(array_merge(array_keys($expenseByCategory)));
        sort($allCategories);

        $this->categoryData = [];
        foreach ($allCategories as $cat) {
            $income  = $incomeByCategory[$cat]  ?? 0;
            $expense = $expenseByCategory[$cat] ?? 0;
            $this->categoryData[] = [
                'category' => $cat,
                'income'   => $income,
                'expense'  => $expense,
                'profit'   => $income - $expense,
            ];
        }
        // ── END NEW ───────────────────────────────────────────────────────────

        return collect($final_arr);
    }

    public function headings(): array
    {
        return [
            'Expense Payment Date',
            'Supplier',
            'Expense Description',
            'Supplier Invoice No',
            'Amount',
            'GST',
            'GST Paid',
            'Payment Method',
            'Tax Type',
            'Expense Category',
            'Project Name',
            '',
            'Invoice Payment Date',
            'Client',
            'Notes',
            'Invoice No',
            'Grand Total',
            'GST Component',
            'Product Category',
            'Project Name',
            'Invoice Date',
            'Invoice Due Date'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:V1')->getFont()->setBold(true);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:V1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('548135');
                $event->sheet->getDelegate()->getStyle('L1:L1000')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('CE181E');
                // ── NEW: P&L Summary section ──────────────────────────────────
                $sheet = $event->sheet->getDelegate();

                // +2 = 1 heading row + 1 blank spacer row
                $summaryStart = $this->totalDataRows + 3;

                // Section title
                $titleRow = $summaryStart;
                $sheet->mergeCells("A{$titleRow}:B{$titleRow}");
                $sheet->setCellValue("A{$titleRow}", 'Profit & Loss Summary by Category');
                $sheet->getStyle("A{$titleRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Column headings
                $headRow = $summaryStart + 1;
                $sheet->setCellValue("A{$headRow}", 'Category');
                //$sheet->setCellValue("B{$headRow}", 'Total Income');
                $sheet->setCellValue("B{$headRow}", 'Total Expense');
                // $sheet->setCellValue("D{$headRow}", 'Profit / Loss');
                // $sheet->setCellValue("E{$headRow}", 'Margin %');
                $sheet->getStyle("A{$headRow}:B{$headRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF548135']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Category data rows
                $row = $headRow + 1;
                foreach ($this->categoryData as $i => $cd) {
                    $sheet->setCellValue("A{$row}", $cd['category']);
                    // $sheet->setCellValue("B{$row}", $cd['income']);
                    $sheet->setCellValue("B{$row}", $cd['expense']);
                    // $sheet->setCellValue("D{$row}", "=B{$row}-C{$row}");
                    // $sheet->setCellValue("E{$row}", "=IF(B{$row}=0,\"-\",D{$row}/B{$row})");

                    // Alternate row background
                    $bg = $i % 2 === 0 ? 'FFF2F2F2' : 'FFFFFFFF';
                    $sheet->getStyle("A{$row}:B{$row}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($bg);

                    // Green = profit, Red = loss
                    // if ($cd['profit'] > 0) {
                    //     $sheet->getStyle("D{$row}")->getFont()->getColor()->setARGB('FF217346');
                    //     $sheet->getStyle("D{$row}")->getFont()->setBold(true);
                    // } elseif ($cd['profit'] < 0) {
                    //     $sheet->getStyle("D{$row}")->getFont()->getColor()->setARGB('FFCE181E');
                    //     $sheet->getStyle("D{$row}")->getFont()->setBold(true);
                    // }

                    $sheet->getStyle("A{$row}:B{$row}")->getFont()->setName('Arial')->setSize(10);
                    $row++;
                }

                // Totals row
                $firstDataRow = $headRow + 1;
                $lastDataRow  = $row - 1;

                $sheet->getStyle("B{$firstDataRow}:B{$lastDataRow}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                //     $sheet->setCellValue("A{$row}", 'TOTAL');
                //    // $sheet->setCellValue("B{$row}", "=SUM(B{$firstDataRow}:B{$lastDataRow})");
                //     $sheet->setCellValue("B{$row}", "=SUM(B{$firstDataRow}:B{$lastDataRow})");
                //     // $sheet->setCellValue("D{$row}", "=B{$row}-C{$row}");
                //     // $sheet->setCellValue("E{$row}", "=IF(B{$row}=0,\"-\",D{$row}/B{$row})");
                //     $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                //         'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                //         'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']],
                //     ]);

                // Currency format on Income / Expense / Profit columns
                // $sheet->getStyle("B{$firstDataRow}:D{$row}")
                //     ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                // // Percentage format on Margin column
                // foreach (range($firstDataRow, $row) as $r) {
                //     $sheet->getStyle("E{$r}")->getNumberFormat()->setFormatCode('0.0%');
                // }

                // Border around whole summary table
                $sheet->getStyle("A{$headRow}:B{$row}")->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF1F3864']],
                        'inside'  => ['borderStyle' => Border::BORDER_THIN,   'color' => ['argb' => 'FFCCCCCC']],
                    ],
                ]);

                // Summary columns width
                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(18);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(12);
                // ── END NEW ───────────────────────────────────────────────────
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'G' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'Q' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'R' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'M' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'U' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'V' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }
}
