<?php

namespace App\Exports;

use App\Models\PayrollFactor;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;


class PayrollFactorExport implements FromCollection,  WithHeadings, ShouldAutoSize, WithEvents
{
    private $filter;
    public function __construct($dates)
    {
        $this->filter = $dates;
    }
    public function headings(): array
    {
        return [
            'Nombres y apellidos', 'Fecha de creación', 'Novedad', 'Descripción', 'Inicio', 'Fin'
        ];
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //echo $this->filter->type;
        return new Collection(
            /* PayrollFactor::query()->with(['person' => function ($query) {
                $query->select('first_name', 'first_surname');
            }])
            ->select('*')
            ->get() */
            DB::table('payroll_factors')
                ->when(request()->get('personfill'), function ($q, $fill) {
                    $q->where('person_id', 'like', '%' . $fill . '%');
                })
                ->when(request()->get('date_end'), function ($q, $fill) {
                    $q->where('date_start', '>=', request()->get('date_start'))
                        ->where('date_end', '<=', request()->get('date_end'));
                })
                ->when(request()->get('date_start'), function ($q, $fill) {
                    $q->where('date_start', '>=', request()->get('date_start'))
                        ->where('date_end', '<=', request()->get('date_end'));
                })
                ->when(request()->get('type'), function ($q, $fill) {
                    $q->where('disability_leave_id', 'like', '%' . $fill . '%');
                })
                ->join('people', 'payroll_factors.person_id', '=', 'people.id')
                ->join('disability_leaves', 'payroll_factors.disability_leave_id', '=', 'disability_leaves.id')
                ->select(
                    DB::raw("CONCAT(people.first_name, ' ', people.first_surname)"),
                    DB::raw('DATE_FORMAT(payroll_factors.created_at, "%d-%m-%Y")'),
                    'disability_leaves.concept',
                    'payroll_factors.observation',
                    DB::raw('DATE_FORMAT(payroll_factors.date_start, "%d-%m-%Y")'),
                    DB::raw('DATE_FORMAT(payroll_factors.date_end, "%d-%m-%Y")'),
                )
                ->orderByDesc('payroll_factors.date_start')
                ->get()
        );
    }

    public function registerEvents(): array
    {

        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }
}
