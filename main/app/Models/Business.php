<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'third_party_id',
        'third_party_person_id',
        'country_id',
        'description',
        'status',
        'city_id',
        'date',
        'budget_value',
        'quotation_value',
        'format_code'
    ];

    public function thirdParty()
    {
        return $this->belongsTo(ThirdParty::class)->name();
    }

    public function thirdPartyPerson()
    {
        return $this->belongsTo(ThirdPartyPerson::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function businessBudget()
    {
        return $this->hasMany(BusinessBudget::class)->with('budget');
    }

    public function quotations()
    {
        return $this->belongsToMany(Quotation::class)->name()->with('municipality', 'client', 'budgets')->withPivot('status');
    }

    public function apus()
    {
        return $this->hasMany(BusinessApu::class)->with('apuable');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class)->with('asignador', 'types', 'realizador');
    }

    public function timeline_tasks()
    {
        return $this->belongsToMany(Task::class)->with('timeline');
    }

    public function history()
    {
        return $this->hasMany(BusinessHistory::class)->with('person');
    }

    public function notes()
    {
        return $this->hasMany(BusinessNote::class)->with('person');
    }
}
