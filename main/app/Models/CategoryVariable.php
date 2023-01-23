<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryVariable extends Model
{
    protected $table = 'category_variables';

    protected $fillable = ['category_id', 'label', 'required', 'type'];

    public function scopeAlias($q, $alias){
        return $q->from($q->getQuery()->from." as ".$alias);
    }
}
