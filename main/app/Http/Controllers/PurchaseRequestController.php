<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPurchaseRequest;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestActivity;

use App\Models\QuotationPurchaseRequest;
use App\Traits\ApiResponser;
use Carbon\Carbon;
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
            //actividad compra va aquí
            $this->newActivity(
                "Se " . ((!$request->id) ? 'creó' : 'editó') . " la solicitud de compra " . $purchaseRequest->code , 
                (!$request->id) ? 'Creación': 'Edición',
                $purchaseRequest->id
            );

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

    public function newActivity($details, $status, $id)
    {
        PurchaseRequestActivity::create(
            [
                'purchase_request_id' => $id,
                'user_id' => auth()->user()->id,
                'details' => $details,
                'date' => Carbon::now(),
                'status' => $status
            ]
        );
    }
    public function show($id)
    {
        return $this->success(PurchaseRequest::with('productPurchaseRequest', 'person', 'quotationPurchaseRequest', 'activity')->find($id));
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
    //Funcion que guarda cotizaciones cargadas por producto
    public function saveQuotationPurchaseRequest(Request $request)
    {
       // dd($request);
        $items = $request->items;
        //dd($items);
        $code = generateConsecutive('quotation_purchase_requests');


        foreach ($items as $key => $value) {
            $value['code'] = $code . '-' . ($key + 1);
            $base64 = saveBase64File($value["file"], 'cotizaciones-solicitud-compra/', false, '.pdf');
            $value['file'] = URL::to('/') . '/api/file-view?path=' . $base64;
            QuotationPurchaseRequest::create($value);

            if ($value["product_purchase_request_id"]) {
                $product = ProductPurchaseRequest::find($value['product_purchase_request_id']);
                $product->update(['status' => 'Cotizaciones cargadas']);

                $this->newActivity(("Se cotizó el producto ".$product['name'] ." con número de cotización " .  $value['code']), 'Cotización', $product->purchase_request_id);

            }

            if ($value["purchase_request_id"]) {
                $purchase = PurchaseRequest::find($value['purchase_request_id']);
                $products = $purchase->productPurchaseRequest()->get();

                foreach ($products as $product) {
                    $product->update(['status' => 'Cotizaciones cargadas']);
                }

                $this->newActivity(("Se realizó la cotización general numero ". $value['code']. " de la solitiud de compra " .  $purchase['code'] ) , 'Cotización', $purchase-> id);
            }

        }
        
        if ($value["product_purchase_request_id"]) {
            PurchaseRequest::find($product->purchase_request_id)->update(['status' => 'Cotizada']);
        } else {
            PurchaseRequest::find($value['purchase_request_id'])->update(['status' => 'Cotizada']);
        }

        sumConsecutive('quotation_purchase_requests');
        return $this->success('Cotización guardada con éxito');
    }

   

    public function getQuotationPurchaserequest($id, $value)
    {
        // esta validacion no esta funcionando
        if ($value == 'product') {
            $quotation = QuotationPurchaseRequest::with('productPurchaseRequest')
                ->where('product_purchase_request_id', $id)->with('thirdParty')->get();
        } else if ($value == 'purchase') {
            $quotation = QuotationPurchaseRequest::with('purchaseRequest')
                ->where('purchase_request_id', $id)->with('thirdParty')->get();
        } else {
            $quotation = '';
        }
        return $this->success($quotation);
    }

    public function saveQuotationApproved($id)
    {
        // actualizacion status quotations purchase request
        $quotationPurchase = QuotationPurchaseRequest::find($id);
        $quotationPurchase->update(['status' => 'Aprobada']);
        $quotationIds =
            QuotationPurchaseRequest::whereNotNull('product_purchase_request_id')
            ->where('product_purchase_request_id', $quotationPurchase->product_purchase_request_id)
            ->where('id', '<>', $id)
            ->pluck('id');
        //dd($quotationIds);
        $quotationsIdsGeneral =
            QuotationPurchaseRequest::whereNotNull('purchase_request_id')
            ->where('purchase_request_id', $quotationPurchase->purchase_request_id)
            ->where('id', '<>', $id)
            ->pluck('id');
        //dd($quotationsIdsGeneral);
        if (!$quotationIds->isEmpty()) {
            QuotationPurchaseRequest::whereIn('id', $quotationIds)->update(['status' => 'Rechazada']);
        }

        if (!$quotationsIdsGeneral->isEmpty()) {
            QuotationPurchaseRequest::whereIn('id', $quotationsIdsGeneral)->update(['status' => 'Rechazada']);
        }

        if ($quotationPurchase['product_purchase_request_id'] && !$quotationPurchase['purchase_request_id']) {
            $productPurchaseRequest = ProductPurchaseRequest::find($quotationPurchase->product_purchase_request_id);
            $productPurchaseRequest->update(['status' => 'Cotización Aprobada']);

            $this->newActivity("Se aprobó la cotización ". $quotationPurchase->code . " del producto ". $productPurchaseRequest->name  , 'Aprobación', $productPurchaseRequest->purchase_request_id);

            $purchaseRequest = PurchaseRequest::find($productPurchaseRequest->purchase_request_id);
            $allProductsPurchaseRequestApproved = ProductPurchaseRequest::where('purchase_request_id', $purchaseRequest->id)
                ->where('status', '<>', 'Cotización Aprobada')  //valor diff de cotizacion aprobada
                ->count() == 0;
            if ($allProductsPurchaseRequestApproved) {
                $purchaseRequest->update(['status' => 'Aprobada']);
                $this->newActivity("Se aprobarón todas las cotizaciones de la solicitud ".  $purchaseRequest->code , 'Aprobación', $purchaseRequest->id);
            }
        }
        //dd($quotationsIdsGeneral);
        if (!$quotationPurchase['product_purchase_request_id'] && $quotationPurchase['purchase_request_id']) {
            $purchaseRequest = PurchaseRequest::find($quotationPurchase->purchase_request_id);
            $purchaseRequest->update(['status' => 'Aprobada']);


            $productPurchaseRequest = ProductPurchaseRequest::where('purchase_request_id', $purchaseRequest->id);
            $productPurchaseRequest->update(['status' => 'Cotización Aprobada']);
            //dd($purchaseRequest);

            $this->newActivity("Se aprobó la cotización general ".$quotationPurchase->code." de la solicitud de compra ". $purchaseRequest->code , 'Aprobación', $purchaseRequest->id);
        }
        //dd($productPurchaseRequest);

        return $this->success('Operación existosa');
    }
}


/**
 *modificacion migracion enum falta Cotizacion aprobada
 *actualizar status a Cotizacion aprobada de un producto
 *recorrer productos que tengas el mismo purchase_request_id y si ya todos estan aprobados entonces que el status de purchase_request se actualice a aprobada
 * agregar a tabla principal en el front cantidad de productos cotizados
 * condicionar todos los botones (cargar, aprobar, ver, nueva order, editar afuera), que aparezcan y desaparezcan según status de la solicitud
 * modal de ver cotizacion aprobada
 * boton de cotizacion general
 * actividad
 */
