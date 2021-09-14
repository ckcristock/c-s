<?php

namespace App\Http\Controllers;

use App\Services\PayrollService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    //

    use ApiResponser;


    function nextMonths()
    {
        try {
            return $this->success( PayrollService::getQuincena() );
        } catch (\Throwable $th) {
            return $this->error($th->getMessage().$th->getLine(),402);
        }
    }
}
