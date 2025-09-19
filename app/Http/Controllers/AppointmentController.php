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
                    'url'   => '/pets/' . $appointment->pet->id, // <-- THIS IS THE NEW LINE
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
        $selectedPetId = $request->input('pet_id');

        return view('appointments.create', [
            'pets' => $pets,
            'selectedPetId' => $selectedPetId
        ]);
    }
    //  /**
    //  * Update the status of an appointment.
    //  */
    // public function updateStatus(Request $request, Appointment $appointment)
    // {
    //     $request->validate([
    //         'status' => 'required|in:Scheduled,Completed,Cancelled,No-Show',
    //     ]);

    //     $appointment->update(['status' => $request->status]);

    //     return redirect()->back()->with('success', 'Appointment status updated.');
    // }
}
