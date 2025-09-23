@extends('layouts.app')

@section('title', 'Medical Record Details')

@section('content')
<div class="bg-white p-8 rounded-lg shadow-md">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <div>
            <h2 class="text-2xl font-bold">Medical Record for {{ $diagnosis->pet->name }}</h2>
            <p class="text-sm text-gray-500">Check-up on: {{ \Carbon\Carbon::parse($diagnosis->checkup_date)->format('M d, Y') }}</p>
        </div>
        <a href="{{ url('/pets/' . $diagnosis->pet_id) }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Pet Profile
        </a>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Left Column (Main Details) --}}
        <div class="md:col-span-2 space-y-6">
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="font-semibold text-gray-600">Chief Complaints:</p>
                <p class="mt-1">{{ $diagnosis->chief_complaints }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="font-semibold text-gray-600">Assessment:</p>
                <p class="mt-1">{{ $diagnosis->assessment ?: 'N/A' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="font-semibold text-gray-600">Diagnosis:</p>
                <p class="mt-1">{{ $diagnosis->diagnosis }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="font-semibold text-gray-600">Plan / Treatment:</p>
                <p class="mt-1">{{ $diagnosis->plan ?: 'N/A' }}</p>
            </div>
        </div>

        {{-- Right Column (Vitals) --}}
        <div class="space-y-4">
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="font-semibold text-gray-600">Attending Vet:</p>
                <p class="text-lg">{{ $diagnosis->vet->name ?? 'N/A' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="font-semibold text-gray-600">Attending Staff:</p>
                <p class="text-lg">{{ $diagnosis->attending_staff ?: 'N/A' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="font-semibold text-gray-600">Weight:</p>
                <p class="text-lg">{{ $diagnosis->weight ? $diagnosis->weight . ' kg' : 'N/A' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="font-semibold text-gray-600">Temperature:</p>
                <p class="text-lg">{{ $diagnosis->temperature ? $diagnosis->temperature . ' °C' : 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- X-Ray Images Section --}}
    @if ($diagnosis->images->count() > 0)
        <div class="mt-8">
            <h3 class="text-xl font-bold mb-4 border-b pb-2">Results and prescriptions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($diagnosis->images as $image)
                    <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="X-Ray Image" class="rounded-lg shadow-md hover:opacity-75 transition-opacity">
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
