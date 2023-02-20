<?php

namespace App\Http\Controllers;

use App\Models\ApuPart;
use App\Models\ApuService;
use App\Models\ApuSet;
use App\Models\Budget;
use App\Models\Business;
use App\Models\BusinessApu;
use App\Models\BusinessBudget;
use App\Models\BusinessHistory;
use App\Models\BusinessNote;
use App\Models\BusinessQuotation;
use App\Models\BusinessTask;
use App\Models\Municipality;
use App\Models\Person;
use App\Models\Quotation;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use \Milon\Barcode\DNS1D;
use \Milon\Barcode\DNS2D;
use Milion\Barcode\BarcodeGenerator;

class BusinessController extends Controller
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
            Business::with('thirdParty', 'thirdPartyPerson', 'country', 'city', 'businessBudget')->get()
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

    public function paginate(Request $request)
    {
        return $this->success(
            Business::with('thirdParty', 'thirdPartyPerson', 'country', 'city', 'businessBudget')
                ->orderByDesc('created_at')
                ->when($request->name, function ($q, $fill) {
                    $q->where('name', 'like', "%$fill%");
                })
                ->when($request->code, function ($q, $fill) {
                    $q->where('code', 'like', "%$fill%");
                })
                ->when($request->company_name, function ($q, $fill) {
                    return $q->whereHas('thirdParty', function ($q) use ($fill) {
                        $q->where('social_reason', 'like', "%$fill%")
                            ->orWhereRaw("CONCAT_WS(' ', first_name, first_surname) = '%$fill%'");
                    });
                })
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function addEventToHistroy($data)
    {
        BusinessHistory::create($data);
    }


    public function store(Request $request)
    {
        try {
            $quotations = $request->quotations;
            $apus = $request->apu;
            $consecutive = getConsecutive('businesses');
            $business = Business::create($request->except('budgets'));
            if ($consecutive->city) {
                $abbreviation = Municipality::where('id', $request->city_id)->first()->abbreviation;
                $business['code'] = generateConsecutive('businesses', $abbreviation);
            } else {
                $business['code'] = generateConsecutive('businesses');
            }
            $business->save();
            $person = Person::where('id', $request->person_id)->fullName()->first();
            $this->addEventToHistroy([
                'business_id' => $business->id,
                'icon' => 'fas fa-business-time',
                'title' => 'Se ha creado el negocio',
                'person_id' => $request->person_id,
                'description' => $person->full_names . ' ha creado el negocio.'
            ]);
            if ($quotations) {
                foreach ($quotations as $key => $value) {
                    BusinessQuotation::create([
                        'quotation_id' => $value['id'],
                        'business_id' =>  $business->id
                    ]);
                    $quotation = Quotation::where('id', $value['id'])->first();
                    $this->addEventToHistroy([
                        'business_id' => $business->id,
                        'icon' => 'fas fa-money-check-alt',
                        'title' => 'Se ha agregado una cotización',
                        'person_id' => $request->person_id,
                        'description' => $person->full_names . ' ha añadido la cotización ' . $quotation->line . ' - ' . $quotation->project . '.'
                    ]);
                }
            }
            if ($apus) {
                foreach ($apus as $apu) {
                    switch ($apu['type']) {
                        case 'P':
                            $apu_item = ApuPart::where('id', $apu['apu_id'])->first();
                            $icon = 'fas fa-wrench';
                            $type = 'App\Models\ApuPart';
                            break;
                        case 'C':
                            $apu_item = ApuSet::where('id', $apu['apu_id'])->first();
                            $icon = 'fas fa-cogs';
                            $type = 'App\Models\ApuSet';
                            break;
                        case 'S':
                            $apu_item = ApuService::where('id', $apu['apu_id'])->first();
                            $icon = 'fas fa-headset';
                            $type = 'App\Models\ApuService';
                            break;
                        default:
                            break;
                    }
                    BusinessApu::create([
                        'apuable_id' => $apu['apu_id'],
                        'apuable_type' => $type,
                        'business_id' =>  $business->id
                    ]);
                    $this->addEventToHistroy([
                        'business_id' => $business->id,
                        'icon' => $icon,
                        'title' => 'Se ha agregado un APU',
                        'person_id' => $request->person_id,
                        'description' => $person->full_names . ' ha añadido el apu ' . $apu_item->name
                    ]);
                }
            }
            if ($request->get('budgets')) {
                foreach ($request->get('budgets') as $budget) {
                    BusinessBudget::create([
                        'budget_id' => $budget['id'],
                        'business_id' =>  $business->id
                    ]);
                    $budget_ = Budget::where('id', $budget['id'])->first();
                    $this->addEventToHistroy([
                        'business_id' => $business->id,
                        'icon' => 'fas fa-money-bill',
                        'title' => 'Se ha agregado un presupuesto',
                        'person_id' => $request->person_id,
                        'description' => $person->full_names . ' ha añadido el presupuesto ' . $budget_->line . ' - ' . $budget_->project . '.'
                    ]);
                }
            }
            sumConsecutive('businesses');
            return $this->success('Creado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function newBusinessBudget(Request $request)
    {
        try {
            $person = Person::where('id', $request->person_id)->fullName()->first();
            Business::where('id', $request->business_id)->update(['budget_value' => $request->budget_value]);
            foreach ($request->get('budgets') as $budget) {
                BusinessBudget::create([
                    'budget_id' => $budget['budget_id'],
                    'business_id' =>  $budget['business_budget_id']
                ]);
                $budget_ = Budget::where('id', $budget['budget_id'])->first();
                $this->addEventToHistroy([
                    'business_id' => $request->business_id,
                    'icon' => 'fas fa-money-bill',
                    'title' => 'Se ha agregado un presupuesto',
                    'person_id' => $request->person_id,
                    'description' => $person->full_names . ' ha añadido el presupuesto ' . $budget_->line . ' - ' . $budget_->project . '.'
                ]);
            }
            return $this->success('Creado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function newBusinessQuotation(Request $request)
    {
        try {
            $person = Person::where('id', $request->person_id)->fullName()->first();
            $business = Business::where('id', $request->business_id)->first();
            $business->update(['quotation_value' => $request->quotation_value]);
            foreach ($request->quotations as $quotation) {
                BusinessQuotation::create([
                    'quotation_id' => $quotation['quotation_id'],
                    'business_id' =>  $quotation['business_id']
                ]);
                $quotation = Quotation::where('id', $quotation['quotation_id'])->first();
                $this->addEventToHistroy([
                    'business_id' => $business->id,
                    'icon' => 'fas fa-money-check-alt',
                    'title' => 'Se ha agregado una cotización',
                    'person_id' => $request->person_id,
                    'description' => $person->full_names . ' ha añadido la cotización ' . $quotation->line . ' - ' . $quotation->project . '.'
                ]);
            }
            return $this->success('Creado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function newBusinessApu(Request $request)
    {
        $person = Person::where('id', $request->person_id)->fullName()->first();
        $business = Business::where('id', $request->business_id)->first();
        //$business->update(['quotation_value' => $request->quotation_value]);
        foreach ($request->apus as $apu) {
            switch ($apu['type']) {
                case 'P':
                    $apu_item = ApuPart::where('id', $apu['apu_id'])->first();
                    $icon = 'fas fa-wrench';
                    $type = 'App\Models\ApuPart';
                    break;
                case 'C':
                    $apu_item = ApuSet::where('id', $apu['apu_id'])->first();
                    $icon = 'fas fa-cogs';
                    $type = 'App\Models\ApuSet';
                    break;
                case 'S':
                    $apu_item = ApuService::where('id', $apu['apu_id'])->first();
                    $icon = 'fas fa-headset';
                    $type = 'App\Models\ApuService';
                    break;
                default:
                    break;
            }
            BusinessApu::create([
                'apuable_id' => $apu['apu_id'],
                'apuable_type' => $type,
                'business_id' =>  $business->id
            ]);
            $this->addEventToHistroy([
                'business_id' => $business->id,
                'icon' => $icon,
                'title' => 'Se ha agregado un APU',
                'person_id' => $request->person_id,
                'description' => $person->full_names . ' ha añadido el apu ' . $apu_item->name
            ]);
        }
    }

    public function newBusinessNote(Request $request)
    {
        $person = Person::where('id', $request->person_id)->fullName()->first();
        BusinessNote::create($request->all());
        $this->addEventToHistroy([
            'business_id' => $request->business_id,
            'icon' => 'fas fa-sticky-note',
            'title' => 'Se ha agregado una nota',
            'person_id' => $request->person_id,
            'description' => $person->full_names . ' ha publicado una nueva nota.'
        ]);
        return $this->success('Creada con éxito');
    }

    public function getNotes($id)
    {
        return $this->success(Business::with('notes')->get()->pluck('notes'));
    }

    public function changeStatusInBusiness(Request $request)
    {
        if ($request->label == 'budget') {
            $aux = BusinessBudget::where('id', $request->item['id'])->first();
            $aux->update(['status' => $request->status]);
        } else if ($request->label == 'quotation') {
            $aux = BusinessQuotation::where('id', $request->item['id'])->first();
            $aux->update(['status' => $request->status]);
        }
        return $this->success('holi');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //return BusinessApu::with('apuable')->get();
        $business = Business::where('id', $id)
            ->with(
                'thirdParty',
                'thirdPartyPerson',
                'country',
                'city',
                'apus',
                'businessBudget',
                'quotations',
                'notes'
            )
            ->first();
        $codeQR = new DNS2D();
        $business2 = Business::where('id', $id)->first();
        $codeQRc = $codeQR->getBarcodePNG(json_encode($business2), 'QRCODE', 10, 10);
        return $this->success(
            $business,
            $codeQRc
        );
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
        try {
            Business::find($id)->update($request->all());
            return $this->success('Estado cambiado');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
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

    public function getTasks($id)
    {
        return $this->success(Business::with('tasks')->where('id', $id)->first());
    }

    public function getHistory($id)
    {
        $timeline = [];
        $task_history = Business::find($id)->timeline_tasks()->get();
        foreach ($task_history as $history) {
            foreach ($history->timeline as $element) {
                $timeline[] = $element;
            }
        }
        $history = Business::find($id)->history()->get();
        return $this->success([
            'history' => $history,
            'timeline' => $timeline
        ]);
    }
}
