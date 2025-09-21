<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Owner;
use Illuminate\Http\Request; 
use Carbon\Carbon;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // <-- THIS IS THE FIX
    {
        // Start with the base query
        $query = Pet::with('owner');

        // If there is a search term, filter the results
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('owner', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  });
        }

        // Get the final results
        $pets = $query->paginate(15); 
        
        return view('pets.index', ['pets' => $pets]);
    }
 /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $owners = Owner::all();
        
        // Get the specific owner ID from the URL, if it exists
        $selectedOwnerId = $request->input('owner_id');

        return view('pets.create', [
            'owners' => $owners,
            'selectedOwnerId' => $selectedOwnerId
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'owner_id' => 'required|exists:owners,id',
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'required|in:Male,Female',
            'allergies' => 'nullable|string',
            'markings' => 'nullable|string|max:255', // <-- Add this rule
        ]);

        Pet::create($validatedData);

        return redirect()->route('pets.index')->with('success', 'Pet added successfully.');
    }

     /**
     * Display the specified resource.
     */
    public function show(Pet $pet)
    {
        // Find the most recent diagnosis for this pet
        $latestDiagnosis = $pet->diagnoses()->latest('checkup_date')->first();

        // Fetch and sort appointments based on the date only
        $upcomingAppointments = $pet->appointments()
                                    ->whereDate('appointment_date', '>=', \Carbon\Carbon::today())
                                    ->orderBy('appointment_date', 'asc')
                                    ->get();

        $pastAppointments = $pet->appointments()
                                ->whereDate('appointment_date', '<', \Carbon\Carbon::today())
                                ->orderBy('appointment_date', 'desc')
                                ->get();

      

        $diagnoses = $pet->diagnoses()->orderBy('checkup_date', 'desc')->paginate(10, ['*'], 'diag_page')
                 ->appends(['tab' => 'medical']);

        $consents = $pet->consents()->paginate(5, ['*'], 'consent_page')
               ->appends(['tab' => 'consent']);

        // Return the pet profile view, passing all the necessary data
        return view('pets.show', compact(
            'pet',
            'latestDiagnosis',
            'upcomingAppointments',
            'pastAppointments',
            'diagnoses',
            'consents' // <-- AND ADD THIS
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pet $pet)
    {
        $owners = Owner::all();
        return view('pets.edit', ['pet' => $pet, 'owners' => $owners]);
    }

    /**
     * Update the specified resource in storage.
     */
    
    public function update(Request $request, Pet $pet)
    {
        $validatedData = $request->validate([
            'owner_id' => 'required|exists:owners,id',
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'required|in:Male,Female',
            'allergies' => 'nullable|string',
            'markings' => 'nullable|string|max:255', // <-- Add this rule
        ]);

        $pet->update($validatedData);

        return redirect()->route('pets.show', $pet->id)->with('success', 'Pet details updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pet $pet)
    {
        $pet->delete();

        return redirect('/pets');
    }
}
