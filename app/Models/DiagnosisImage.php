<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosisImage extends Model
{
    use HasFactory;

    protected $fillable = ['diagnosis_id', 'image_path'];

    public function diagnosis()
    {
        return $this->belongsTo(Diagnosis::class);
    }
}
