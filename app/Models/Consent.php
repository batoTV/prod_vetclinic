<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consent extends Model
{
    use HasFactory;

    protected $fillable = ['pet_id', 'consent_type', 'notes', 'file_path'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}