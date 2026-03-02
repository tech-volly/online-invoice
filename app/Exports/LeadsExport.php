<?php

namespace App\Exports;

use App\Models\Lead;
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

class LeadsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function collection() {
        $leads = Lead::with(['lead_follow_ups'])->orderBy('id','desc')->get();
        foreach($leads as $key => $val) {
            $data[$key] = [];
            $data[$key]['name'] = $val->client_first_name.' '.$val->client_last_name;
            $data[$key]['company_name'] = $val->company_name;
            $data[$key]['email'] = $val->client_email;
            $data[$key]['mobile'] = $val->client_mobile;
            $data[$key]['discussion_date'] = getLeadDiscussionDate($val->id, 'N');
            $data[$key]['follow_up'] = getLeadFollowUpDetails($val->id, 'N');
            $data[$key]['enquiry_date'] = $val->created_at ? changeDateFormatAtExport($val->created_at) : '';
        }

        return collect($data);
    }

    public function headings(): array {
        return [
            'Name',
            'Company Name',
            'Email',
            'Mobile',
            'Discussion Date',
            'FollowUp',
            'Enquiry Date'
        ];
    }

    public function columnFormats(): array {
        return [
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:G1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('70AD47');
            },
        ];
    }
}
