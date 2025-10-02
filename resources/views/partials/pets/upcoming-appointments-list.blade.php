<div class="overflow-x-auto">
    <table class="w-full text-left">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-4 font-semibold">Appointment Date</th>
                <th class="p-4 font-semibold">Title</th>
                <th class="p-4 font-semibold">Linked Medical Record</th> {{-- New Column --}}
                <th class="p-4 font-semibold text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($upcomingAppointments as $appointment)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-4">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</td>
                    <td class="p-4">{{ $appointment->title }}</td>
                    <td class="p-4">
                        {{-- New Cell: Check if a diagnosis is linked --}}
                        @if ($appointment->diagnosis)
                            <a href="{{ route('diagnoses.show', $appointment->diagnosis) }}" class="text-indigo-600 hover:underline">
                                Medical Record from {{ $appointment->diagnosis->checkup_date->format('M d, Y') }}
                            </a>
                        @else
                            <span class="text-gray-400">N/A</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center items-center space-x-4">
                             {{-- Edit Link --}}
        <a href="{{ route('appointments.edit', $appointment->id) }}" class="text-green-600 hover:text-green-800 mr-2">
            <i class="fas fa-pen"></i>
        </a>
        
        {{-- Delete Button using the generic class --}}
        <button type="button" class="text-red-600 hover:text-red-800 delete-button" 
                data-url="{{ route('appointments.destroy', $appointment->id) }}" 
                title="Cancel Appointment">
            <i class="fas fa-trash"></i>
        </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">No upcoming appointments scheduled.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($upcomingAppointments->hasPages())
    <div class="mt-6">
        {{ $upcomingAppointments->links() }}
    </div>
@endif