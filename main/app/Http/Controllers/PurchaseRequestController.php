<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseRequest;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PurchaseRequestController extends Controller
{

    use ApiResponser;

    public function getProducts(Request $request)
    {
        return $this->success(
            Product::where('Id_Categoria', $request->category_id)->with('unit')
                ->when($request->search, function ($query, $fill) {
                    $query->where('Nombre_Comercial', 'like', "%$fill%");
                })->get(['*', 'Nombre_Comercial as name'])->take(10)
        );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        try{
            $purchaseRequest=PurchaseRequest::updateOrcreate([
            'category_id' => $request->input('category_id'),
            'expected_date' => $request->input('expected_date'),
            'observations' => $request->input('observations')
        ]);
        
        $products = $request->input('products');
        foreach($products as $product){
            $purchaseRequest->productPurchaseRequest()->updateOrCreate([
                'product_id'=>$product['id'],
                'name'=>$product['name'],
                'ammount'=>$product['ammount']
            ]);
        }

        return $this->success('Creado con éxito');
        
        }catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseRequest $purchaseRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseRequest $purchaseRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseRequest $purchaseRequest)
    {
        //
    }
}
