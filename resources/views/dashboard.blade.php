@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-indigo-500 text-white rounded-full h-16 w-16 flex items-center justify-center">
                <i class="fas fa-paw fa-2x"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-600">Total Pets</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $totalPets }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-green-500 text-white rounded-full h-16 w-16 flex items-center justify-center">
                <i class="fas fa-user-friends fa-2x"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-600">Total Owners</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $totalOwners }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-blue-500 text-white rounded-full h-16 w-16 flex items-center justify-center">
                <i class="fas fa-calendar-day fa-2x"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-600">Today's Appointments</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $todaysAppointments }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
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
                    @forelse ($todaysAppointmentsList as $appointment)
                        <tr class="border-b hover:bg-gray-100 cursor-pointer" onclick="window.location='{{ url('/pets/' . $appointment->pet->id . '?tab=upcoming') }}';">
                            <td class="p-4">{{ $appointment->pet->name }}</td>
                            <td class="p-4">{{ $appointment->pet->owner->full_name }}</td>
                            <td class="p-4">{{ $appointment->pet->owner->email }}</td>
                            <td class="p-4">{{ $appointment->pet->owner->phone_number }}</td>
                            <td class="p-4">{{ $appointment->title }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500">No appointments scheduled for today.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $todaysAppointmentsList->links() }}
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
<!-- 
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4">Today's Clinic Activity</h2>
            <div class="max-h-[600px] overflow-y-auto pr-4">
                <div class="flow-root">
                    <ul role="list" class="-mb-4">
                        @forelse ($todaysActivities as $activity)
                            <li>
                                <div class="relative pb-4">
                                    @if (!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex items-start space-x-3">
                                        <div>
                                            <div class="relative px-1">
                                                <div class="h-8 w-8 bg-gray-100 rounded-full ring-8 ring-white flex items-center justify-center">
                                                    @if ($activity->type === 'new_pet')
                                                        <i class="fas fa-paw text-green-500"></i>
                                                    @elseif ($activity->type === 'new_diagnosis')
                                                        <i class="fas fa-notes-medical text-blue-500"></i>
                                                    @elseif ($activity->type === 'new_consent')
                                                        <i class="fas fa-file-signature text-yellow-500"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1 py-1.5">
                                            <div class="text-sm text-gray-500">
                                                @if ($activity->type === 'new_pet')
                                                    <strong class="font-medium text-gray-900">{{ $activity->model->owner->name }}</strong> registered a new pet:
                                                    <a href="{{ route('pets.show', $activity->model) }}" class="font-medium text-indigo-600 hover:underline">{{ $activity->model->name }}</a>
                                                @elseif ($activity->type === 'new_diagnosis')
                                                    A <a href="{{ route('diagnoses.show', $activity->model) }}" class="font-medium text-indigo-600 hover:underline">medical record</a> was created for
                                                    <a href="{{ route('pets.show', $activity->model->pet) }}" class="font-medium text-indigo-600 hover:underline">{{ $activity->model->pet->name }}</a>.
                                                @elseif ($activity->type === 'new_consent')
                                                    <strong class="font-medium text-gray-900">{{ $activity->model->pet->owner->name }}</strong> signed a "{{ ucfirst($activity->model->consent_type) }} Consent" form for
                                                    <a href="{{ route('pets.show', ['pet' => $activity->model->pet, 'tab' => 'consent']) }}" class="font-medium text-indigo-600 hover:underline">{{ $activity->model->pet->name }}</a>.
                                                @endif
                                                <span class="whitespace-nowrap text-xs">{{ $activity->timestamp->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-gray-500">No new activity today.</p>
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div> -->

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4">Today's Visiting Clients</h2>
             {{-- This wrapper makes the list scrollable --}}
    <div class="max-h-[600px] overflow-y-auto border rounded-lg p-2">
        <ul role="list" class="divide-y divide-gray-200">
            @forelse ($uniqueActiveOwners as $owner)
                <li class="py-3 px-2">
                    <a href="{{ route('owners.show', $owner) }}" class="flex items-center space-x-3 group">
                        <div class="h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-gray-500"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-900 group-hover:text-indigo-600">
                            {{-- This will now display in "Last Name, First Name" format --}}
                            <span class="uppercase">{{ $owner->last_name }}</span>, {{ $owner->first_name }}
                        </span>
                    </a>
                </li>
            @empty
                <li class="py-3 text-center text-gray-500">
                    No clients have visited yet today.
                </li>
            @endforelse
        </ul>
    </div>
</div>
     @endsection