<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTypes extends Model
{
    protected $fillable = [
        'code',
        'dian_code',
        'name',
        'abbreviation',
        'status'
    ];
    protected $table = 'document_types';
}
