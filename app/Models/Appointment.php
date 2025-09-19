<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'title',
        'description',
        'appointment_date', // Changed from start_date and end_date
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
