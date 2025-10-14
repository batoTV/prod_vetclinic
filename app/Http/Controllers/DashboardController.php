<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Owner;
use App\Models\Pet;
use App\Models\Diagnosis; // <-- Import Diagnosis
use App\Models\Consent; 
use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function index()
{
    $user = auth()->user();
    if (!$user->isVet() && !$user->isReceptionist()) {
        return redirect()->route('pets.index')->with('error', 'You do not have access to the dashboard.');
    }

    $totalPets = Pet::count();
    $totalOwners = Owner::count();
    
    // OPTIMIZATION: Use count() instead of paginate() for the stats card
    $todaysAppointmentsCount = Appointment::whereDate('appointment_date', today())->count();

   $query = Appointment::with('pet.owner');

// Join pets and owners tables so we can sort by owner's name
$query->join('pets', 'appointments.pet_id', '=', 'pets.id')
      ->join('owners', 'pets.owner_id', '=', 'owners.id');

// Apply your date filter
$query->whereDate('appointments.appointment_date', today())
// IMPORTANT: Select only appointment columns to avoid conflicts
      ->select('appointments.*') 
// Set the new sort order
      ->orderBy('owners.last_name', 'asc'); 

// (Optional) Add a secondary sort by time
// ->orderBy('appointments.appointment_date', 'asc');

// Paginate the final results
$todaysAppointmentsList = $query->paginate(15);

    // --- FETCH TODAY'S ACTIVITIES ---
    $newlyAddedPets = Pet::with('owner')->whereDate('created_at', today())->get();
    $newDiagnoses = Diagnosis::with('pet.owner')->whereDate('created_at', today())->get();
    $newConsents = Consent::with('pet.owner')->whereDate('created_at', today())->get();

    // --- MAP ACTIVITIES TO A STANDARD FORMAT ---
    $petActivities = $newlyAddedPets->map(function ($item) {
        return (object) ['timestamp' => $item->created_at, 'type' => 'new_pet', 'model' => $item];
    });
    $diagnosisActivities = $newDiagnoses->map(function ($item) {
        return (object) ['timestamp' => $item->created_at, 'type' => 'new_diagnosis', 'model' => $item];
    });
    $consentActivities = $newConsents->map(function ($item) {
        return (object) ['timestamp' => $item->created_at, 'type' => 'new_consent', 'model' => $item];
    });

    // --- 1. CORRECTLY COMBINE AND SORT ALL ACTIVITIES ---
    $todaysActivities = collect([])
        ->merge($petActivities)
        ->merge($diagnosisActivities)
        ->merge($consentActivities)
        ->filter() // Remove any nulls
        ->sortByDesc('timestamp'); // Sort by newest first

    // --- 2. CORRECTLY GET UNIQUE OWNERS FROM THE FULL ACTIVITY LIST ---
    $uniqueActiveOwners = $todaysActivities->map(function ($activity) {
        if (isset($activity->model->owner)) {
            return $activity->model->owner;
        }
        if (isset($activity->model->pet->owner)) {
            return $activity->model->pet->owner;
        }
        return null;
    })
    ->filter()           // Remove any nulls from the owner list
    ->unique('id')       // Get only unique owners based on their ID
    ->sortBy('last_name');     // Sort them alphabetically

    return view('dashboard', [
        'totalPets' => $totalPets,
        'totalOwners' => $totalOwners,
        'todaysAppointments' => $todaysAppointmentsCount, // Send the COUNT for the card
        'todaysAppointmentsList' => $todaysAppointmentsList, // Send the LIST for the table
        'todaysActivities' => $todaysActivities,
        'uniqueActiveOwners' => $uniqueActiveOwners,
    ]);
}
}