@extends('layouts.app')

@section('title', 'Pets')

@section('header-actions')
     <a href="{{ url('/pets/create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition-colors duration-300 flex items-center">
        <i class="fas fa-plus mr-2"></i> Add New Pet
    </a>
@endsection

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    
    <!-- Search and Filter Form -->
    <form action="{{ url('/pets') }}" method="GET" class="mb-6">
        <div class="relative">
            <input type="text" name="search" placeholder="Search for a pet by name or owner..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
    </form>

    <!-- Pet List Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4 font-semibold">Pet Name</th>
                    <th class="p-4 font-semibold">Species</th>
                    <th class="p-4 font-semibold">Breed</th>
                    <th class="p-4 font-semibold">Owner</th>
                    <th class="p-4 font-semibold text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pets as $pet)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4">{{ $pet->name }}</td>
                        <td class="p-4">{{ $pet->species }}</td>
                        <td class="p-4">{{ $pet->breed }}</td>
                        <td class="p-4">{{ $pet->owner->name }}</td>
                        <td class="p-4 text-center">
                            <a href="{{ url('/pets/' . $pet->id) }}"class="text-indigo-600 hover:text-indigo-800 mr-4" title="View Profile">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ url('/pets/' . $pet->id . '/edit') }}" class="text-green-600 hover:text-green-800 mr-4" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <button type="button" class="text-red-600 hover:text-red-800 delete-button" data-url="{{ url('/pets/' . $pet->id) }}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">No pets found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-6">
        {{ $pets->links() }}
    </div>
    </div>
</div>
@endsection
