<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayoffsCertificate extends Model
{
    use HasFactory;
    protected $fillable = [
        'reason_withdrawal', 'person_id', 'reason', 'document', 'state'
    ];
}
