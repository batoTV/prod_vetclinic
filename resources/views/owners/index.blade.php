@extends('layouts.app')

@section('title', 'Owners')

@section('header-actions')
    <a href="{{ url('/owners/create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition-colors duration-300 flex items-center">
        <i class="fas fa-plus mr-2"></i> Add New Owner
    </a>
@endsection

@section('content')

@if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif

@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <strong class="font-bold">Success!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif


<div class="bg-white p-6 rounded-lg shadow-md">
    <!-- Search and Filter Form -->
    <form action="{{ url('/owners') }}" method="GET" class="mb-6">
        <div class="relative">
            <input type="text" name="search" placeholder="Search for an owner by name..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
    </form>

    <!-- Owner List Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4 font-semibold">Name</th>
                    <th class="p-4 font-semibold">Email</th>
                    <th class="p-4 font-semibold">Phone Number</th>
                    <th class="p-4 font-semibold text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($owners as $owner)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4">{{ $owner->name }}</td>
                        <td class="p-4">{{ $owner->email }}</td>
                        <td class="p-4">{{ $owner->phone_number }}</td>
                        <td class="p-4 text-center">
                             <a href="{{ url('/owners/' . $owner->id) }}" class="text-indigo-600 hover:text-indigo-800 mr-4" title="View Profile"> {{-- <-- THIS LINK IS UPDATED --}}
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ url('/owners/' . $owner->id . '/edit') }}" class="text-green-600 hover:text-green-800 mr-4" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <button type="button" class="text-red-600 hover:text-red-800 delete-button" data-url="{{ url('/owners/' . $owner->id) }}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">No owners found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-6">
        {{ $owners->links() }}
    </div>
    </div>
</div>
@endsection
