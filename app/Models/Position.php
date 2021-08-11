<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;
    /**
     * Get the user that owns the Position
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dependency()
    {
        return $this->belongsTo(Dependency::class);
    }
}
