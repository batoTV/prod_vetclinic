@extends('layouts.app')

@section('title', 'Edit Medical Record for ' . $diagnosis->pet->name)

@section('content')
<div class="bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6">Edit Medical Record</h2>

    <form action="{{ url('/diagnoses/' . $diagnosis->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Check-up Date -->
            <div>
                <label for="checkup_date" class="block text-sm font-medium text-gray-700">Check-up Date</label>
                <input type="date" name="checkup_date" id="checkup_date" value="{{ $diagnosis->checkup_date }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Pet Name (Read-only) -->
            <div>
                <label for="pet_name" class="block text-sm font-medium text-gray-700">Pet</label>
                <input type="text" name="pet_name" id="pet_name" value="{{ $diagnosis->pet->name }}" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
            </div>

            <!-- Weight -->
            <div>
                <label for="weight" class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                <input type="number" step="0.01" name="weight" id="weight" value="{{ $diagnosis->weight }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Temperature -->
            <div>
                <label for="temperature" class="block text-sm font-medium text-gray-700">Temperature (°C)</label>
                <input type="number" step="0.1" name="temperature" id="temperature" value="{{ $diagnosis->temperature }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <!-- Attending Vet -->
        <div>
            <label for="vet_id" class="block text-sm font-medium text-gray-700">Attending Vet</label>
            <select name="vet_id" id="vet_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Select a Veterinarian</option>
                @foreach ($vets as $vet)
                    <option value="{{ $vet->id }}" @selected(old('vet_id', $diagnosis->vet_id) == $vet->id)>
                        {{ $vet->name }}
                    </option>
                @endforeach
            </select>
        </div>

         <div>
        <label for="attending_staff" class="block text-sm font-medium text-gray-700">Attending Staff</label>
        <input
            type="text"
            id="attending_staff"
            name="attending_staff"
            value="{{ old('attending_staff', $diagnosis->attending_staff) }}"
            placeholder="e.g., Staff Name - Chico Macoy"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('attending_staff') border-red-500 @enderror"
        >
        @error('attending_staff')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

        <!-- Chief Complaints -->
        <div class="md:col-span-2">
            <label for="chief_complaints" class="block text-sm font-medium text-gray-700">Chief Complaints</label>
            <textarea name="chief_complaints" id="chief_complaints" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ $diagnosis->chief_complaints}}</textarea>
        </div>

        <!-- Assessment -->
        <div class="md:col-span-2">
            <label for="assessment" class="block text-sm font-medium text-gray-700">Assessment</label>
            <textarea name="assessment" id="assessment" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ $diagnosis->assessment}}</textarea>
        </div>
            <!-- Diagnosis -->
            <div class="md:col-span-2">
                <label for="diagnosis" class="block text-sm font-medium text-gray-700">Diagnosis</label>
                <textarea name="diagnosis" id="diagnosis" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ $diagnosis->diagnosis }}</textarea>
            </div>

            <!-- Plan -->
            <div class="md:col-span-2">
                <label for="plan" class="block text-sm font-medium text-gray-700">Plan / Treatment</label>
                <textarea name="plan" id="plan" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ $diagnosis->plan }}</textarea>
            </div>

            <!-- X-Ray Image Upload -->
            <div class="md:col-span-2">
                <label for="xray_images" class="block text-sm font-medium text-gray-700">Upload More Images</label>
                <input type="file" name="xray_images[]" id="xray_images" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                @error('xray_image')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current X-Ray Image -->
            @if ($diagnosis->xray_image_path)
                <div class="md:col-span-2">
                    <p class="block text-sm font-medium text-gray-700">Current Image</p>
                    <a href="{{ asset('storage/' . $diagnosis->xray_image_path) }}" target="_blank" class="cursor-pointer">
                        <img src="{{ asset('storage/' . $diagnosis->xray_image_path) }}" alt="X-Ray Image" class="mt-2 rounded-lg shadow-md max-w-xs h-auto">
                    </a>
                </div>
            @endif
        </div>

        <div class="mt-8 flex justify-end">
            <a href="{{ url('/pets/' . $diagnosis->pet_id) }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg mr-4 hover:bg-gray-300 transition-colors duration-300 flex items-center">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition-colors duration-300 flex items-center">
                <i class="fas fa-check mr-2"></i>Update Record
            </button>
        </div>
    </form>

     @if ($diagnosis->images->count() > 0)
        <div class="mt-8">
            <h3 class="text-xl font-bold mb-4 border-b pb-2">Current X-Ray Images</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($diagnosis->images as $image)
                    <div class="relative group">
                        <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="X-Ray Image" class="rounded-lg shadow-md hover:opacity-75 transition-opacity">
                        </a>
                         <button type="button" class="delete-button absolute top-2 right-2 bg-red-600 text-white rounded-full h-6 w-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity" data-url="/diagnosis-images/{{ $image->id }}" title="Delete Image">
                            &times;
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
