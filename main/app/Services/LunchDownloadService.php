<?php

namespace App\Services;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;

class LunchDownloadService
{
    static public function getLunches()
    {
        return DB::table('lunch_people as lp')
            ->select(
                DB::raw('concat(p.first_name," ",p.first_surname) as person'),
                DB::raw('concat(user.first_name," ",user.first_surname) as user'),
                'l.value',
                'l.created_at',
                'l.state'
            )
            ->join('lunches as l', 'l.id', '=', 'lp.lunch_id')
            ->join('people as p', 'p.id', '=', 'lp.person_id')
            ->join('users as u', 'u.id', '=', 'l.user_id')
            ->join('people as user', 'user.id', '=', 'u.person_id')
            ->get();

    }

}
