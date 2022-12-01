<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use PHPUnit\Framework\MockObject\Api;

class QuotationController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(Quotation::get());
    }

    public function paginate(Request $request)
    {
        return $this->success(
            Quotation::with('municipality', 'client')
                ->when($request->city, function ($q, $fill) {
                    $q->whereHas('municipality', function ($query) use ($fill) {
                        $query->where('name', 'like', '%' . $fill . '%');
                    });
                })
                ->when($request->client, function ($q, $fill) {
                    $q->whereHas('client', function ($query) use ($fill) {
                        $query->where('social_reason', 'like', '%' . $fill . '%');
                    });
                })
                ->when($request->description, function ($q, $fill) {
                    $q->where('description', 'like', '%' . $fill . '%');
                })
                ->when($request->code, function ($q, $fill) {
                    $q->where('code', 'like', '%' . $fill . '%');
                })
                ->when($request->status, function ($q, $fill) {
                    $q->where('status', $fill);
                })
                ->paginate(Request()->get('pageSize', 10), ['*'], 'page', Request()->get('page', 1))
        );
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
        $quotation = Quotation::find($id);
        $data = $request->all();
        $quotation->fill($data)->save();
        return $this->success('Actualizado con éxito');
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
