<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'description',
        'name',
        'public_name'
    ];
    public function usuario()
    {
        return $this->BelongsToMany(Usuario::class);
    }
}
