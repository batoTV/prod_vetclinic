@extends('layouts.app')

@section('title', 'Edit Appointment') {{-- 1. Changed Title --}}

@section('content')
<div class="bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6">Edit Appointment Details</h2> {{-- 1. Changed Heading --}}

    {{-- 2. Changed Form Action and Method --}}
    <form action="{{ route('appointments.update', $appointment) }}" method="POST">
        @csrf
        @method('PUT') {{-- 3. Added PUT Method --}}
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="pet_id" class="block text-sm font-medium text-gray-700">Pet</label>
                <select name="pet_id" id="pet_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select a Pet</option>
                    @foreach ($pets as $pet)
                        {{-- 4. Modified logic to select the correct pet --}}
                        <option value="{{ $pet->id }}" {{ old('pet_id', $appointment->pet_id) == $pet->id ? 'selected' : '' }}>
                            {{ $pet->name }} ({{ $pet->owner->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Appointment Title</label>
                {{-- 4. Added value to pre-fill the input --}}
                <input type="text" name="title" id="title" required value="{{ old('title', $appointment->title) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="md:col-span-2">
                <label for="appointment_date" class="block text-sm font-medium text-gray-700">Appointment Date</label>
                {{-- 4. Added value to pre-fill the date --}}
                <input type="date" name="appointment_date" id="appointment_date" required value="{{ old('appointment_date', \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700">Description / Notes</label>
                {{-- 4. Added value inside the textarea --}}
                <textarea name="description" id="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $appointment->description) }}</textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <a href="{{ url()->previous() }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg mr-4 hover:bg-gray-300">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700">Update Appointment</button> {{-- 5. Changed Button Text --}}
        </div>
    </form>
</div>
@endsection