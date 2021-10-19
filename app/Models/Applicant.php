<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function visaType(){
        return $this->belongsTo(VisaType::class,'visaType_id', 'id');
    }
    public function drivingLicense(){
        return $this->belongsTo(drivingLicense::class);
    }
}
