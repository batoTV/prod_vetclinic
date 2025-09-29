<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display the calendar view.
     */
    public function index()
    {
        return view('appointments.index');
    }

    /**
     * Fetch event data for the calendar.
     */
    public function getEvents()
    {
        // Eager load the 'pet' relationship to prevent extra queries
        $appointments = Appointment::with('pet')->get();

        $events = [];
        foreach ($appointments as $appointment) {
            // Safety check to ensure the pet exists
            if ($appointment->pet) {
                $events[] = [
                    'title' => $appointment->pet->name . ' - ' . $appointment->title,
                    'start' => $appointment->appointment_date,
                     'url'   => route('pets.show', ['pet' => $appointment->pet->id]),
                ];
            }
        }
        
        return response()->json($events);
    }

    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'appointment_date' => 'required|date',
        ]);

        // Create the appointment
        Appointment::create($validatedData);

        // Redirect back to the pet's profile page
        return redirect('/pets/' . $validatedData['pet_id'])->with('success', 'Appointment has been scheduled successfully.');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Fetch all pets for the dropdown
        $pets = Pet::with('owner')->get();
        
        // Get the specific pet ID from the URL, if it exists
        $selectedPetId = $request->input('pet');

        return view('appointments.create', [
            'pets' => $pets,
            'selectedPetId' => $selectedPetId
        ]);
    }
    public function edit(Appointment $appointment)
    {
        // We need the list of all pets for the dropdown, just like in create()
        $pets = Pet::with('owner')->get();

        return view('appointments.edit', [
            'appointment' => $appointment,
            'pets' => $pets
        ]);
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'appointment_date' => 'required|date',
        ]);

        // Update the appointment with the validated data
        $appointment->update($validatedData);

        // Redirect back to the pet's profile page, on the upcoming appointments tab
        return redirect()->route('pets.show', ['pet' => $appointment->pet_id, 'tab' => 'upcoming'])
                         ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $petId = $appointment->pet_id; // Store pet_id before deleting
        $appointment->delete();

        // Redirect back to the pet's profile page
        return redirect()->route('pets.show', ['pet' => $petId, 'tab' => 'upcoming'])
                         ->with('success', 'Appointment cancelled successfully.');
    }
}
