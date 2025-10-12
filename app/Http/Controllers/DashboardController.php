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

        $totalPets = Pet::count();
        $totalOwners = Owner::count();
        
        // Fetch today's appointments with pet and owner info, paginated
        $todaysAppointments = Appointment::with('pet.owner')
                                        ->whereDate('appointment_date', today())
                                        ->orderBy('appointment_date', 'asc')
                                        ->paginate(15);

       
        
        // Check if user has access to dashboard (only vets and receptionists)
        if (!$user->isVet() && !$user->isReceptionist()) {
            // Redirect assistants to pets page
            return redirect()->route('pets.index')->with('error', 'You do not have access to the dashboard.');
        }

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

        // --- COMBINE AND SORT ALL ACTIVITIES ---
        $todaysActivities = collect([])
            ->merge($petActivities)
            ->merge($diagnosisActivities)
            ->merge($consentActivities)
            ->sortByDesc('timestamp'); // Sort by newest first

       
        
        // Dashboard logic here
         return view('dashboard', [
            'totalPets' => $totalPets,
            'totalOwners' => $totalOwners,
            'todaysAppointments' => $todaysAppointments,
             'todaysActivities' => $todaysActivities // <-- Pass the combined list to the view
        ]);
    }
}