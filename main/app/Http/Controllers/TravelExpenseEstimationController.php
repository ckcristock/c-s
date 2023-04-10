<?php

namespace App\Http\Controllers;

use App\Models\TravelExpenseEstimation;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TravelExpenseEstimationController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(
            TravelExpenseEstimation::get()->map(function ($travelExpense) {
                $travelExpense->displacement = json_decode($travelExpense->displacement);
                $travelExpense->destination = json_decode($travelExpense->destination);
                return $travelExpense;
            })
        );
    }

    public function paginate()
    {
        $pageSize = request()->get('pageSize', 50);
        $page = request()->get('page', 1);
        $travelExpenses = TravelExpenseEstimation::when(request()->get('description'), function ($q, $fill) {
            $q->where('description', 'like', '%' . $fill . '%');
        })
            ->get();
        $transformedTravelExpenses = $travelExpenses->map(function ($travelExpense) {
            $travelExpense->displacement = json_decode($travelExpense->displacement);
            $travelExpense->destination = json_decode($travelExpense->destination);
            return $travelExpense;
        });
        $paginatedTravelExpenses = new LengthAwarePaginator(
            $transformedTravelExpenses->forPage($page, $pageSize),
            $transformedTravelExpenses->count(),
            $pageSize,
            $page
        );
        return $this->success($paginatedTravelExpenses);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['displacement'] = json_encode($request->displacement);
            $data['destination'] = json_encode($request->destination);
            //dd($data);
            $travelExpense = TravelExpenseEstimation::updateOrCreate(['id' => $request->get('id')], $data);
            return ($travelExpense->wasRecentlyCreated) ? $this->success('Creado con éxito') : $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return  $this->errorResponse([$th->getMessage(), $th->getFile(), $th->getLine()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
