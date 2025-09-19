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
  
    
public function store(Request $request, Pet $pet)
{
    $validated = $request->validate([
        // Add 'non' to the list of accepted types
        
        'consent_type' => ['required', 'string', 'in:general,surgery,non'], 
        
        // Make 'notes' required ONLY IF the type is 'non'
        'notes' => [Rule::requiredIf($request->input('consent_type') === 'non'), 'nullable', 'string'],
        
        'signature' => ['required', 'string'],
    ]);

    // Determine which PDF template to use
    $pdfTemplate = 'pdfs.' . $validated['consent_type'] . '_consent';

    $dataForPdf = [
        'ownerName' => $pet->owner->name,
        'petName' => $pet->name,
        'date' => now()->format('M d, Y'),
        'signature' => $validated['signature'],
        'notes' => $validated['notes'],
    ];

    $pdf = Pdf::loadView($pdfTemplate, $dataForPdf);
    $filename = $validated['consent_type'] . '-consent-' . $pet->id . '-' . time() . '.pdf';
    $filePath = 'consent_forms/' . $filename;

    Storage::disk('public')->put($filePath, $pdf->output());

    // Save the record with the consent type
    $pet->consents()->create([
        'consent_type' => $validated['consent_type'],
        'notes' => $validated['notes'],
        'file_path' => $filePath,
    ]);

    return redirect()->route('client.success', ['action' => 'consent']);
}

public function downloadPdf(Consent $consent)
{
	dd(
		'Logged-in User ID:',
		auth()->id(),
		'Pet Owner ID:',
		$consent->pet->owner_id
	);
}
}
