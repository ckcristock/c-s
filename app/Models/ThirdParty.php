<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdParty extends Model
{
    use HasFactory;
    protected $fillable = [
        'nit',
        'person_type',
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
        'reteica_account',
        'retefuente_account',
        'g_contribut',
        'reteiva_account',
        'condition_payment',
        'assigned_space',
        'discount_prompt_payment',
        'discount_days',
        'state',
        'rut',
        'cod_dian_address'
    ];

    public function thirdPartyPerson()
    {
        return $this->hasMany(ThirdPartyPerson::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }
}
