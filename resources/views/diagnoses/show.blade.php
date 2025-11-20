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
        <div class="flex items-center space-x-7">
          <a href="{{ route('diagnoses.edit', $diagnosis) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700">
            Edit Record
        </a>
        <a href="{{ url('/pets/' . $diagnosis->pet_id) }}" class="bg-gray-200 ml-4 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Pet Profile
        </a>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Left Column (Main Details) --}}
        <div class="md:col-span-2 space-y-6">
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="font-semibold text-gray-600">Chief Complaints:</p>
                <p class="mt-1">{{ $diagnosis->chief_complaints }}</p>
            </div>
            <!-- <div class="p-4 bg-gray-50 rounded-lg">
                <p class="font-semibold text-gray-600">Assessment:</p>
                <p class="mt-1">{{ $diagnosis->assessment ?: 'N/A' }}</p>
            </div> -->
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

    <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-bold mb-4 border-b pb-2">Appointments Scheduled</h3>
    
    {{-- This wrapper div makes the table scrollable --}}
    <div class="max-h-96 overflow-y-auto border rounded-lg">
        <table class="w-full text-left">
            <thead class="bg-gray-100 sticky top-0">
                <tr>
                    <th class="p-4 font-semibold">Appointment Date</th>
                    <th class="p-4 font-semibold">Title</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($diagnosis->appointments as $appointment)
                    <tr>
                        <td class="p-4">{{ $appointment->appointment_date->format('M d, Y') }}</td>
                        <td class="p-4">{{ $appointment->title }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="p-4 text-center text-gray-500">No appointments are linked to this medical record.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

    {{-- X-Ray Images Section --}}
    @if ($diagnosis->images->count() > 0)
        <div class="mt-8">
            <h3 class="text-xl font-bold mb-4 border-b pb-2">Results and Prescriptions</h3>
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
<div class="mt-8 bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-bold mb-4 border-b pb-2">Record History</h3>
    
    <div class="max-h-96 overflow-y-auto">
        <ul role="list" class="-mb-8">
            @forelse ($diagnosis->audits->sortByDesc('created_at') as $audit)
                <li>
                    <div class="relative pb-8">
                        @if (!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                        @endif
                        <div class="relative flex space-x-3">
                            <div>
                                <span class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center ring-8 ring-white">
                                    @if ($audit->event == 'created')
                                        <i class="fas fa-plus text-green-600"></i>
                                    @elseif ($audit->event == 'updated')
                                        <i class="fas fa-pen text-blue-600"></i>
                                    @elseif ($audit->event == 'deleted')
                                        <i class="fas fa-trash text-red-600"></i>
                                    @endif
                                </span>
                            </div>
                            <div class="min-w-0 flex-1 pt-1.5">
                                <div>
                                    <p class="text-sm text-gray-500">
                                        <span class="font-medium text-gray-900">
                                            {{ $audit->user->name ?? 'System' }}
                                        </span>
                                        {{ $audit->event }} this record
                                        <span class="whitespace-nowrap">{{ $audit->created_at->diffForHumans() }}</span>
                                    </p>
                                </div>
                                
                                {{-- Show specific changes if it was an update --}}
                                @if ($audit->event == 'updated')
                                    <div class="mt-2 text-sm text-gray-700 bg-gray-50 p-2 rounded border">
                                        <ul class="list-disc list-inside">
                                            @foreach ($audit->getModified() as $attribute => $modified)
                                                <li>
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $attribute)) }}:</strong> 
                                                    from <span class="text-red-500 line-through">{{ $modified['old'] ?? 'blank' }}</span> 
                                                    to <span class="text-green-600 font-semibold">{{ $modified['new'] }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li>
                    <p class="text-gray-500 italic">No history recorded for this item.</p>
                </li>
            @endforelse
        </ul>
    </div>
</div>

@endsection
