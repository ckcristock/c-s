<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryVariable extends Model
{
    use SoftDeletes;

    protected $table = 'category_variables';

    protected $fillable = ['category_id', 'label', 'required', 'type'];

    /* public function scopeAlias($q, $alias){
        return $q->from($q->getQuery()->from." as ".$alias);
    } */
}
