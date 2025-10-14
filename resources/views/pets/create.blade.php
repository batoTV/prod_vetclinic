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

        {{-- Otherwise, show the full dropdown --}}
        <select name="owner_id" id="owner_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">Select an Owner</option>
            @foreach ($allOwners as $o)
                <option value="{{ $o->id }}">{{ $o->last_name }}, {{ $o->first_name }}</option>
            @endforeach
        </select>

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
                <label for="allergies" class="block text-sm font-medium text-gray-700">Allergies</label>
                <textarea name="allergies" id="allergies" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{-- For edit form, add: old('allergies', $pet->allergies) --}}</textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <a href="{{ url()->previous() }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg mr-4 hover:bg-gray-300 transition-colors duration-300 flex items-center">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition-colors duration-300 flex items-center">
                <i class="fas fa-check mr-2"></i>Save Pet
            </button>
        </div>
    </form>
</div>
@endsection
