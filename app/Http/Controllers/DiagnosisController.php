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
        return view('diagnoses.show', ['diagnosis' => $diagnosis]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Diagnosis $diagnosis)
    {
        $vets = User::where('role', 'vet')->get();
        return view('diagnoses.edit', [
            'diagnosis' => $diagnosis,
            'vets' => $vets
        ]);
    }

    public function store(Request $request, Pet $pet)
    {
        $validatedData = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'checkup_date' => 'required|date',
            'weight' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'vet_id' => 'required|exists:users,id',
            'attending_staff' => 'nullable|string|max:255', // <-- ADD THIS LINE
            'chief_complaints' => 'required|string',
            'diagnosis' => 'nullable|string',
            'assessment' => 'nullable|string',
            'plan' => 'nullable|string',
            'xray_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $diagnosis = Diagnosis::create($validatedData);

        if ($request->hasFile('xray_images')) {
            foreach ($request->file('xray_images') as $file) {
                $path = $file->store('xrays', 'public');
                $diagnosis->images()->create(['image_path' => $path]);
            }
        }

        return redirect('/pets/' . $pet->id)->with('success', 'Medical record has been added successfully.');
    }

    public function update(Request $request, Diagnosis $diagnosis)
    {
         $validatedData = $request->validate([
            'checkup_date' => 'required|date',
            'weight' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'vet_id' => 'required|exists:users,id',
            'attending_staff' => 'nullable|string|max:255', // <-- ADD THIS LINE
            'chief_complaints' => 'required|string',
            'diagnosis' => 'nullable|string',
            'assessment' => 'nullable|string',
            'plan' => 'nullable|string',
            'xray_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // NO CHANGE NEEDED HERE. Laravel automatically handles the new field
        // because it's in the $fillable array and validated above.
        $diagnosis->update($validatedData);

        if ($request->hasFile('xray_images')) {
            foreach ($request->file('xray_images') as $file) {
                $path = $file->store('xrays', 'public');
                $diagnosis->images()->create(['image_path' => $path]);
            }
        }

        return redirect('/pets/' . $diagnosis->pet_id)->with('success', 'Medical record has been updated successfully.');
    }

    public function destroy(Diagnosis $diagnosis)
    {
        foreach ($diagnosis->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $petId = $diagnosis->pet_id;
        $diagnosis->delete();
        return redirect('/pets/' . $petId)->with('success', 'Medical record deleted.');
    }
    
    public function destroyImage(DiagnosisImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return redirect()->back()->with('success', 'Image deleted successfully.');
    }
}
