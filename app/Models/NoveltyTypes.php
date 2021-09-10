<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoveltyTypes extends Model
{
    use HasFactory;
    protected $fillable = [
        'novelty_type',
        'novelty',
        'modality',
        'status'
    ];
    protected $table = 'novelty_types';
}
