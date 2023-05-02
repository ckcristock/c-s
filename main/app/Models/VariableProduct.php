<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariableProduct extends Model
{
    protected $fillable =
    [
        'product_id',
        'subcategory_variables_id',
        'category_variables_id',
        'valor'
    ];

    public function scopeAlias($q, $alias){
        return $q->from($q->getQuery()->from." as ".$alias);
    }

    public function categoryVariables()
    {
        return $this->belongsTo(CategoryVariable::class);
    }

    public function subCategoryVariables()
    {
        return $this->belongsTo(SubcategoryVariable::class, 'subcategory_variables_id');
    }
}
