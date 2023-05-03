<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubcategoryVariable extends Model
{
    use SoftDeletes;

    protected $table = 'subcategory_variables';

    protected $fillable = [
        'subcategory_id',
        'label',
        'type',
        'required',
        'reception'
    ];

    /* public function scopeAlias($q, $alias){
        return $q->from($q->getQuery()->from." as ".$alias)->whereNull();
    } */

}
