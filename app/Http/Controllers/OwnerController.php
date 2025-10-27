<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Http\Request; // Make sure this line is present

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    // Add orderBy() here to set the default sort
    $query = Owner::query()->orderBy('last_name', 'asc');

    // Check if the search term is present and not empty
    if ($request->has('search') && $request->input('search') != '') {
        $searchTerm = $request->input('search');
    
        $query->where(function ($subQuery) use ($searchTerm) {
            $subQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                     ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
        });
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
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'phone_number' => 'required|digits:11|unique:owners,phone_number',
        'email' => 'required|email|max:255|unique:owners,email',
        'address' => 'required|string',
    ]);

        Owner::create($validatedData);

        return redirect()->route('owners.index')->with('success', 'Owner added successfully.');
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
            'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:owners,email,' . $owner->id,
            'phone_number' => 'required|digits:11|unique:owners,phone_number,' . $owner->id,
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
