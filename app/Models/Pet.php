<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
