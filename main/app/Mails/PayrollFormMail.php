<?php

namespace App\Mails;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayrollFormMail extends Mailable
{
    use Queueable, SerializesModels;
    public $funcionario;
    public $fin_periodo;
    public $inicio_periodo;
    public $diff_meses;
    public $diff_meses_restantes;
    public $diff_years;
    public $diff_dias;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($funcionario, $fin_periodo, $inicio_periodo,$diff_meses_restantes, $diff_years, $diff_dias)
    {
        $this->funcionario =$funcionario;
        $this->fin_periodo =$fin_periodo;
        $this->inicio_periodo =$inicio_periodo;
        $this->diff_meses_restantes =$diff_meses_restantes;
        $this->diff_years =$diff_years;
        $this->diff_dias =$diff_dias;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $title = 'Comprobante de pago de nómina del ' . Carbon::createFromFormat('Y-m-d H:i:s',$this->fin_periodo)->format('d-M-Y');
        return $this->view('mails.payroll_email')->subject($title);
    }
}
