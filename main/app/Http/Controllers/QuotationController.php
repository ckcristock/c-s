<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Municipality;
use App\Models\Quotation;
use App\Models\QuotationActivity;
use App\Models\QuotationItem;
use App\Models\QuotationItemSubitem;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
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
        return $this->success(Quotation::with('municipality', 'client', 'items')->name()->get());
    }

    public function paginate(Request $request)
    {
        return $this->success(
            Quotation::with('municipality', 'client', 'items')
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
                ->when($request->date_start, function ($q, $fill) use ($request) {
                    $q->whereBetween('created_at', [$fill, $request->date_end]);
                })
                ->when($request->status, function ($q, $fill) {
                    $q->where('status', $fill);
                })
                ->when($request->third_party_id, function ($q, $fill) {
                    $q->where('customer_id', $fill);
                })
                ->name()
                ->orderByDesc('created_at')
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
        $data = $request->all();
        $items = $request->items;
        $consecutive = getConsecutive('quotations');
        if ($consecutive->city) {
            $abbreviation = Municipality::where('id', $data['destinity_id'])->first()->abbreviation;
            $data['code'] = generateConsecutive('quotations', $abbreviation);
        } else {
            $data['code'] = generateConsecutive('quotations');
        }
        $quotation = Quotation::updateOrCreate(['id' => $request->id], $data);
        if ($quotation->wasRecentlyCreated) {
            sumConsecutive('quotations');
            $item = $quotation->items()->createMany($items);
            foreach ($item as $index => $subitem) {
                $subitem->subItems()->createMany($request->items[$index]['subItems']);
            }
            $this->addActivities($quotation->id, 'fas fa-plus', 'Cotización creada', '', 'Creación');
            return $this->success('Creado con éxito');
        } else {
            $deleted = QuotationItem::where('quotation_id', $quotation->id)->pluck('id');
            QuotationItemSubitem::whereIn('quotation_item_id', $deleted)->delete();
            QuotationItem::where('quotation_id', $quotation->id)->delete();
            $item = $quotation->items()->createMany($items);
            foreach ($item as $index => $subitem) {
                $subitem->subItems()->createMany($request->items[$index]['subItems']);
            }
            $this->addActivities($quotation->id, 'fas fa-edit', 'Cotización editada', '', 'Edición');
            return $this->success('Actualizado con éxito');
        }
    }

    function addActivities($id, $icon, $title, $description, $status)
    {
        QuotationActivity::create([
            'quotation_id' => $id,
            'icon' => $icon,
            'title' => $title,
            'person_id' => auth()->user()->id,
            'description' => $description,
            'status' => $status
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->success(Quotation::where('id', $id)->with('municipality', 'client', 'items', 'third_person', 'activities')->first());
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
        switch ($request->status) {
            case 'Anulada':
                $this->addActivities($id, 'fas fa-ban', 'Cotización anulada', '', 'Anulación');
                break;
            case 'Aprobada':
                $this->addActivities($id, 'fas fa-check', 'Cotización aprobada', '', 'Aprobación');
                break;
            case 'Pendiente':
                if ($quotation->status == 'Anulada') {
                    $this->addActivities($id, 'fas fa-check', 'Cotización activada', '', 'Edición');
                } else {
                    $this->addActivities($id, 'fas fa-check', 'Cotización devuelta', '', 'Devuelta');
                }
                break;
            default:
                break;
        }
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

    public function pdf($id)
    {
        $company = Company::first();
        $image = $company->page_heading;
        $data = Quotation::where('id', $id)->with('municipality', 'client', 'items', 'third_person', 'activities')->first();
        $creator = data_get($data, 'activities', [])
            ->first(function ($activity) {
                return data_get($activity, 'status') === 'Creación';
            })
            ->person;

        $approve = data_get($data, 'activities', [])
            ->first(function ($activity) {
                return data_get($activity, 'status') === 'Aprobación';
            })
            ->person;
        $datosCabecera = (object) array(
            'Titulo' => 'Cotización',
            'Codigo' => $data->code,
            'Fecha' => $data->created_at,
            'CodigoFormato' => $data->format_code
        );
        $pdf = PDF::loadView('pdf.quotation', [
            'data' => $data,
            'company' => $company,
            'datosCabecera' => $datosCabecera,
            'image' => $image,
            'creator' => $creator,
            'approve' => $approve,
        ]);
        return $pdf->download('cotizacion.pdf');
    }
}
