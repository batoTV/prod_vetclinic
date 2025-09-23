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
        {{-- Action Buttons --}}
        <div class="flex items-center">
            {{-- BEST PRACTICE: Use named routes instead of url() --}}
            <a href="{{ route('diagnoses.create', ['pet' => $pet->id]) }}" class="bg-green-600 text-white h-12 w-12 rounded-full shadow-lg hover:bg-green-700 flex items-center justify-center transition-transform transform hover:scale-110" title="Add Medical Record">
                <i class="fas fa-notes-medical fa-lg"></i>
            </a>
            <a href="{{ route('appointments.create', ['pet' => $pet->id]) }}" class="bg-red-500 text-white h-12 w-12 rounded-full shadow-lg hover:bg-red-600 flex items-center justify-center transition-transform transform hover:scale-110 ml-4" title="New Appointment for {{ $pet->name }}">
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
                    // SUGGESTION: This logic is perfect for a Model Accessor. See notes below.
                    $birthDate = \Carbon\Carbon::parse($pet->birth_date);
                    $ageInYears = $birthDate->age;
                    if ($ageInYears >= 1) {
                        $ageString = $ageInYears . ' ' . \Illuminate\Support\Str::plural('year', $ageInYears) . ' old';
                    } else {
                        $ageInMonths = (int) $birthDate->diffInMonths(now());
                        $ageString = $ageInMonths . ' ' . \Illuminate\Support\Str::plural('month', $ageInMonths) . ' old';
                    }
                @endphp
                <p><span class="font-semibold">Age:</span> {{ $pet->age }}</p>
                <p><span class="font-semibold">Latest Weight:</span> {{ $latestDiagnosis && $latestDiagnosis->weight ? $latestDiagnosis->weight . ' kg' : 'N/A' }}</p>
                <p><span class="font-semibold">Latest Temp:</span> {{ $latestDiagnosis && $latestDiagnosis->temperature ? $latestDiagnosis->temperature . ' °C' : 'N/A' }}</p>
            </div>

            @if($pet->allergies)
            <div class="mt-4 pt-4 border-t">
                <h4 class="font-bold text-yellow-800">Allergy | Health Notes</h4>
                <p class="mt-1 text-sm text-gray-700">{{ $pet->allergies }}</p>
            </div>
            @endif
        </div>

        {{-- Right Column: Owner --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-bold mb-4 border-b pb-2">Owner Information</h3>
            <div class="space-y-3 text-gray-700">
                <div><p class="font-semibold">Name:</p><p>{{ $pet->owner->name }}</p></div>
                <div><p class="font-semibold">Email:</p><p>{{ $pet->owner->email }}</p></div>
                <div><p class="font-semibold">Phone:</p><p>{{ $pet->owner->phone_number }}</p></div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                <a href="#" id="tab-medical" class="tab-link border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Medical History</a>
                <a href="#" id="tab-upcoming" class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Upcoming Appointments</a>
                <a href="#" id="tab-history" class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Appointment History</a>
                <a href="#" id="tab-consent" class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Consent Forms</a>
            </nav>
        </div>

        <div id="content-medical" class="tab-content mt-6">
            @include('partials.pets.diagnoses-table', ['diagnoses' => $diagnoses])
        </div>
        
        <div id="content-upcoming" class="tab-content mt-6 hidden">
             @include('partials.pets.upcoming-appointments-list', ['upcomingAppointments' => $upcomingAppointments])
        </div>

        <div id="content-history" class="tab-content mt-6 hidden">
            @include('partials.pets.past-appointments-list', ['pastAppointments' => $pastAppointments])
        </div>
    
        <div id="content-consent" class="tab-content mt-6 hidden">
            @include('partials.pets.consent-section', ['consents' => $consents, 'pet' => $pet])
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Setup ---
    const tabs = document.querySelectorAll('.tab-link');
    const contents = document.querySelectorAll('.tab-content');
    const canvas = document.getElementById('signature-pad');
    let signaturePad; // Initialize lazily

    // --- Functions ---
    const resizeCanvas = () => {
        if (!canvas) return;
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        if (signaturePad) {
            signaturePad.clear();
        } else {
             signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });
        }
    };

    const activateTab = (tabEl) => {
        // Deactivate all tabs and hide content
        tabs.forEach(t => {
            t.classList.remove('border-indigo-500', 'text-indigo-600');
            t.classList.add('border-transparent', 'text-gray-500');
        });
        contents.forEach(c => c.classList.add('hidden'));

        // Activate the clicked tab
        tabEl.classList.add('border-indigo-500', 'text-indigo-600');
        tabEl.classList.remove('border-transparent', 'text-gray-500');

        // Show corresponding content
        const contentId = tabEl.id.replace('tab-', 'content-');
        document.getElementById(contentId)?.classList.remove('hidden');

        // If the consent tab was just opened, resize the canvas now that it's visible.
        if (tabEl.id === 'tab-consent') {
            resizeCanvas();
        }
    };

    // --- Event Listeners ---
    tabs.forEach(tab => {
        tab.addEventListener('click', function(event) {
            event.preventDefault();
            activateTab(this);
        });
    });

    window.addEventListener("resize", resizeCanvas);

    // Signature Pad Form Logic
    const consentForm = document.getElementById('consentForm');
    if (consentForm) {
        const clearButton = document.getElementById('clear-button');
        const signatureInput = document.getElementById('signature-input');

        clearButton.addEventListener('click', () => signaturePad.clear());

        consentForm.addEventListener('submit', function (event) {
            if (signaturePad && signaturePad.isEmpty()) {
                alert("Please provide a signature.");
                event.preventDefault();
            } else if (signaturePad) {
                signatureInput.value = signaturePad.toDataURL('image/png');
            }
        });
    }

    // --- Initial State Logic (Auto-select tab from URL) ---
    const urlParams = new URLSearchParams(window.location.search);
    const tabFromUrl = urlParams.get('tab');
    
    // Default to 'medical' if no tab is in the URL, otherwise use the one from the URL.
    const tabToOpenId = `tab-${tabFromUrl || 'medical'}`;
    const tabToOpen = document.getElementById(tabToOpenId);

    if (tabToOpen) {
        activateTab(tabToOpen);
    }
});
</script>
@endpush