<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Http\Request; // Make sure this line is present

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // <-- THIS IS THE FIX
    {
        $query = Owner::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        $owners = $query->paginate(15);

        return view('owners.index', ['owners' => $owners]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('owners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'email' => 'required|email|unique:owners,email',
            'address' => 'required|string',
        ]);

        Owner::create($validatedData);

        return redirect('/owners')->with('success', 'Owner has been added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Owner $owner)
    {
        return view('owners.edit', ['owner' => $owner]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Owner $owner)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'email' => 'required|email|unique:owners,email,' . $owner->id,
            'address' => 'required|string',
        ]);

        $owner->update($validatedData);

        return redirect('/owners')->with('success', 'Owner has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Owner $owner)
    {
        if ($owner->pets()->count() > 0) {
            return redirect('/owners')->with('error', 'Cannot delete owner: This owner still has pets registered in the system.');
        }

        $owner->delete();

        return redirect('/owners')->with('success', 'Owner has been deleted successfully.');
    }

        /**
     * Display the specified resource.
     */
    public function show(Owner $owner)
    {
        // Eager load the owner's pets to prevent extra queries
        $owner->load('pets');
        
        return view('owners.show', ['owner' => $owner]);
    }

    public function getPetsJson(Owner $owner)
    {
        // We load the pets from the relationship and return them as JSON
        return response()->json($owner->pets);
    }
}
