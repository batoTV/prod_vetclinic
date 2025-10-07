<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Consent;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage; // <-- Add this line
use Illuminate\Validation\Rule; 

class ConsentController extends Controller
{
  
    
// The Pet $pet is removed from the method signature
public function store(Request $request)
{
    $validated = $request->validate([
        // This now expects an array of pet IDs from your multi-select form
        'pet_ids'      => ['required', 'array'],
        'pet_ids.*'    => ['required', 'exists:pets,id'],
        'consent_type' => ['required', 'string', 'in:general,surgery,non'], 
        'notes'        => [Rule::requiredIf($request->input('consent_type') === 'non'), 'nullable', 'string'],
        'signature'    => ['required', 'string'],
    ]);

    // Loop through each pet ID that was submitted
    foreach ($validated['pet_ids'] as $petId) {
        // Find the pet for the current loop iteration
        $pet = Pet::find($petId);

        // --- All of your existing PDF logic is now inside the loop ---
        $pdfTemplate = 'pdfs.' . $validated['consent_type'] . '_consent';

        $dataForPdf = [
            'ownerName' => $pet->owner->name,
            'petName'   => $pet->name,
            'date'      => now()->format('M d, Y'),
            'signature' => $validated['signature'],
            'notes'     => $validated['notes'],
        ];

        $pdf = Pdf::loadView($pdfTemplate, $dataForPdf);
        $filename = $validated['consent_type'] . '-consent-' . $pet->id . '-' . time() . '.pdf';
        $filePath = 'consent_forms/' . $filename;

        Storage::disk('public')->put($filePath, $pdf->output());

        $pet->consents()->create([
            'consent_type' => $validated['consent_type'],
            'notes'        => $validated['notes'],
            'file_path'    => $filePath,
        ]);
    }

    return redirect()->route('client.success', ['action' => 'consent']);
}
 public function destroy(Consent $consent)
    {
        // Delete the associated file from storage if it exists
        if ($consent->file_path) {
            Storage::disk('public')->delete($consent->file_path);
        }

        // Delete the record from the database
        $consent->delete();

        // Redirect back with a success message
        return back()->with('success', 'Consent record deleted successfully.');
    }

}
