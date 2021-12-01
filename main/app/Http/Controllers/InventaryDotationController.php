<?php

namespace App\Http\Controllers;

use App\Models\InventaryDotation;
use App\Models\ProductDotationType;
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
        $page = Request()->get('page');
        $page = $page ? $page : 1;
        $pageSize = Request()->get('pageSize');
        $pageSize = $pageSize ? $pageSize : 10;

        $d = DB::table('inventary_dotations as ID')
            ->select('ID.stock', 'ID.code', 'ID.name', 'ID.size', 'ID.status', 'ID.type', 'ID.cost', 'ID.id')
            ->selectRaw('(SELECT IFNULL(SUM(quantity), 0)
                            FROM dotation_products DP
                            INNER JOIN dotations D ON D.id = DP.dotation_id
                            WHERE DP.inventary_dotation_id = ID.id
                            AND (D.delivery_state <> "Entregado" OR D.delivery_state <> "Anulado")) as cantidadA')

            ->when(Request()->get('type'),  function ($q, $fill){
            $q->where('ID.type', $fill);
             })

            ->when(Request()->get('type'),  function ($q, $fill){
            $q->where('ID.type', $fill);
             })
            ->when(Request()->get('name'),  function ($q, $fill){
            $q->where('ID.name', 'like', '%' . $fill . '%');
             })

            ->when(Request()->get('entrega'),  function ($q, $fill){

                $q->havingRaw('ID.stock - cantidadA > 0');
            // $q->where('ID.type', $fill);
             })

            ->paginate($pageSize, '*', 'page', $page);

        return $this->success($d);

    }

    public function getSelected(){

        $page = Request()->get('page');
        $page = $page ? $page : 1;
        $pageSize = Request()->get('pageSize');
        $pageSize = $pageSize ? $pageSize : 10;
        $id = Request()->get('id');

        $d = DB::table('inventary_dotations as ID')
        ->select(DB::raw(' CONCAT(P.first_name," ",P.first_surname) as nameF '),
                'PD.created_at',
                'D.delivery_code',
                'PD.quantity',
                'D.type',
                'D.delivery_state'
        )
                ->join('dotation_products AS PD', 'PD.inventary_dotation_id', '=', 'ID.id')
                ->join('dotations AS D', 'D.id' , '=', 'PD.Dotation_id')
                ->join('people AS  P', 'P.id', '=', 'D.person_id')

         ->where([
                    ['ID.id', $id],
                    ['D.delivery_state', '<>',  "Entregado"],
                    ['D.delivery_state', '<>',  "Anulado"],
                ])
        ->paginate($pageSize, '*', 'page', $page);

    return $this->success($d);
    }


    public function getInventaryEpp()
    {
        $d = ProductDotationType::with('inventary')
            ->whereHas('inventary', function ($q) {
            {
                $q->where('type','EPP');
            }
            })
            ->get();
        return $this->success($d);
    }

    public function indexGruopByCategory(Request $request)
    {
        $d = DB::select(
            'SELECT CPD.name ,SUM(stock) stock
            FROM inventary_dotations ID
            INNER JOIN product_dotation_types CPD
            ON ID.product_dotation_type_id = CPD.id
            GROUP BY ID.product_dotation_type_id ',
        );
        return $this->success($d);
    }


    public function statistics(Request $request)
    {

        $d = DB::table('dotations as D')
        ->selectRaw('IFNULL(SUM(D.cost), 0) as totalCostoMes, IFNULL(count(*),0) as totalMes')
        // ->join('dotation_products AS PD', 'PD.dotation_id', '=', 'D.id')
        // ->join('inventary_dotations AS ID', 'ID.id', '=', 'PD.inventary_dotation_id')

                 ->when(Request()->get('delivery'), function ($q, $fill) {
                    $q->where('D.delivery_state', $fill );
                   })

                //  ->when(Request()->get('art'), function ($q, $fill) {
                //     $q->where('ID.name', 'like', '%' . $fill . '%');
                //     })

                ->when(Request()->get('cod'), function ($q, $fill) {
                    $q->where('D.delivery_code', 'like', '%' . $fill . '%');
                })

                ->when(Request()->get('person'),  function ($q, $fill){
                $q->where('D.user_id', $fill);
                    })
                ->when(Request()->get('persontwo'),  function ($q, $fill){
                $q->where('D.person_id', $fill);
                    })

                ->when(Request()->get('firstDay'), function ($q, $fill) {
                $q->whereDate('D.dispatched_at', '>=' , $fill );
                    })

                ->when(Request()->get('lastDay'), function ($q, $fill) {
                $q->whereDate('D.dispatched_at', '<=', $fill );
                    })

                ->when(Request()->get('type'),  function ($q, $fill){
                $q->where('D.type', $fill);
                })->get();




        $dyear = DB::table('dotations as D')
                ->selectRaw('IFNULL(SUM(D.cost), 0) as totalCostoAnual, IFNULL(count(*),0) as totalAnual')

                // ->join('dotation_products AS PD', 'PD.dotation_id', '=', 'D.id')
                // ->join('inventary_dotations AS ID', 'ID.id', '=', 'PD.inventary_dotation_id')


                ->when(Request()->get('delivery'), function ($q, $fill) {
                    $q->where('D.delivery_state', $fill );
                   })

                //  ->when(Request()->get('art'), function ($q, $fill) {
                //     $q->where('ID.name', 'like', '%' . $fill . '%');
                //     })

                 ->when(Request()->get('cod'), function ($q, $fill) {
                    $q->where('D.delivery_code', 'like', '%' . $fill . '%');
                   })

                ->when(Request()->get('person'),  function ($q, $fill){
                $q->where('D.user_id', $fill);
                    })

                ->when(Request()->get('persontwo'),  function ($q, $fill){
                    $q->where('D.person_id', $fill);
                        })

                ->when(Request()->get('firstDay'), function ($q, $fill) {
                $q->whereDate('D.dispatched_at', '>=' , $fill );
                    })

                ->when(Request()->get('lastDay'), function ($q, $fill) {
                $q->whereDate('D.dispatched_at', '<=', $fill );
                    })

                ->when(Request()->get('type'),  function ($q, $fill){
                $q->where('D.type', $fill);
                })->get();


        return $this->success(['month' => $d[0], 'year' => $dyear[0]]);


               // $date = explode('-', $request->get('cantMes'));

        // $d = DB::select('SELECT ifnull(count(*),0) as totalMes,
        //  ifnull(SUM(D.cost),0) as totalCostoMes
        //  FROM dotations D
        //  where DATE(D.dispatched_at) BETWEEN "'.$firstDay.'" and "'.$lastDay.'"
        //         AND D.state = "Activa" or D.person_id = "'.$person.'" ');



        // $dyear = DB::select('SELECT count(*) as totalAnual,
        //  ifnull(SUM(D.cost),0) as totalCostoAnual
        //  FROM dotations D
        //  where DATE(D.dispatched_at) BETWEEN "'.$firstDay.'" and "'.$lastDay.'"
        //         AND D.state = "Activa" or D.person_id = "'.$person.'" ');
    }


}
