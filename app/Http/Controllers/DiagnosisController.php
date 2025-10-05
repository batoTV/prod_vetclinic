<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Diagnosis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\DiagnosisImage;

class DiagnosisController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Pet $pet)
    {
        $vets = User::where('role', 'vet')->get();
        
        return view('diagnoses.create', [
            'pet' => $pet,
            'vets' => $vets
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Diagnosis $diagnosis)
    {
        $diagnosis->load('appointments'); 
        return view('diagnoses.show', ['diagnosis' => $diagnosis]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Diagnosis $diagnosis)
    {
        $vets = User::where('role', 'vet')->get();
        $diagnosis->load('appointments'); 
        return view('diagnoses.edit', [
            'diagnosis' => $diagnosis,
            'vets' => $vets
        ]);
    }

   public function store(Request $request, Pet $pet)
{
    // 1. Validation rules are updated.
    $validatedData = $request->validate([
        'checkup_date' => 'required|date',
        'weight' => 'nullable|numeric',
        'temperature' => 'nullable|numeric',
        // 'vet_id' is now nullable to allow creating a record from client registration
        'vet_id' => 'nullable|exists:users,id', 
        'attending_staff' => 'nullable|string|max:255',
        // 'chief_complaint' is now nullable (was required) and singular
        'chief_complaints' => 'nullable|string',
        'diagnosis' => 'nullable|string',
        'assessment' => 'nullable|string',
        'plan' => 'nullable|string',
        'xray_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        'appointments' => 'nullable|array', // Validate the main array
        'appointments.*.appointment_date' => 'nullable|date', // Changed from required_with
        'appointments.*.title' => 'nullable|string|max:255',
    ]);

    // 2. Create the diagnosis using the relationship.
    // This automatically adds the correct pet_id.
    $diagnosis = $pet->diagnoses()->create($validatedData);
    
      $appointmentsData = isset($validatedData['appointments']) ? 
        array_filter($validatedData['appointments'], function($item) {
            return !empty($item['appointment_date']) && !empty($item['title']);
        }) : [];

    // Now, loop through the CLEAN, FILTERED data
    if (!empty($appointmentsData)) {
        foreach ($appointmentsData as $appointmentData) {
            $appointmentData['pet_id'] = $pet->id; 
            $diagnosis->appointments()->create($appointmentData);
        }
    }

    // Image handling logic remains the same
    if ($request->hasFile('xray_images')) {
        foreach ($request->file('xray_images') as $file) {
            $path = $file->store('xrays', 'public');
            $diagnosis->images()->create(['image_path' => $path]);
        }
    }

    // 3. (Best Practice) Redirect using the named route.
    return redirect()->route('pets.show', $pet->id)
                     ->with('success', 'Medical record has been added successfully.');
}

    public function update(Request $request, Diagnosis $diagnosis)
{
    // VALIDATION: Added rule for the array and corrected others for consistency
    $validatedData = $request->validate([
        'checkup_date' => 'required|date',
        'weight' => 'nullable|numeric',
        'temperature' => 'nullable|numeric',
        // This rule assumes your vets are users. If 'vets' is a separate table, change to 'exists:vets,id'
        'vet_id' => 'nullable|exists:users,id',
        'attending_staff' => 'nullable|string|max:255',
        'chief_complaints' => 'nullable|string',
        'assessment' => 'nullable|string',
        // Made this 'required' to match the form's HTML
        'diagnosis' => 'nullable|string',
        'plan' => 'nullable|string',
        'appointments' => 'nullable|array',
        'appointments.*.appointment_date' => 'nullable|date', // Changed from required_with
        'appointments.*.title' => 'nullable|string|max:255',
        // ** THIS IS THE KEY FIX **
        // First, validate that xray_images is an array.
        'xray_images' => 'nullable|array',
        // Then, validate each item within that array.
        'xray_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
    ]);
    
    // Update the main diagnosis fields
    $diagnosis->update($validatedData);

    $appointmentsData = isset($validatedData['appointments']) ? 
        array_filter($validatedData['appointments'], function($item) {
            return !empty($item['appointment_date']) && !empty($item['title']);
        }) : [];

    // First, delete all old appointments linked to this diagnosis
    $diagnosis->appointments()->delete();

    // Then, create the new, filtered appointments
    if (!empty($appointmentsData)) {
        foreach ($appointmentsData as $appointmentData) {
            $appointmentData['pet_id'] = $diagnosis->pet_id;
            $diagnosis->appointments()->create($appointmentData);
        }
    }
    // Handle the uploaded images
    if ($request->hasFile('xray_images')) {
        foreach ($request->file('xray_images') as $file) {
            // Store the file in 'storage/app/public/diagnosis_images'
            $path = $file->store('diagnosis_images', 'public');
            // Create a related image record
            $diagnosis->images()->create(['image_path' => $path]);
        }
    }

    // Redirect back to the pet's page with a success message
    return redirect()->route('diagnoses.show', $diagnosis)
                     ->with('success', 'Medical record has been updated successfully.');
}

    public function destroy(Diagnosis $diagnosis)
    {
        foreach ($diagnosis->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $petId = $diagnosis->pet_id;
        $diagnosis->delete();
        return redirect()->route('pets.show', ['pet' => $petId])
                 ->with('success', 'Medical record deleted.');
    }
    
    public function destroyImage(DiagnosisImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return redirect()->back()->with('success', 'Image deleted successfully.');
    }
}
