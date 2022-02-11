<?php

namespace App\Services;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;

class LunchDownloadService
{
    static public function getLunches($dates)
    {
        return DB::table('lunches as l')
            ->select(
                DB::raw('concat(p.first_name," ",p.first_surname) as person'),
                DB::raw('concat(user.first_name," ",user.first_surname) as user'),
                'l.value',
                'l.created_at'
            )
            ->join('people as p', 'p.id', '=', 'l.person_id')
            ->join('users as u', 'u.id', '=', 'l.user_id')
            ->join('people as user', 'user.id', '=', 'u.person_id')
            // ->whereBetween(DB::raw("DATE(l.created_at)"), $dates)
            ->when($dates, function ($q, $fill) {
                // $q->whereDate('l.created_at', '>=' , $fill[0] );
                $q->whereDate('l.created_at', '>=', $fill[0])
                ->whereDate('l.created_at', '<=', $fill[1]);
            })
            ->when($dates, function ($q, $fill) {
                //  $q->whereDate('l.created_at', '<=', $fill[1] );
                 $q->whereDate('l.created_at', '>=', $fill[0])
                ->whereDate('l.created_at', '<=', $fill[1]);
                })
            ->where('l.state', 'Activo')
            ->get();

    }

}
