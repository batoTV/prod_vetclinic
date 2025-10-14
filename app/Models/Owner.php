<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name', // Updated
        'last_name',  // Updated
        'phone_number',
        'email',
        'address',
    ];

    /**
     * Get the pets for the owner.
     */
    public function pets()
    {
        return $this->hasMany(Pet::class);
    }
    /**
     * Get the owner's full name.
     * This creates a virtual attribute: $owner->full_name
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
}