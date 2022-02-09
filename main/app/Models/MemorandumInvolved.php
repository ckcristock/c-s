<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemorandumInvolved extends Model
{
    use HasFactory;
    protected $fillable = [
      'memorandum_id',
      'person_involved_id'
    ];

    public function memorandum()
    {
        return $this->belongsTo(Memorandum::class)->with('memorandumtype');
    }
}
