@extends('layouts.app')

@section('title', 'Add Medical Record for ' . $pet->name)

@section('content')
<div class="bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6">New Medical Record</h2>

    <form action="{{ url('/pets/' . $pet->id . '/diagnoses') }}"method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Check-up Date -->
            <div>
                <label for="checkup_date" class="block text-sm font-medium text-gray-700">Check-up Date</label>
                <input type="date" name="checkup_date" id="checkup_date" value="{{ old('checkup_date', date('Y-m-d')) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('checkup_date')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pet Name (Read-only) -->
            <div>
                <label for="pet_name" class="block text-sm font-medium text-gray-700">Pet</label>
                <input type="text" name="pet_name" id="pet_name" value="{{ $pet->name }}" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                <input type="hidden" name="pet_id" value="{{ $pet->id }}">
            </div>

            <!-- Weight -->
            <div>
                <label for="weight" class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                <input type="number" step="0.01" name="weight" id="weight" value="{{ old('weight') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('weight')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Temperature -->
            <div>
                <label for="temperature" class="block text-sm font-medium text-gray-700">Temperature (°C)</label>
                <input type="number" step="0.1" name="temperature" id="temperature" value="{{ old('temperature') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('temperature')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            <!-- Attending Vet -->
        <div>
            <label for="vet_id" class="block text-sm font-medium text-gray-700">Attending Vet</label>
            <select name="vet_id" id="vet_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Select a Veterinarian</option>
                @foreach ($vets as $vet)
                    <option value="{{ $vet->id }}">{{ $vet->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div>
        <label for="attending_staff" class="block text-sm font-medium text-gray-700">Attending Staff</label>
        <input
            type="text"
            id="attending_staff"
            name="attending_staff"
            value="{{ old('attending_staff') }}"
            placeholder="e.g. staff name - chico macoy"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('attending_staff') border-red-500 @enderror"
        >
        @error('attending_staff')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

        <!-- Chief Complaints -->
        <div class="md:col-span-2">
            <label for="chief_complaints" class="block text-sm font-medium text-gray-700">Chief Complaints</label>
            <textarea name="chief_complaints" id="chief_complaints" rows="3" required class="mt-1 block w-full ..."></textarea>
        </div>

        <!-- Assessment -->
        <div class="md:col-span-2">
            <label for="assessment" class="block text-sm font-medium text-gray-700">Assessment</label>
            <textarea name="assessment" id="assessment" rows="3" class="mt-1 block w-full ..."></textarea>
        </div>

            <!-- Diagnosis -->
            <div class="md:col-span-2">
                <label for="diagnosis" class="block text-sm font-medium text-gray-700">Diagnosis</label>
                <textarea name="diagnosis" id="diagnosis" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('diagnosis') }}</textarea>
                @error('diagnosis')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Plan -->
            <div class="md:col-span-2">
                <label for="plan" class="block text-sm font-medium text-gray-700">Plan / Treatment</label>
                <textarea name="plan" id="plan" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('plan') }}</textarea>
                @error('plan')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- X-Ray Image Upload -->
            <div class="md:col-span-2">
                <label for="xray_images" class="block text-sm font-medium text-gray-700">Upload Results|Prescriptions</label>
                <input type="file" name="xray_images[]" id="xray_images" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                @error('xray_images.*')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <a href="{{ url('/pets/' . $pet->id) }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg mr-4 hover:bg-gray-300 transition-colors duration-300 flex items-center">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition-colors duration-300 flex items-center">
                <i class="fas fa-check mr-2"></i>Save Record
            </button>
        </div>
    </form>
</div>
@endsection
