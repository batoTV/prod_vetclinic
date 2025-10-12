<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Pet;
use App\Models\Diagnosis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientRegistrationController extends Controller
{
    /**
     * Show the client registration form.
     */
    public function create()
    {
        // Fetch all owners and load their pets at the same time
        $ownersWithPets = Owner::with('pets')->get();

        return view('auth.client-register', [
            'ownersWithPets' => $ownersWithPets,
        ]);
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        if ($request->input('client_status') === 'new') {
            return $this->storeNewOwner($request);
        } else {
            return $this->storeExistingOwner($request);
        }
    }
    
    /**
     * Store a new owner and their pet(s).
     */
    private function storeNewOwner(Request $request)
{
    // 1. VALIDATION RULES UPDATED FOR CLARITY AND ACCURACY
    $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:owners'],
        'phone_number' => ['required', 'digits:11', 'unique:owners'],
        'address' => ['required', 'string'],
        'pets' => ['present', 'array'], // Use 'present' to ensure the pets array exists, even if empty
        'pets.*.name' => ['required', 'string', 'max:255'],
        'pets.*.species' => ['required', 'string', 'max:255'],
        'pets.*.breed' => ['nullable', 'string', 'max:255'],
        'pets.*.birth_date' => ['required', 'date'],
        'pets.*.gender' => ['required', 'in:Male,Female'],
        'pets.*.allergies' => ['nullable', 'string'],
        'pets.*.markings' => ['nullable', 'string', 'max:255'],
        'pets.*.chief_complaints' => ['nullable', 'string'], // **FIXED**: Changed to singular
    ];

    // 2. ADDED CUSTOM MESSAGES FOR BETTER USER FEEDBACK
    $messages = [
        'pets.*.name.required' => 'The pet\'s name is required.',
        'pets.*.species.required' => 'Please select a species for the pet.',
        'pets.*.birth_date.required' => 'The pet\'s birth date is required.',
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Use the validated data for security
    $validated = $validator->validated();

    DB::beginTransaction();
    try {
        // Create owner from the validated data
        $owner = Owner::create($validated);

        if (!empty($validated['pets'])) {
            foreach ($validated['pets'] as $petData) {
                // The 'if' check is no longer needed because validation already ensures name/species exist
                $pet = $owner->pets()->create($petData);

                // **FIXED**: Check for singular 'chief_complaints'
                if (!empty($petData['chief_complaints'])) {
                    // **IMPROVED**: Create diagnosis using the relationship
                    $pet->diagnoses()->create([
                        'checkup_date' => now(),
                        'chief_complaints' => $petData['chief_complaints'],
                    ]);
                }
            }
        }

        DB::commit();
        return redirect()->route('client.success', ['action' => 'register']);
    } catch (\Exception $e) {
        DB::rollBack();
        // Log the actual error for your records and show a generic message to the user
        \Log::error('Client Registration Failed: ' . $e->getMessage());
        return redirect()->back()->withErrors(['general' => 'An unexpected error occurred. Please try again.'])->withInput();
    }
}

    /**
 * Store pet(s) for an existing owner.
 */
private function storeExistingOwner(Request $request)
{
    // 1. VALIDATION RULES UPDATED FOR CLARITY AND ACCURACY
    $rules = [
        'owner_id' => ['required', 'exists:owners,id'],
        'pets' => ['present', 'array'], // Use 'present' to ensure the array exists, even if empty
        'pets.*.name' => ['required', 'string', 'max:255'],
        'pets.*.species' => ['required', 'string', 'max:255'],
        'pets.*.breed' => ['nullable', 'string', 'max:255'],
        'pets.*.birth_date' => ['required', 'date'],
        'pets.*.gender' => ['required', 'in:Male,Female'],
        'pets.*.allergies' => ['nullable', 'string'],
        'pets.*.markings' => ['nullable', 'string', 'max:255'],
        'pets.*.chief_complaints' => ['nullable', 'string'], // **FIXED**: Changed to singular
    ];

    // 2. ADDED CUSTOM MESSAGES FOR BETTER USER FEEDBACK
    $messages = [
        'pets.*.name.required' => 'The pet\'s name is required.',
        'pets.*.species.required' => 'Please select a species for the pet.',
        'pets.*.birth_date.required' => 'The pet\'s birth date is required.',
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Use the validated data for security
    $validated = $validator->validated();

    DB::beginTransaction();
    try {
        $owner = Owner::findOrFail($validated['owner_id']);

        if (!empty($validated['pets'])) {
            foreach ($validated['pets'] as $petData) {
                // The 'if' check for name is no longer needed because validation handles it
                $pet = $owner->pets()->create($petData);

                // **FIXED**: Check for singular 'chief_complaints'
                if (!empty($petData['chief_complaints'])) {
                    // Create diagnosis using the pet relationship
                    $pet->diagnoses()->create([
                        'checkup_date' => now(),
                        'chief_complaints' => $petData['chief_complaints'],
                    ]);
                }
            }
        }

        DB::commit();
        return redirect()->route('client.success', ['action' => 'register']);

    } catch (\Exception $e) {
        DB::rollBack();
        // Log the actual error for debugging and show a generic message to the user
        \Log::error('Existing Client Pet Registration Failed: ' . $e->getMessage());
        return redirect()->back()->withErrors(['general' => 'An unexpected error occurred. Please try again.'])->withInput();
    }
}
    /**
     * Find an existing owner.
     */
    public function findOwner(Request $request)
    {
        $request->validate([
            'name' => 'required_without:phone_number|string|max:255',
            'phone_number' => 'required_without:name|string|max:255',
        ]);

        $query = Owner::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('phone_number')) {
            $query->where('phone_number', $request->phone_number);
        }

        $owner = $query->first();

        if ($owner) {
            return response()->json([
                'success' => true, 
                'owner' => $owner, 
                'message' => 'Welcome back, ' . $owner->name . '! We\'ve found your record. Please add your pet\'s information below.'
            ]);
        }

        return response()->json([
            'success' => false, 
            'message' => 'We could not find a record matching that information. Please double-check your details or register as a new client.'
        ]);
    }

    /**
     * Show the registration success page.
     */
    public function success()
    {
        return view('auth.register-success');
    }
}