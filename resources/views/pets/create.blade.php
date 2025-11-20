@extends('layouts.app')

@section('title', 'Add a New Pet')

@section('content')
<div class="bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6">New Pet Details</h2>

    <form action="{{ url('/pets') }}"  method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Pet Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Pet Name</label>
                <input type="text" name="name" id="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Owner -->
<div>
    <label for="owner_id" class="block text-sm font-medium text-gray-700">Owner</label>

    {{-- This variable name MUST match the key from the controller --}}
    @if ($owner) 
        
        {{-- If an owner is pre-selected, show this disabled input --}}
        <input type="text" disabled value="{{ $owner->last_name }}, {{ $owner->first_name }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
        <input type="hidden" name="owner_id" value="{{ $owner->id }}">

   @else
        {{-- NEW SEARCHABLE DROPDOWN --}}
        <div x-data="{
            open: false,
            search: '',
            allOwners: @js($allOwners->map(fn($o) => ['id' => $o->id, 'full_name' => $o->full_name])),
            selectedOwnerId: {{ $selectedOwnerId ?? 'null' }},

            get filteredOwners() {
                if (this.search === '') {
                    // Show all owners if search is empty
                    return this.allOwners;
                }
                // Filter owners based on search text
                return this.allOwners.filter(owner => 
                    owner.full_name.toLowerCase().includes(this.search.toLowerCase())
                );
            },
            
            selectOwner(owner) {
                this.selectedOwnerId = owner.id; // Set the hidden input value
                this.search = owner.full_name;   // Set the visible input text
                this.open = false;               // Close the dropdown
            }
        }" class="relative">
    
            {{-- This hidden input stores and submits the selected owner ID --}}
            <input type="hidden" name="owner_id" x-model="selectedOwnerId">
    
            {{-- This is the visible text input for searching --}}
            <input type="text"
                   x-model="search"
                   @input.debounce.300ms="open = true"
                   @focus="open = true"
                   @click.away="open = false"
                   @keydown.escape.prevent="open = false; search = '';"
                   placeholder="Type to search for an owner..."
                   required
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
    
            {{-- This is the dropdown list of results --}}
            <div x-show="open"
                 x-transition
                 class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg max-h-60 overflow-y-auto border border-gray-200">
                <ul class="py-1">
                    <template x-for="owner in filteredOwners" :key="owner.id">
                        <li @click="selectOwner(owner)"
                            class="px-4 py-2 text-sm text-gray-700 cursor-pointer hover:bg-indigo-50">
                            <span x-text="owner.full_name"></span>
                        </li>
                    </template>
                    
                    {{-- Show a message if no results are found --}}
                    <li x-show="filteredOwners.length === 0 && search.length > 0" 
                        class="px-4 py-2 text-sm text-gray-500 italic">
                        No owners found matching "<span x-text="search"></span>"
                    </li>
                </ul>
            </div>
        </div>
    @endif
</div>
        
                
                
                 

            <!-- Species -->
         <div>
            <label for="species" class="block text-sm font-medium text-gray-700">Species</label>
            
            {{-- The text input is now a dropdown menu --}}
            <select name="species" id="species" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Select a Species</option>
                <option value="Feline">Feline</option>
                <option value="Canine">Canine</option>
            </select>
        </div>

            <!-- Breed -->
            <div>
                <label for="breed" class="block text-sm font-medium text-gray-700">Breed</label>
                <input type="text" name="breed" id="breed" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Birth Date -->
            <div>
                <label for="birth_date" class="block text-sm font-medium text-gray-700">Birth Date</label>
                <input type="date" name="birth_date" id="birth_date" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Gender -->
            <div>
                <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                <select name="gender" id="gender" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
             <!-- Markings -->
            <div>
                <label for="markings" class="block text-sm font-medium text-gray-700">Markings / Color</label>
                <input type="text" name="markings" id="markings" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <!-- Allergies -->
            <div class="md:col-span-2">
                <label for="allergies" class="block text-sm font-medium text-gray-700">Health Notes / Allergies</label>
                <textarea name="allergies" id="allergies" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{-- For edit form, add: old('allergies', $pet->allergies) --}}</textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            @if(isset($owner) && $owner)
                {{-- Case 1: We know the owner, go back to their profile --}}
                <a href="{{ route('owners.show', $owner->id) }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg mr-4 hover:bg-gray-300 transition-colors duration-300 flex items-center">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            @else
                {{-- Case 2: No specific owner, go back to the general Pets list --}}
                <a href="{{ route('pets.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg mr-4 hover:bg-gray-300 transition-colors duration-300 flex items-center">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            @endif
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition-colors duration-300 flex items-center">
                <i class="fas fa-check mr-2"></i>Save Pet
            </button>
        </div>
    </form>
</div>
@endsection
