<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Pet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'name',
        'species',
        'breed',
        'birth_date',
        'gender',
        'allergies',
        'markings',
    ];

    /**
     * Get the owner that owns the pet.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * Get the diagnoses for the pet.
     */
    public function diagnoses()
    {
        return $this->hasMany(Diagnosis::class);
    }

    /**
     * Get the appointments for the pet.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function consents()
{
    return $this->hasMany(Consent::class)->latest();
}
protected function age(): Attribute
    {
        return Attribute::make(
            get: function () {
                $birthDate = Carbon::parse($this->birth_date);
                $ageInYears = $birthDate->age;

                if ($ageInYears >= 1) {
                    return $ageInYears . ' ' . \Illuminate\Support\Str::plural('year', $ageInYears) . ' old';
                }
                
                $ageInMonths = (int) $birthDate->diffInMonths(now());
                return $ageInMonths . ' ' . \Illuminate\Support\Str::plural('month', $ageInMonths) . ' old';
            },
        );
    }
}
