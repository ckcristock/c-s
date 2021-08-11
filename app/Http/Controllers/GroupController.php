<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    use ApiResponser;
    //

    public function index(){
        return $this->success(Group::all(['name as text','id as value']));
    }
}
