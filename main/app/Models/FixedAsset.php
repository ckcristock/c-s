<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'user_id',
        'fixed_asset_type_id',
        'niif_cost',
        'pcga_cost',
        'nit',
        'iva',
        'base',
        'niif_iva',
        'niif_base',
        'center_cost_id',
        'name',
        'amount',
        'document',
        'reference',
        'code',
        'source',
        'fixed_asset_code',
        'concept',
        'date',
        'depreciation_type',
        'source_rete_cost',
        'ica_rete_cost',
        'niif_source_rete_cost',
        'niif_ica_rete_cost',
        'rete_ica_account_id',
        'source_rete_account_id',
        'state'
    ];
}
