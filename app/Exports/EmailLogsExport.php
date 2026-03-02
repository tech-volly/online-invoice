<?php

namespace App\Exports;

use App\Models\EmailLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class EmailLogsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function collection() {
        $logs = EmailLog::orderBy('id','desc')->get();
        foreach($logs as $key => $val) {
            $data[$key] = [];
            $data[$key]['sender'] = $val->email_sender;
            $data[$key]['receiver'] = $val->email_receiver;
            $data[$key]['content'] = $val->email_content;
            $data[$key]['date'] = getFormatedDateTime($val->email_send_date);
        }

        return collect($data);
    }

    public function headings(): array {
        return [
            'Sender',
            'Receiver',
            'Content',
            'Date'
        ];
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:D1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('70AD47');
            },
        ];
    }
}
