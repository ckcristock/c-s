<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSocialSecurityCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "prefix",
        "concept",
        "percentage",
        "created_at",
        "updated_at"
    ];

}
