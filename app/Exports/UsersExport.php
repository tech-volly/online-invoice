<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DB;

class UsersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        
        $users = DB::table('users')
            ->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftjoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->leftjoin('departments', 'departments.id', '=', 'users.department_id')
            ->where('users.deleted_at', '=', null)
            ->select('users.first_name', 'users.last_name', 'users.email', 'users.phone_number', 'departments.name as department_name', 'roles.name as role_name')
            ->get(); 

        return collect($users);
    }

    public function headings(): array {
        return [
            'First Name',
            'Last Name',
            'Email',
            'Phone Number',
            'Department Name',
            'Role Name'
        ];
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:F1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('70AD47');
            },
        ];
    }
}
