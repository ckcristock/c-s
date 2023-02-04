<?php

namespace App\Exports;

use App\Models\PayrollPayment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class NominaExport implements FromView, WithEvents, ShouldAutoSize, WithColumnFormatting
{

    public function __construct($nomina)
    {
        $this->nomina = $nomina;
    }

    public function view(): View
    {
        return view('exports.NominaReport', ['nomina'=>$this->nomina]);
        //return view('exports.testReport');
    }

    public function registerEvents(): array
    {
        return [];
    }

    public function columnFormats(): array
    {
        return [
            'C'=>NumberFormat::FORMAT_NUMBER,
            'D'=>NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
