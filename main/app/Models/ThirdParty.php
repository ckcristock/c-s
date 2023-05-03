<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ThirdParty extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    /* protected $fillable = [
        'document_type',
        'nit',
        'dv',
        'person_type',
        'social_reason',
        'first_name',
        'second_name',
        'first_surname',
        'second_surname',
        'dian_address',
        'address_one',
        'address_two',
        'address_three',
        'address_four',
        'cod_dian_address',
        'tradename',
        'department_id',
        'municipality_id',
        'zone_id',
        'landline',
        'cell_phone',
        'email',
        'winning_list_id',
        'apply_iva',
        'contact_payments',
        'phone_payments',
        'email_payments',
        'regime',
        'encourage_profit',
        'ciiu_code_id',
        'withholding_agent',
        'withholding_oninvoice',
        'reteica_type',
        'reteica_account_id',
        'reteica_percentage',
        'retefuente_account_id',
        'retefuente_percentage',
        'g_contribut',
        'reteiva_account_id',
        'reteiva_percentage',
        'condition_payment',
        'assigned_space',
        'discount_prompt_payment',
        'discount_days',
        'state',
        'rut',
        'image',
        'fiscal_responsibility',
        'country_id',
        'location',
        'city_id'
    ]; */

    protected $hidden = [
        "updated_at", "created_at",
    ];


    public function thirdPartyPerson()
    {
        return $this->hasMany(ThirdPartyPerson::class)->with('thirdParty');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function accountPlan()
    {
        return $this->belongsTo(AccountPlan::class);
    }

    public function scopeName($q, $alias = 'full_name')
    {
        // Si se envió 'select *' retire 'social_reason' de la lista de campos
        if (is_null($q->getQuery()->columns)) {
            $q2 = DB::query()->fromSub($q, "s")->get();
            $columnas = array_keys((array) $q2->first());
            $columnas = array_diff($columnas, ["social_reason"]);
            $q->select($columnas);
        }
        return $q->addSelect(DB::raw('IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname, second_name, second_surname)) as ' . $alias));
    }

    public function scopeName2($q)
    {
        return $q->select(
            '*',
            DB::raw('IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname)) as text'),
            'id as value',
        );
    }

    public function scopeFullName($q)
    {
        return $q->select('*', DB::raw('IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname)) as full_name'));
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'customer_id')->with('municipality', 'client', 'items');
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class, 'customer_id')->with(
            [
                'destiny' => function ($q) {
                    $q->select('*');
                },
                'user' => function ($q) {
                    $q->select('id', 'usuario', 'person_id')
                        ->with(
                            ['person' => function ($q) {
                                $q->select('id', 'first_name', 'first_surname');
                            }]
                        );;
                },
                'customer' => function ($q) {
                    $q->select('id', 'nit')
                        ->selectRaw('IFNULL(social_reason, CONCAT_WS(" ",first_name, first_name) ) as name');
                }
            ]
        );
    }

    public function business()
    {
        return $this->hasMany(Business::class)->with('thirdParty', 'thirdPartyPerson', 'country', 'city', 'businessBudget');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function document_type_()
    {
        return $this->belongsTo(DocumentTypes::class, 'document_type');
    }

    public function reteica()
    {
        return $this->belongsTo(PlanCuentas::class, 'reteica_account_id', 'Id_Plan_Cuentas');
    }

    public function reteiva()
    {
        return $this->belongsTo(PlanCuentas::class, 'retefuente_account_id', 'Id_Plan_Cuentas');
    }

    public function retefuente()
    {
        return $this->belongsTo(PlanCuentas::class, 'reteiva_account_id', 'Id_Plan_Cuentas');
    }
}
