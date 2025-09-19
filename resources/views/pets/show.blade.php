@extends('layouts.app')

@section('title', 'Pet Profile')

@section('content')
    {{-- Main Details Header with Action Buttons --}}
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center">
            <img src="https://placehold.co/100x100/E2E8F0/4A5568?text={{ substr($pet->name, 0, 1) }}" alt="Pet Photo" class="w-24 h-24 rounded-full mr-6">
            <div>
                <h2 class="text-4xl font-bold">{{ $pet->name }}</h2>
                <p class="text-gray-600">{{ $pet->breed }}</p>
            </div>
        </div>
        {{-- New Action Buttons --}}
        <div class="flex items-center">
            <a href="{{ url('/pets/' . $pet->id . '/diagnoses/create') }}" class="bg-green-600 text-white h-12 w-12 rounded-full shadow-lg hover:bg-green-700 flex items-center justify-center transition-transform transform hover:scale-110" title="Add Medical Record">
                <i class="fas fa-notes-medical fa-lg"></i>
            </a>
            <a href="{{ url('/appointments/create?pet_id=' . $pet->id) }}" class="bg-red-500 text-white h-12 w-12 rounded-full shadow-lg hover:bg-red-600 flex items-center justify-center transition-transform transform hover:scale-110 ml-4" title="New Appointment for {{ $pet->name }}">
                <i class="fas fa-calendar-plus fa-lg"></i>
            </a>
        </div>
    </div>

    {{-- Two-Column Layout for Details --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        {{-- Left Column: Pet, Vitals & Allergies --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-bold mb-4 border-b pb-2">Pet Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <p><span class="font-semibold">Species:</span> {{ $pet->species }}</p>
                <p><span class="font-semibold">Gender:</span> {{ $pet->gender }}</p>
                <p><span class="font-semibold">Markings/Color:</span> {{ $pet->markings ?: 'N/A' }}</p>
                <p><span class="font-semibold">Birth Date:</span> {{ \Carbon\Carbon::parse($pet->birth_date)->format('M d, Y') }}</p>
                @php
                    $birthDate = \Carbon\Carbon::parse($pet->birth_date);
                    $ageInYears = $birthDate->age;
                    $ageString = '';
                    if ($ageInYears >= 1) {
                        $ageString = $ageInYears . ' ' . \Illuminate\Support\Str::plural('year', $ageInYears) . ' old';
                    } else {
                        $ageInMonths = (int) $birthDate->diffInMonths(now());
                        $ageString = $ageInMonths . ' ' . \Illuminate\Support\Str::plural('month', $ageInMonths) . ' old';
                    }
                @endphp
                <p><span class="font-semibold">Age:</span> {{ $ageString }}</p>
                <p><span class="font-semibold">Latest Weight:</span> {{ $latestDiagnosis && $latestDiagnosis->weight ? $latestDiagnosis->weight . ' kg' : 'N/A' }}</p>
                <p><span class="font-semibold">Latest Temp:</span> {{ $latestDiagnosis && $latestDiagnosis->temperature ? $latestDiagnosis->temperature . ' °C' : 'N/A' }}</p>
                
            </div>

            @if($pet->allergies)
            <div class="mt-4 pt-4 border-t">
                <h4 class="font-bold text-yellow-800">Allergy Notes</h4>
                <p class="mt-1 text-sm text-gray-700">{{ $pet->allergies }}</p>
            </div>
            @endif
        </div>

        {{-- Right Column: Owner --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-bold mb-4 border-b pb-2">Owner Information</h3>
            <div class="space-y-3 text-gray-700">
                <div>
                    <p class="font-semibold">Name:</p>
                    <p>{{ $pet->owner->name }}</p>
                </div>
                <div>
                    <p class="font-semibold">Email:</p>
                    <p>{{ $pet->owner->email }}</p>
                </div>
                <div>
                    <p class="font-semibold">Phone:</p>
                    <p>{{ $pet->owner->phone_number }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs for History and Appointments -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="#" id="tab-medical" class="tab-link border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Medical History
                </a>
                <a href="#" id="tab-upcoming" class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Upcoming Appointments
                </a>
                <a href="#" id="tab-history" class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Appointment History
                </a>
                <a href="#" id="tab-consent" class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Consent Forms
                </a>
            </nav>
        </div>

        <!-- Medical History Content -->
        <div id="content-medical" class="tab-content mt-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-4 font-semibold">Check-up Date</th>
                            <th class="p-4 font-semibold">Diagnosis</th>
                            <th class="p-4 font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($diagnoses as $diagnosis)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-4">{{ \Carbon\Carbon::parse($diagnosis->checkup_date)->format('M d, Y') }}</td>
                                <td class="p-4">{{ $diagnosis->diagnosis }}</td>
                                <td class="p-4 text-center">
					
                                    <a href="{{ url('/diagnoses/'.$diagnosis->id) }}" class="text-indigo-600 hover:text-indigo-800 mr-2" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ url('/diagnoses/' . $diagnosis->id . '/edit') }}" class="text-green-600 hover:text-green-800 mr-2" title="Edit Record">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <button type="button" class="text-red-600 hover:text-red-800 delete-button" data-url="{{ url('/diagnoses/'.$diagnosis->id) }}" title="Delete Record">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-4 text-center text-gray-500">No medical history found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $diagnoses->links() }}
            </div>
        </div>
        
        <!-- Upcoming Appointments Content -->
        <div id="content-upcoming" class="tab-content mt-6 hidden">
             <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @forelse ($upcomingAppointments as $appointment)
                    <li>
                        <div class="relative pb-8">
                            @if (!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0-5 bg-gray-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                        <i class="fas fa-calendar-check text-white"></i>
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
                        <p class="text-gray-500">No upcoming appointments found for this pet.</p>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Past Appointments Content -->
        <div id="content-history" class="tab-content mt-6 hidden">
             <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @forelse ($pastAppointments as $appointment)
                    <li>
                         <div class="relative pb-8">
                            @if (!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0-5 bg-gray-200" aria-hidden="true"></span>
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
        </div>
    
   <!-- <div id="content-consent" class="tab-content mt-6 hidden"> -->
    <!-- <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold mb-4">Record Owner's Consent</h3>
        
        <form id="consentForm" action="{{ route('consents.store', $pet->id) }}" method="POST">
            @csrf
            <div x-data="{ consentType: '' }">
                
                {{-- Add the select block here --}}
                <div class="mb-6">
                    <label for="consent_type" class="block font-medium text-sm text-gray-700">Type of Consent</label>
                    <select name="consent_type" id="consent_type" x-model="consentType" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select a form...</option>
                        <option value="general">General Consent</option>
                        <option value="surgery">Surgery/Hospitalization Consent</option>
                        {{-- You can add Grooming and Non-Consent later --}}
                    </select>
                </div>

                {{-- The rest of the form is now hidden until a type is selected --}}
                <div x-show="consentType" x-transition class="space-y-4">
                    
                    {{-- The consent text and signature pad --}}
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Owner's Signature</label>
                        <p class="text-xs text-gray-500 mb-1">Please have the owner review the terms and sign below.</p>
                        <div class="mt-1 border border-gray-300 rounded-md">
                            <canvas id="signature-pad" class="w-full h-48"></canvas>
                        </div>
                        <button type="button" id="clear-button" class="mt-2 text-sm text-indigo-600 hover:underline">Clear Signature</button>
                    </div>
                    
                    <input type="hidden" name="signature" id="signature-input">

                    <div>
                        <label for="notes" class="block font-medium text-sm text-gray-700">Notes for this Visit (Optional)</label>
                        <textarea name="notes" id="notes" rows="3" class="w-full mt-1 border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700">Save Consent</button>
                    </div>

                </div>
            </div>
        </form>
    </div> -->

        <!-- <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('signature-pad');
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)'
            });

            const clearButton = document.getElementById('clear-button');
            clearButton.addEventListener('click', function () {
                signaturePad.clear();
            });

            const form = document.getElementById('consentForm');
            const signatureInput = document.getElementById('signature-input');
            form.addEventListener('submit', function (event) {
                if (signaturePad.isEmpty()) {
                    alert("Please provide a signature.");
                    event.preventDefault();
                } else {
                    signatureInput.value = signaturePad.toDataURL('image/png');
                }
            });
        });
        </script> -->

        <div id="content-consent" class="tab-content mt-6 hidden">
        <h3 class="text-xl font-semibold mb-4">Consent History</h3>
        
        <div class="space-y-4">
            @forelse ($consents as $consent)
                <div class="border-b pb-2 mb-2">
                    <p class="text-sm text-gray-600">Consent given on: {{ $consent->created_at->format('M d, Y \a\t h:i A') }}</p>
                    @if ($consent->file_path)
                        <a href="{{ route('consents.download', $consent) }}" target="_blank" class="text-indigo-600 hover:underline">
                            View Signed {{ ucfirst($consent->consent_type) }} Consent PDF 
                        </a>
                    @endif
                    @if ($consent->notes)
                        <p class="mt-2 text-gray-800 bg-gray-50 p-2 rounded">{{ $consent->notes }}</p>
                    @endif
                </div>
            @empty
                <p class="text-gray-500">No consent records found for this pet.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $consents->links() }}
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.tab-link');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function(event) {
                event.preventDefault();

                // Deactivate all tabs and hide all content
                tabs.forEach(t => {
                    t.classList.remove('border-indigo-500', 'text-indigo-600');
                    t.classList.add('border-transparent', 'text-gray-500');
                });
                contents.forEach(c => c.classList.add('hidden'));

                // Activate the clicked tab
                this.classList.add('border-indigo-500', 'text-indigo-600');
                this.classList.remove('border-transparent', 'text-gray-500');

                // Show the corresponding content
                const contentId = this.id.replace('tab-', 'content-');
                document.getElementById(contentId).classList.remove('hidden');
            });
        });

        // Auto-select tab from URL
        const urlParams = new URLSearchParams(window.location.search);
        const tabToOpen = urlParams.get('tab');

        if (tabToOpen === 'upcoming') {
            const upcomingTab = document.getElementById('tab-upcoming');
            if (upcomingTab) {
                upcomingTab.click();
            }
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Tab Switching Logic ---
    const tabs = document.querySelectorAll('.tab-link');
    const contents = document.querySelectorAll('.tab-content');

    // --- Signature Pad Logic ---
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)'
    });

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear(); 
    }

    // This handles resizing if the browser window changes size
    window.addEventListener("resize", resizeCanvas);

    // --- Combined Tab Click Handler ---
    tabs.forEach(tab => {
        tab.addEventListener('click', function(event) {
            event.preventDefault();

            // Deactivate all tabs and hide content
            tabs.forEach(t => {
                t.classList.remove('border-indigo-500', 'text-indigo-600');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            contents.forEach(c => c.classList.add('hidden'));

            // Activate the clicked tab
            this.classList.add('border-indigo-500', 'text-indigo-600');
            this.classList.remove('border-transparent', 'text-gray-500');

            // Show the corresponding content
            const contentId = this.id.replace('tab-', 'content-');
            document.getElementById(contentId).classList.remove('hidden');

            // --- THE FIX ---
            // If the consent tab was just opened, resize the canvas now that it's visible.
            if (this.id === 'tab-consent') {
                resizeCanvas();
            }
        });
    });

    // --- Signature Pad Form Logic ---
    const clearButton = document.getElementById('clear-button');
    clearButton.addEventListener('click', function () {
        signaturePad.clear();
    });

    const form = document.getElementById('consentForm');
    const signatureInput = document.getElementById('signature-input');
    form.addEventListener('submit', function (event) {
        if (signaturePad.isEmpty()) {
            alert("Please provide a signature.");
            event.preventDefault();
        } else {
            signatureInput.value = signaturePad.toDataURL('image/png');
        }
    });
    
    // --- Auto-select tab from URL logic (from your file) ---
    const urlParams = new URLSearchParams(window.location.search);
    const tabToOpen = urlParams.get('tab');
    if (tabToOpen === 'upcoming') {
        document.getElementById('tab-upcoming')?.click();
    }
});
</script>
@endsection
