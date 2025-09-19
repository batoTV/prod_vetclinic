@extends('layouts.app')

@section('title', 'Owner Profile ')

@section('content')
    {{-- Main Header --}}
     <div class="flex items-center mb-8">
        <div class="bg-gray-200 text-gray-700 rounded-full h-24 w-24 flex items-center justify-center">
            <span class="text-4xl font-bold">{{ substr($owner->name, 0, 1) }}</span>
        </div>
        <div class="ml-6">
            <h2 class="text-4xl font-bold">{{ $owner->name }}</h2>
        </div>
    </div>

    {{-- Two-Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column: Contact Info --}}
        <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-bold mb-4 border-b pb-2">Contact Information</h3>
            <div class="space-y-3 text-gray-700">
                <div>
                    <p class="font-semibold">Email:</p>
                    <p>{{ $owner->email }}</p>
                </div>
                <div>
                    <p class="font-semibold">Phone:</p>
                    <p>{{ $owner->phone_number }}</p>
                </div>
                <div>
                    <p class="font-semibold">Address:</p>
                    <p>{{ $owner->address }}</p>
                </div>
            </div>
        </div>

        {{-- Right Column: Registered Pets --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Registered Pets</h3>
                <a href="{{ url('/pets/create?owner_id=' . $owner->id) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 flex items-center">
                    <i class="fas fa-plus mr-2"></i> Register New Pet
                </a>
            </div>
            
            @if($owner->pets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-4 font-semibold">Pet Name</th>
                                <th class="p-4 font-semibold">Species</th>
                                <th class="p-4 font-semibold">Breed</th>
                                <th class="p-4 font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($owner->pets as $pet)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-4">{{ $pet->name }}</td>
                                    <td class="p-4">{{ $pet->species }}</td>
                                    <td class="p-4">{{ $pet->breed }}</td>
                                    <td class="p-4 text-center">
                                        <a href="{{ url('/pets/' . $pet->id) }}" class="text-indigo-600 hover:text-indigo-800" title="View Pet Profile">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">This owner has no pets registered.</p>
            @endif
        </div>
    </div>
@endsection
