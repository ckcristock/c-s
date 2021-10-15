<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrivingLicenseJob extends Model
{
    use HasFactory;
    protected $fillable = [
        'driving_license_id',
        'job_id'
    ];
}
