<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ThirdParty extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    /* protected $fillable = [
        'nit',
        'person_type',
        'third_party_type',
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
        'retefuente_account_id',
        'g_contribut',
        'reteiva_account_id',
        'condition_payment',
        'assigned_space',
        'discount_prompt_payment',
        'discount_days',
        'state',
        'rut',
        'cod_dian_address',
        'image'
    ]; */

    protected $hidden = [
        "updated_at","created_at",
    ];


    public function thirdPartyPerson()
    {
        return $this->hasMany(ThirdPartyPerson::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function accountPlan()
    {
        return $this->belongsTo(AccountPlan::class);
    }
    public function scopeName($q)
    {
        return $q->addSelect(DB::raw('*, IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname)) as full_name'));    }

}
