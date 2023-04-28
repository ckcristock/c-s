<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;

class BoardController extends Controller
{
    use ApiResponser;

    public function getData()
    {
        $board = DB::table('boards')->get();
        return $this->success($board);
    }
    
    public function setBoardsPerson($personId, $boards)
    {
        DB::table('users')->where('person_id', $personId)->update(['board_id' => $boards]);
    }

    public function personBoards($personId)
    {
        $board = DB::table('users')
            ->where('person_id', $personId)
            ->select('board_id')
            ->get();
        return $this->success($board);
    }
}
