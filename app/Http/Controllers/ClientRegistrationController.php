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
        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:owners'],
             'phone_number' => ['required', 'digits:11', 'unique:owners'],
            'address' => ['nullable', 'string'],
            'pets' => ['nullable', 'array'],
            'pets.*.name' => ['nullable', 'string', 'max:255'],
            'pets.*.species' => ['nullable', 'string', 'max:255'],
            'pets.*.breed' => ['nullable', 'string', 'max:255'],
            'pets.*.birth_date' => ['nullable', 'date'],
            'pets.*.gender' => ['nullable', 'in:Male,Female'],
            'pets.*.allergies' => ['nullable', 'string'],
            'pets.*.markings' => ['nullable', 'string', 'max:255'],
            'pets.*.chief_complaint' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $owner = Owner::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
            ]);

            if ($request->has('pets')) {
                foreach ($request->pets as $petData) {
                    if (!empty($petData['name']) && !empty($petData['species'])) {
                        $pet = $owner->pets()->create([
                            'name' => $petData['name'],
                            'species' => $petData['species'],
                            'breed' => $petData['breed'],
                            'birth_date' => $petData['birth_date'],
                            'gender' => $petData['gender'],
                            'allergies' => $petData['allergies'] ?? null,
                            'markings' => $petData['markings'] ?? null,
                        ]);

                        // CORRECTED LOGIC: Check for a chief complaint for THIS pet and create a diagnosis
                        if (!empty($petData['chief_complaint'])) {
                            Diagnosis::create([
                                'pet_id' => $pet->id, // Use the current pet's ID
                                'checkup_date' => now(),
                                'chief_complaints' => $petData['chief_complaint'],
                                'diagnosis' => $petData['chief_complaint'],
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('client.success', ['action' => 'register']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['general' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Store pet(s) for an existing owner.
     */
    private function storeExistingOwner(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'owner_id' => ['required', 'exists:owners,id'],
            'pets' => ['nullable', 'array'],
            'pets.*.name' => ['nullable', 'string', 'max:255'],
            'pets.*.species' => ['nullable', 'string', 'max:255'],
            'pets.*.breed' => ['nullable', 'string', 'max:255'],
            'pets.*.birth_date' => ['nullable', 'date'],
            'pets.*.gender' => ['nullable', 'in:Male,Female'],
            'pets.*.allergies' => ['nullable', 'string'],
            'pets.*.markings' => ['nullable', 'string', 'max:255'],
            'pets.*.chief_complaint' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $owner = Owner::findOrFail($request->owner_id);
            if ($request->has('pets')) {
                foreach ($request->pets as $petData) {
                    if (!empty($petData['name']) && !empty($petData['species'])) {
                        $pet = $owner->pets()->create([
                            'name' => $petData['name'],
                            'species' => $petData['species'],
                            'breed' => $petData['breed'],
                            'birth_date' => $petData['birth_date'],
                            'gender' => $petData['gender'],
                            'allergies' => $petData['allergies'] ?? null,
                            'markings' => $petData['markings'] ?? null,
                        ]);

                        // CORRECTED LOGIC: Check for a chief complaint for THIS pet and create a diagnosis
                        if (!empty($petData['chief_complaint'])) {
                            Diagnosis::create([
                                'pet_id' => $pet->id, // Use the current pet's ID
                                'checkup_date' => now(),
                                'chief_complaints' => $petData['chief_complaint'],
                                'diagnosis' => $petData['chief_complaint'],
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('client.success', ['action' => 'register']);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['general' => $e->getMessage()])->withInput();
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