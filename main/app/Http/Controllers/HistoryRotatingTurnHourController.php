<?php

namespace App\Http\Controllers;

use App\Models\HistoryRotatingTurnHour;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class HistoryRotatingTurnHourController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pageSize = Request()->get('pageSize', 10);
        $page = Request()->get('page', 1);

        $items = HistoryRotatingTurnHour::with('rotating_turn_hour', 'person')
            ->groupBy('batch')
            ->orderBy('batch', 'desc')
            ->paginate($pageSize, ['*'], 'page', $page);

        // Reestructurar los resultados dentro del objeto paginado
        $result = $items->map(function ($item) {
            return [
                'batch' => $item->batch,
                'elements' => $item->where('batch', $item->batch)->with('rotating_turn_hour', 'person')->get()->toArray(),
                'created_at' => $item->created_at,
                'person' => $item->person,
                'show' => false

            ];
        });

        // Reemplazar los resultados paginados con los resultados reestructurados
        $items->setCollection($result);

        return $this->success($items);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HistoryRotatingTurnHour  $historyRotatingTurnHour
     * @return \Illuminate\Http\Response
     */
    public function show(HistoryRotatingTurnHour $historyRotatingTurnHour)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HistoryRotatingTurnHour  $historyRotatingTurnHour
     * @return \Illuminate\Http\Response
     */
    public function edit(HistoryRotatingTurnHour $historyRotatingTurnHour)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HistoryRotatingTurnHour  $historyRotatingTurnHour
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HistoryRotatingTurnHour $historyRotatingTurnHour)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HistoryRotatingTurnHour  $historyRotatingTurnHour
     * @return \Illuminate\Http\Response
     */
    public function destroy(HistoryRotatingTurnHour $historyRotatingTurnHour)
    {
        //
    }
}
