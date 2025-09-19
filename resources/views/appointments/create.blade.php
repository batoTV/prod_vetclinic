@extends('layouts.app')

@section('title', 'Add New Appointment')

@section('content')
<div class="bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6">New Appointment Details</h2>

    <form action="{{ url('/appointments') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Pet -->
            <div>
                <label for="pet_id" class="block text-sm font-medium text-gray-700">Pet</label>
                <select name="pet_id" id="pet_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select a Pet</option>
                    @foreach ($pets as $pet)
                        <option value="{{ $pet->id }}" {{ (isset($selectedPetId) && $selectedPetId == $pet->id) ? 'selected' : '' }}>
                            {{ $pet->name }} ({{ $pet->owner->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Appointment Title</label>
                <input type="text" name="title" id="title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Appointment Date -->
            <div class="md:col-span-2">
                <label for="appointment_date" class="block text-sm font-medium text-gray-700">Appointment Date</label>
                {{-- This input type is now 'date' --}}
                <input type="date" name="appointment_date" id="appointment_date" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700">Description / Notes</label>
                <textarea name="description" id="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <a href="{{ url()->previous() }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg mr-4 hover:bg-gray-300">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700">Save Appointment</button>
        </div>
    </form>
</div>
@endsection
