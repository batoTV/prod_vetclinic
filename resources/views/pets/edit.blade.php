@extends('layouts.app')

@section('title', 'Edit Pet: ' . $pet->name)

@section('content')
<div class="bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6">Edit Pet Details</h2>

    <form action="{{ url('/pets/' . $pet->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Pet Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Pet Name</label>
                <input type="text" name="name" id="name" required value="{{ old('name', $pet->name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Owner -->
            <div>
    <label for="owner_id" class="block text-sm font-medium text-gray-700">Owner</label>

    {{-- NEW SEARCHABLE DROPDOWN --}}
    <div x-data="{
        open: false,
        search: '{{ $pet->owner->full_name ?? '' }}',
        allOwners: @js($owners->map(fn($o) => ['id' => $o->id, 'full_name' => $o->full_name])),
        selectedOwnerId: {{ $pet->owner_id ?? 'null' }},

        get filteredOwners() {
            if (this.search === '{{ $pet->owner->full_name ?? '' }}') {
                return this.allOwners; // Show all if input hasn't changed from default
            }
            if (this.search === '') {
                return this.allOwners;
            }
            return this.allOwners.filter(owner => 
                owner.full_name.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        
        selectOwner(owner) {
            this.selectedOwnerId = owner.id;
            this.search = owner.full_name;
            this.open = false;
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
               @keydown.escape.prevent="open = false; search = '{{ $pet->owner->full_name ?? '' }}';"
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
                        class="px-4 py-2 text-sm text-gray-700 cursor-pointer hover:bg-indigo-50"
                        :class="{ 'bg-indigo-100': selectedOwnerId === owner.id }">
                        <span x-text="owner.full_name"></span>
                    </li>
                </template>
                
                <li x-show="filteredOwners.length === 0 && search.length > 0" 
                    class="px-4 py-2 text-sm text-gray-500 italic">
                    No owners found matching "<span x-text="search"></span>"
                </li>
            </ul>
        </div>
    </div>
</div>

            <!-- Species -->
            <div>
    <label for="species" class="block text-sm font-medium text-gray-700">Species</label>
    
    <select name="species" id="species" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">Select a Species</option>
        
        <option value="Feline" {{ old('species', $pet->species) == 'Feline' ? 'selected' : '' }}>
            Feline
        </option>
        
        <option value="Canine" {{ old('species', $pet->species) == 'Canine' ? 'selected' : '' }}>
            Canine
        </option>
    </select>
    
</div>

            <!-- Breed -->
            <div>
                <label for="breed" class="block text-sm font-medium text-gray-700">Breed</label>
                <input type="text" name="breed" id="breed" required value="{{ old('breed', $pet->breed) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Birth Date -->
            <div>
                <label for="birth_date" class="block text-sm font-medium text-gray-700">Birth Date</label>
                <input type="date" name="birth_date" id="birth_date" required value="{{ old('birth_date', $pet->birth_date) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Gender -->
            <div>
                <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                <select name="gender" id="gender" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="Male" {{ old('gender', $pet->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $pet->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
             <!-- Markings -->
            <div>
                <label for="markings" class="block text-sm font-medium text-gray-700">Markings / Color</label>
                <input type="text" name="markings" id="markings" value="{{ old('markings', $pet->markings) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <!-- Allergies -->
            <div class="md:col-span-2">
                <label for="allergies" class="block text-sm font-medium text-gray-700">Allergies</label>
                <textarea name="allergies" id="allergies" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('allergies', $pet->allergies) }}</textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <a href="{{ url('/pets') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg mr-4 hover:bg-gray-300 transition-colors duration-300 flex items-center">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition-colors duration-300 flex items-center">
                <i class="fas fa-check mr-2"></i>Update Pet
            </button>
        </div>
    </form>
</div>
@endsection
