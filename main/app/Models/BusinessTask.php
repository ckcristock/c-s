<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessTask extends Model
{
    use HasFactory;

    protected $table = 'business_task';

    protected $fillable = [
        "business_id",
        "task_id",
    ];
}
