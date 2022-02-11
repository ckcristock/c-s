<?php

namespace App\Exports;

use App\Services\LunchDownloadService;
use Illuminate\Database\Eloquent\Collection;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LunchExport implements FromCollection,  WithHeadings, ShouldAutoSize, WithEvents
{
    private $dates ;
    public function __construct($dates)
    {
        $this->dates = $dates;
    }
    public function headings(): array
    {
        return [
          'Funcionario', 'Func. creó', 'Valor', 'Fecha'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

         return new Collection(LunchDownloadService::getLunches($this->dates));

    }

    public function registerEvents(): array
    {

        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }

}
