<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accommodation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name'
    ];

    public function hotels()
    {
        return $this->belongsToMany(Hotels::class);
    }

}
