<?php

use App\Models\Company;

if (!function_exists('getCabecera')) {
    function getCabecera($table)
    {
        $company = Company::first();
        $image = $company->page_heading;
        //return $consecutivo;
    }
}
