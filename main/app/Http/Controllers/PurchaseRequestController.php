<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPurchaseRequest;
use App\Models\PurchaseRequest;
use App\Models\Quotation;
use App\Models\QuotationPurchaseRequest;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

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
    }



    public function paginate(Request $request)
    {
        return $this->success(
            PurchaseRequest::with('productPurchaseRequest', 'person')
                ->when($request->code, function ($q, $fill) {
                    $q->where('code', 'like', "%$fill%");
                })
                ->when($request->start_created_at, function ($q, $fill) use ($request) {
                    $q->where('created_at', '>=', $fill)
                        ->where('created_at', '<=', $request->end_created_at . ' 23:59:59');
                })
                ->when($request->start_expected_date, function ($q, $fill) use ($request) {
                    $q->whereBetween('expected_date', [$fill, $request->end_expected_date]);
                })
                ->when($request->status, function ($q, $fill) {
                    $q->where('status', 'like', "%$fill%");
                })
                ->when($request->funcionario, function ($q, $fill) {
                    $q->whereHas('person', function ($query) use ($fill) {
                        $query->where('first_name', 'like', "%$fill%");
                    });
                })
                ->orderByDesc('created_at')
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
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
        try {
            $products_delete = $request->products_delete;
            $data = $request->except('products', 'products_delete');
            $products = $request->products;
            $data['user_id'] = auth()->user()->id;
            if (!$request->id) {
                $data['code'] = generateConsecutive('purchase_requests');
            }
            $data['quantity_of_products'] = count($products);
            $purchaseRequest = PurchaseRequest::updateOrcreate(
                ['id' => $request->id],
                $data
            );
            if (count($products_delete) > 0) {
                foreach ($products_delete as $product) {
                    ProductPurchaseRequest::find($product)->delete();
                }
            }
            foreach ($products as $product) {
                $purchaseRequest->productPurchaseRequest()->updateOrCreate(['id' => $product['id']], $product);
            }
            if (!$request->id) {
                sumConsecutive('purchase_requests');
            }
            return $this->success('Creado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->success(PurchaseRequest::with('productPurchaseRequest', 'person')->find($id));
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

    public function saveQuotationPurchaseRequest(Request $request)
    {
        $items = $request->items;
        $code = generateConsecutive('quotation_purchase_requests');
        foreach ($items as $key => $value) {
            $value['code'] = $code . '-' . ($key + 1);
            $base64 = saveBase64File($value["file"], 'cotizaciones-solicitud-compra/', false, '.pdf');
            $value['file'] = URL::to('/') . '/api/file-view?path=' . $base64;
            QuotationPurchaseRequest::create($value);
            $product = ProductPurchaseRequest::find($value['product_purchase_request_id']);
            $product->update(['status' => 'Cotizaciones cargadas']);
        }
        PurchaseRequest::find($product->purchase_request_id)->update(['status' => 'Cotizada']);
        sumConsecutive('quotation_purchase_requests');
        return $this->success('holi');
    }

    public function getQuotationPurchaserequest($id)
    {
        $quotation = QuotationPurchaseRequest::where('product_purchase_request_id', $id)->with('thirdParty')->get();
        return $this->success($quotation);
    }

    public function saveQuotationApproved($id)
    {
        $quotationPurchase = QuotationPurchaseRequest::find($id);
        $quotationPurchase->update(['status' => 'Aprobada']);
        $quotationIds =
            QuotationPurchaseRequest::where('product_purchase_request_id', $quotationPurchase->product_purchase_request_id)
                ->where('id', '<>', $id)
                ->pluck('id');
        QuotationPurchaseRequest::whereIn('id', $quotationIds)->update(['status' => 'Rechazada']);

        /**
         *modificacion migracion enum falta Cotizacion aprobada
         *actualizar status a Cotizacion aprobada de un producto
         *recorrer productos que tengas el mismo purchase_request_id y si ya todos estan aprobados entonces que el status de purchase_request se actualice a aprobada
         * agregar a tabla principal en el front cantidad de productos cotizados
         * condicionar todos los botones (cargar, aprobar, ver, nueva order)
         * modal de ver cotizacion aprobada
         * boton de cotizacion general
         * actividad
         */

        return $this->success('Operación existosa');
    }
}
