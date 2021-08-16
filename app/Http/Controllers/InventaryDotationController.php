<?php

namespace App\Http\Controllers;

use App\Models\InventaryDotation;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventaryDotationController extends Controller
{
    use ApiResponser;
    //

    public function index()
    {
        $page = Request()->get('page');
        $page = $page ? $page : 1;

        $pageSize = Request()->get('pageSize');
        $pageSize = $pageSize ? $pageSize : 10;

        return $this->success(
            InventaryDotation::orderBy('id', 'DESC')->paginate($pageSize, '*', 'page', $page)
        );
    }

    public function getInventary()
    {
        $id = Request()->get('inventary_dotation_group_id');
        $d = DB::select('SELECT * FROM inventary_dotation_groups GI
        INNER JOIN inventary_dotations ID ON GI.id = ID.inventary_dotation_group_id
        WHERE GI.id = "' . $id . '"');

        return $this->success( $d );
    }
}
