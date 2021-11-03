<?php

namespace App\Services;

use App\Models\ApuPart;
use Exception;
use Illuminate\Support\Facades\Http;

class ApuPArtService
{
    static function saveApu($data)
	{
	   return ApuPart::create($data);
	}
}
