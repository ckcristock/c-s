<?php

namespace App\Http\Controllers;

use App\Exports\DotationExport;
use App\Exports\DownloaDeliveriesExport;
use App\Models\InventaryDotation;
use App\Models\ProductDotationType;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LateArrivalExport;



class InventaryDotationController extends Controller
{
    use ApiResponser;
    //

    public function index(Request $request)
    {
        $page = Request()->get('page');
        $page = $page ? $page : 1;

        $pageSize = Request()->get('pageSize');
        $pageSize = $pageSize ? $pageSize : 10;

        return $this->success(
            InventaryDotation::when($request->code, function ($q, $fill) {
                $q->where('code', 'like', '%' . $fill . '%');
            })
            ->when($request->nombre, function ($q, $fill) {
                $q->where('name', 'like', '%' . $fill . '%');
            })
            ->when($request->calidad, function ($q, $fill) {
                $q->where('status', 'like', '%' . $fill . '%');
            })
            ->when($request->tipo, function ($q, $fill) {
                $q->where('type', 'like', '%' . $fill . '%');
            })
            ->when($request->talla, function ($q, $fill) {
                $q->where('size', 'like', '%' . $fill . '%');
            })
            ->orderBy('id', 'DESC')->paginate($pageSize, '*', 'page', $page)
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

    public function getTotatInventary(){
        $d = DB::table('inventary_dotations as id')
        ->selectRaw('pdt.name, SUM(id.stock) as value')
        // ->join('dotation_products AS dp', 'dp.dotation_id', '=', 'D.id')
        // ->join('inventary_dotations AS id', 'id.id', '=', 'dp.inventary_dotation_id')
        ->join('product_dotation_types AS pdt', 'pdt.id', '=', 'id.product_dotation_type_id')


        ->when(Request()->get('person'),  function ($q, $fill){
            $q->where('D.user_id', $fill);
                })
        ->when(Request()->get('persontwo'), function ($q, $fill) {
            $q->where('D.person_id', $fill);
        })

        ->when(Request()->get('cod'), function ($q, $fill) {
            $q->where('D.delivery_code', 'like', '%' . $fill . '%');
        })

        ->when(Request()->get('type'), function ($q, $fill) {
            $q->where('D.type', 'like', '%' . $fill . '%');
        })

        ->when(Request()->get('delivery'), function ($q, $fill) {
            $q->where('D.delivery_state', $fill);
        })


        ->when(Request()->get('firstDay'), function ($q, $fill) {
        $q->whereDate('D.dispatched_at', '>=' , $fill );
            })

        ->when(Request()->get('lastDay'), function ($q, $fill) {
        $q->whereDate('D.dispatched_at', '<=', $fill );
            })
    ->groupBy('pdt.id')
    ->get();

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

                 ->when(Request()->get('delivery'), function ($q, $fill) {
                    $q->where('D.delivery_state', $fill );
                   })

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

                ->when(Request()->get('delivery'), function ($q, $fill) {
                    $q->where('D.delivery_state', $fill );
                   })

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



        $td = DB::table('dotations as D')
                ->selectRaw(' IFNULL(count(*),0) as totalDotacion')

                ->when(Request()->get('delivery'), function ($q, $fill) {
                    $q->where('D.delivery_state', $fill );
                   })

                 ->when(Request()->get('cod'), function ($q, $fill) {
                    $q->where('D.delivery_code', 'like', '%' . $fill . '%');
                   })

                ->when(Request()->get('person'),  function ($q, $fill){
                $q->where('D.user_id', $fill);
                    })

                ->when(Request()->get('persontwo'),  function ($q, $fill){
                    $q->where('D.person_id', $fill);
                        })


                ->when(Request()->get('type'),  function ($q, $fill){
                $q->where('D.type', $fill);
                })
                ->where('D.type', 'Dotacion')
                ->get();

            $te = DB::table('dotations as D')
                ->selectRaw(' IFNULL(count(*),0) as totalEpp')

                ->when(Request()->get('delivery'), function ($q, $fill) {
                    $q->where('D.delivery_state', $fill );
                   })

                 ->when(Request()->get('cod'), function ($q, $fill) {
                    $q->where('D.delivery_code', 'like', '%' . $fill . '%');
                   })

                ->when(Request()->get('person'),  function ($q, $fill){
                $q->where('D.user_id', $fill);
                    })

                ->when(Request()->get('persontwo'),  function ($q, $fill){
                    $q->where('D.person_id', $fill);
                        })


                ->when(Request()->get('type'),  function ($q, $fill){
                $q->where('D.type', $fill);
                })
                ->where('D.type', 'EPP')
                ->get();






        return $this->success(['month' => $d[0], 'year' => $dyear[0], 'td' => $td[0], 'te' => $te[0]]);


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

    public function download($fechaInicio, $fechaFin,Request $req)
    {
        $dates = [$fechaInicio,$fechaFin];

        return Excel::download(new DotationExport($dates), 'inventario.xlsx');
        return 'asd';
    }

    public function downloadeliveries($fechaInicio, $fechaFin,Request $req)
    {
        $dates = [$fechaInicio,$fechaFin];

        return Excel::download(new DownloaDeliveriesExport($dates), 'downloadeliveries.xlsx');
        return 'asd';
    }


}
