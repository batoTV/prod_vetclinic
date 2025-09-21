<div class="flow-root">
    <ul role="list" class="-mb-8">
        @forelse ($pastAppointments as $appointment)
        <li>
            <div class="relative pb-8">
                @if (!$loop->last)
                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                @endif
                <div class="relative flex space-x-3">
                    <div>
                        <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                            <i class="fas fa-history text-white"></i>
                        </span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1.5">
                        <div>
                            <p class="text-sm text-gray-500">Appointment on <time>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</time></p>
                            <p class="font-medium text-gray-900">{{ $appointment->title }}</p>
                            <p class="mt-2 text-sm text-gray-700">{{ $appointment->description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        @empty
        <li>
            <p class="text-gray-500">No past appointments found for this pet.</p>
        </li>
        @endforelse
    </ul>
</div>