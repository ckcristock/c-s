<?php

namespace App\Exports;

use App\Models\PayrollPayment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class NovedadesExport implements FromView, WithEvents, ShouldAutoSize, WithColumnFormatting
{

    private $novedades;

    public function __construct($novedades)
    {
        $this->novedades = $novedades;
    }

    public function view(): View
    {
        return view('exports.NovedadesReport', ['novedades'=>$this->novedades]);
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
