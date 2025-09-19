@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Pets Card -->
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-indigo-500 text-white rounded-full h-16 w-16 flex items-center justify-center">
                <i class="fas fa-paw fa-2x"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-600">Total Pets</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $totalPets }}</p>
            </div>
        </div>

        <!-- Total Owners Card -->
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-green-500 text-white rounded-full h-16 w-16 flex items-center justify-center">
                <i class="fas fa-user-friends fa-2x"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-600">Total Owners</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $totalOwners }}</p>
            </div>
        </div>

        <!-- Today's Appointments Card -->
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-blue-500 text-white rounded-full h-16 w-16 flex items-center justify-center">
                <i class="fas fa-calendar-day fa-2x"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-600">Today's Appointments</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $todaysAppointments->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Today's Schedule -->
   <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4">Today's Schedule</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-4 font-semibold">Pet Name</th>
                        <th class="p-4 font-semibold">Owner</th>
                        <th class="p-4 font-semibold">Owner Email</th>
                        <th class="p-4 font-semibold">Owner Phone</th>
                        <th class="p-4 font-semibold">Appointment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($todaysAppointments as $appointment)
                        {{-- THIS TABLE ROW IS NOW A CLICKABLE LINK --}}
                        <tr class="border-b hover:bg-gray-100 cursor-pointer" onclick="window.location='{{ url('/pets/' . $appointment->pet->id . '?tab=upcoming') }}';">
                            <td class="p-4">{{ $appointment->pet->name }}</td>
                            <td class="p-4">{{ $appointment->pet->owner->name }}</td>
                            <td class="p-4">{{ $appointment->pet->owner->email }}</td>
                            <td class="p-4">{{ $appointment->pet->owner->phone_number }}</td>
                            <td class="p-4">{{ $appointment->title }}</td>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500">No appointments scheduled for today.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $todaysAppointments->links() }}
        </div>
    </div>
@endsection
