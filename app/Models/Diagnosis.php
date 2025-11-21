<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable; 
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\Auth;

class Diagnosis extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'pet_id',
        'vet_id',
        'checkup_date',
        'weight',
        'temperature',
        'attending_vet',
        'attending_staff',
        'chief_complaints', // This was 'diagnosis'
        'diagnosis',
        'assessment',
        'plan',
    ];

    /**
     * Get the pet that the diagnosis belongs to.
     */
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    /**
     * Get all of the images for the diagnosis.
     */
    public function images()
    {
        return $this->hasMany(DiagnosisImage::class);
    }

     /**
     * Get the user (vet) that attended this diagnosis.
     */
    public function vet()
    {
        return $this->belongsTo(User::class, 'vet_id');
    }
    public function appointments() // Note the plural name
{
    return $this->hasMany(Appointment::class)->orderBy('appointment_date');
}
protected $casts = [
        'checkup_date' => 'datetime', // This is the line to add
    ];
    /**
     * Customize the audit data before it is saved.
     */
  public function transformAudit(array $data): array
{
    $user = Auth::user();

    // 1. Check if a user is logged in AND their role is 'assistant'
    // We use strtolower() to ensure it matches 'Assistant' or 'assistant'
    if ($user && strtolower($user->role) === 'Assistant') {
        
        // 2. Only for assistants, grab the Attending Staff name
        $staffName = $this->attending_staff;

        // (Extra check: If updating, grab the NEW value being saved)
        if (isset($data['new_values']['attending_staff'])) {
            $staffName = $data['new_values']['attending_staff'];
        }

        // 3. Save it to tags
        if (!empty($staffName)) {
            $data['tags'] = $staffName;
        }
    }

    // If the user is a Vet/Doctor, the code above is skipped, 
    // 'tags' remains empty, and the View will default to showing the User's Name.

    return $data;
}
    
}
