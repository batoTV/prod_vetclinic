<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Owner;
use App\Models\Pet;
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
        
        // Dashboard logic here
         return view('dashboard', [
            'totalPets' => $totalPets,
            'totalOwners' => $totalOwners,
            'todaysAppointments' => $todaysAppointments,
        ]);
    }
}