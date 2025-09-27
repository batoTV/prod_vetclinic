@extends('layouts.guest')

@section('content')
   @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    @endpush

    <script>
        function clientRegisterData(oldPets,errors) {
            return {
                // --- DATA PROPERTIES ---
                
                clientStatus: @json(old('client_status', 'new')),
                errors: errors || {},
                numberOfPets: (oldPets && oldPets.length > 0) ? oldPets.length : 1,
                pets: (oldPets && oldPets.length > 0) ? oldPets : [ { name: '', species: '', breed: '', birth_date: '', gender: 'Male', allergies: '', markings: '', chief_complaint: '' } ],
                findName: '',
                findPhone: '',
                foundOwner: null,
                searchMessage: '',
                isLoading: false,
                findErrors: {},
                ownerPets: [],
                selectedPetId: null,
                selectedConsentType: '',
                consentNotes: '',
                consentModalOpen: false,
                hasAgreedToTerms: false,
                signaturePad: null,
                 
                

                // --- METHODS ---
                createEmptyPet() {
                    return { name: '', species: '', breed: '', birth_date: '', gender: 'Male', allergies: '', markings: '', chief_complaint: '' };
                },
                addPet() { 
                    this.pets.push(this.createEmptyPet()); 
                    this.numberOfPets = this.pets.length;
                },
                removePet(index) { 
                    if (this.pets.length > 1) {
                        this.pets.splice(index, 1); 
                        this.numberOfPets = this.pets.length; 
                    }
                },
                updatePetCount() {
                    const count = parseInt(this.numberOfPets) || 1;
                    if (count < 1) { this.numberOfPets = 1; }
                    if (count > 10) { this.numberOfPets = 10; }

                    const currentCount = this.pets.length;
                    if (count > currentCount) { 
                        for (let i = currentCount; i < count; i++) this.pets.push(this.createEmptyPet());
                    } else if (count < currentCount) { 
                        this.pets.splice(count); 
                    }
                },
                findOwner() {
                    this.isLoading = true; this.searchMessage = ''; this.findErrors = {};
                    if (!this.findName.trim() && !this.findPhone.trim()) {
                        this.findErrors.general = 'Please enter a name or a phone number to search.';
                        this.isLoading = false; return;
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    fetch('{{ route("client.find") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ name: this.findName, phone_number: this.findPhone })
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.searchMessage = data.message;
                        if (data.success) {
                            this.foundOwner = data.owner;
                           fetch(`{{ url('/') }}/owners/${this.foundOwner.id}/pets`)
                                .then(res => res.json())
                                .then(petData => { this.ownerPets = petData; });
                        } else { 
                            this.foundOwner = null; 
                            this.ownerPets = [];
                        }
                    })
                    .catch(() => { this.searchMessage = 'An error occurred during the search. Please try again.'; })
                    .finally(() => { this.isLoading = false; });
                },
                
                // --- SIGNATURE PAD & CONSENT FORM LOGIC ---
                submitConsentForm(event) {
                    if (!this.signaturePad || this.signaturePad.isEmpty()) {
                        alert("Please provide a signature before saving.");
                        return;
                    }
                    if (!this.hasAgreedToTerms) {
                        alert("Please read and agree to the consent terms before saving.");
                        return;
                    }
                    document.getElementById('signature-input').value = this.signaturePad.toDataURL('image/png');
                    event.target.submit();
                },
                initSignaturePad() {
                    const canvas = document.getElementById('signature-pad');
                    if (!canvas) return;
                    this.signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(249, 250, 251)' });
                    this.resizeCanvas();

                    document.getElementById('clear-button').addEventListener('click', () => {
                        if (this.signaturePad) this.signaturePad.clear();
                    });
                },
                resizeCanvas() {
                    const canvas = document.getElementById('signature-pad');
                    if (!canvas || !this.signaturePad) return;
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                    this.signaturePad.fromData(this.signaturePad.toData());
                },
                whenVisible(elementId, callback) {
                    const observer = new IntersectionObserver((entries, obs) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                callback();
                                obs.disconnect();
                            }
                        });
                    });
                    const el = document.getElementById(elementId);
                    if (el) observer.observe(el);
                },
                
                // --- MAIN INITIALIZATION & WATCHERS ---
                init() {
    // Watch for changes in the main client status radio buttons
    this.$watch('clientStatus', () => {
        // This block now runs on EVERY radio button change, resetting everything.
        this.foundOwner = null;
        this.searchMessage = '';
        this.ownerPets = [];
        this.selectedPetId = null;
        this.selectedConsentType = '';
        this.hasAgreedToTerms = false;
        this.findName = '';
        this.findPhone = '';
        this.consentNotes = '';
        if (this.signaturePad) this.signaturePad.clear();
    });

    // Watch for changes in the consent type dropdown (this logic remains)
    this.$watch('selectedConsentType', (value) => {
        this.selectedPetId = null;
        this.hasAgreedToTerms = false;
        this.consentNotes = '';
        if (this.signaturePad) this.signaturePad.clear();
        
        if (value) {
            this.$nextTick(() => {
                this.whenVisible('signature-pad', () => this.initSignaturePad());
            });
        }
    });
    
    window.addEventListener("resize", () => { if (this.signaturePad) this.resizeCanvas(); });
}
            }
        }
    </script>
    <div x-data="clientRegisterData(@js(old('pets')), @js($errors->toArray()))" x-init="init()">
        
        {{-- Section 1: Client Status Selection --}}
        <div class="mb-6 p-6 bg-white rounded-lg shadow-md">
            <h2 class="text-xl font-bold text-gray-800 mb-2">Client Status</h2>
            <div class="flex flex-wrap items-center gap-x-8 gap-y-4">
                <label class="flex items-center cursor-pointer mr-6"><input type="radio" value="new" x-model="clientStatus" class="form-radio h-5 w-5 text-indigo-600"><span class="ml-2 text-gray-700">I am a new client</span></label>
                <label class="flex items-center cursor-pointer mr-6"><input type="radio" value="existing" x-model="clientStatus" class="form-radio h-5 w-5 text-indigo-600"><span class="ml-2 text-gray-700">I am an existing client</span></label>
                <label class="flex items-center cursor-pointer mr-6"><input type="radio" value="consent" x-model="clientStatus" class="form-radio h-5 w-5 text-indigo-600"><span class="ml-2 text-gray-700">Sign Consent Form</span></label>
            </div>
        </div>

        {{-- Section 2: Form for New & Existing Client Pet Registration --}}
        <form id="registrationForm" method="POST" action="{{ route('client.store') }}" x-show="clientStatus === 'new' || clientStatus === 'existing'" x-transition x-cloak>
            @csrf
            <input type="hidden" name="client_status" x-model="clientStatus">
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                {{-- Subsection: New Client Information --}}
                <div x-show="clientStatus === 'new'" x-transition>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Your Information</h2>
                    
                    {{-- All owner fields use `old()` to retain data after server-side validation errors --}}
                    <div><label for="name" class="block font-medium text-sm text-gray-700">Full Name</label><input id="name" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="text" name="name" value="{{ old('name') }}" required></div>
                    @error('name')<span class="text-red-600 text-sm mt-1">{{ $message }}</span>@enderror
                    
                    <div class="mt-4"><label for="email" class="block font-medium text-sm text-gray-700">Email</label><input id="email" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="email" name="email" value="{{ old('email') }}" required></div>
                    @error('email')<span class="text-red-600 text-sm mt-1">{{ $message }}</span>@enderror
                    
                    <div class="mt-4"><label for="phone_number" class="block font-medium text-sm text-gray-700">Phone Number</label><input id="phone_number" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="tel" name="phone_number" value="{{ old('phone_number') }}" required maxlength="11" pattern="[0-9]{11}" inputmode="numeric" title="Phone number must be 11 digits."></div>
                    @error('phone_number')<span class="text-red-600 text-sm mt-1">{{ $message }}</span>@enderror
                    
                    <div class="mt-4"><label for="address" class="block font-medium text-sm text-gray-700">Address</label><input id="address" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="text" name="address" value="{{ old('address') }}" required></div>
                    @error('address')<span class="text-red-600 text-sm mt-1">{{ $message }}</span>@enderror
                </div>
                
                {{-- Subsection: Existing Client Search --}}
                <div x-show="clientStatus === 'existing'" x-transition>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Find Your Record</h2>
                    <div><label for="find_name" class="block font-medium text-sm text-gray-700">Full Name</label><input id="find_name" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="text" x-model.debounce.300ms="findName"></div>
                    <div class="mt-4"><label for="find_phone" class="block font-medium text-sm text-gray-700">Phone Number</label><input id="find_phone" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="tel" x-model.debounce.300ms="findPhone"></div>
                    <div x-show="findErrors.general" x-text="findErrors.general" class="mt-4 p-3 bg-red-100 text-red-700 rounded-md"></div>
                    <div class="mt-4"><button type="button" @click="findOwner()" :disabled="isLoading" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 disabled:opacity-50"><span x-text="isLoading ? 'Searching...' : 'Find Me'"></span></button></div>
                    <div x-show="searchMessage" x-text="searchMessage" class="mt-4 p-4 rounded-md" :class="foundOwner ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"></div>
                    <input type="hidden" name="owner_id" x-bind:value="foundOwner ? foundOwner.id : ''">
                </div>

                {{-- Subsection: Pet Information (Dynamic) --}}
                <div class="mt-8 pt-6 border-t" x-show="clientStatus === 'new' || (clientStatus === 'existing' && foundOwner)">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Pet Information</h2>
        <div class="flex items-center space-x-2">
            <label for="numberOfPets" class="block font-medium text-sm text-gray-700">Number of Pets:</label>
            <input id="numberOfPets" type="number" class="w-20 border-gray-300 rounded-md shadow-sm" x-model.number="numberOfPets" @input.debounce="updatePetCount()" min="1" max="10"/>
        </div>
    </div>
    <template x-for="(pet, index) in pets" :key="index">
        <div class="border p-4 rounded-md mt-4">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-lg mb-2" x-text="'Pet ' + (index + 1)"></h3>
                <button type="button" x-show="pets.length > 1" @click="removePet(index)" class="text-red-500 hover:text-red-700 font-bold">&times; Remove</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label :for="'pet_name_' + index" class="block font-medium text-sm text-gray-700">Pet's Name</label>
                    <input :id="'pet_name_' + index" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="text" :name="'pets[' + index + '][name]'" x-model="pet.name" required>
                    {{-- Error Message --}}
                    <div x-show="errors['pets.' + index + '.name']" x-text="errors['pets.' + index + '.name']" class="text-red-600 text-sm mt-1"></div>
                </div>

                <div>
                    <label :for="'pet_species_' + index" class="block font-medium text-sm text-gray-700">Species</label>
                    <input :id="'pet_species_' + index" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="text" :name="'pets[' + index + '][species]'" x-model="pet.species" required>
                    {{-- Error Message --}}
                    <div x-show="errors['pets.' + index + '.species']" x-text="errors['pets.' + index + '.species']" class="text-red-600 text-sm mt-1"></div>
                </div>

                <div>
                    <label :for="'pet_breed_' + index" class="block font-medium text-sm text-gray-700">Breed</label>
                    <input :id="'pet_breed_' + index" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="text" :name="'pets[' + index + '][breed]'" x-model="pet.breed">
                    {{-- Error Message --}}
                    <div x-show="errors['pets.' + index + '.breed']" x-text="errors['pets.' + index + '.breed']" class="text-red-600 text-sm mt-1"></div>
                </div>

                <div>
                    <label :for="'pet_birth_date_' + index" class="block font-medium text-sm text-gray-700">Birth Date</label>
                    <input :id="'pet_birth_date_' + index" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="date" :name="'pets[' + index + '][birth_date]'" x-model="pet.birth_date" required>
                    {{-- Error Message --}}
                    <div x-show="errors['pets.' + index + '.birth_date']" x-text="errors['pets.' + index + '.birth_date']" class="text-red-600 text-sm mt-1"></div>
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label :for="'pet_gender_' + index" class="block font-medium text-sm text-gray-700">Gender</label>
                    <select :id="'pet_gender_' + index" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" :name="'pets[' + index + '][gender]'" x-model="pet.gender">
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                    {{-- Error Message --}}
                    <div x-show="errors['pets.' + index + '.gender']" x-text="errors['pets.' + index + '.gender']" class="text-red-600 text-sm mt-1"></div>
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label :for="'pet_markings_' + index" class="block font-medium text-sm text-gray-700">Markings / Color</label>
                    <input :id="'pet_markings_' + index" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="text" :name="'pets[' + index + '][markings]'" x-model="pet.markings">
                     {{-- Error Message --}}
                    <div x-show="errors['pets.' + index + '.markings']" x-text="errors['pets.' + index + '.markings']" class="text-red-600 text-sm mt-1"></div>
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label :for="'pet_allergies_' + index" class="block font-medium text-sm text-gray-700">Medical Notes / Allergies</label>
                    <textarea :id="'pet_allergies_' + index" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" :name="'pets[' + index + '][allergies]'" x-model="pet.allergies"></textarea>
                    {{-- Error Message --}}
                    <div x-show="errors['pets.' + index + '.allergies']" x-text="errors['pets.' + index + '.allergies']" class="text-red-600 text-sm mt-1"></div>
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label :for="'pet_chief_complaint_' + index" class="block font-medium text-sm text-gray-700">Reason for Visit (Chief Complaint)</label>
                    <textarea :id="'pet_chief_complaint_' + index" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" :name="'pets[' + index + '][chief_complaint]'" x-model="pet.chief_complaint"></textarea>
                     {{-- Error Message --}}
                    <div x-show="errors['pets.' + index + '.chief_complaint']" x-text="errors['pets.' + index + '.chief_complaint']" class="text-red-600 text-sm mt-1"></div>
                </div>

            </div>
        </div>
    </template>
</div>
                <div class="flex items-center justify-end mt-8" x-show="clientStatus === 'new' || (clientStatus === 'existing' && foundOwner)"><button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">Register</button></div>
            </div>
        </form>

        {{-- Section 3: Consent Form --}}
        <div x-show="clientStatus === 'consent'" x-transition x-cloak>
            <div class="bg-white p-6 rounded-lg shadow-md">
                
                <h2 class="text-xl font-bold text-gray-800 mb-4">Select Consent Type</h2>
                <div>
                    <label for="consent_type_register" class="block font-medium text-sm text-gray-700">Type of Consent</label>
                    <select id="consent_type_register" x-model="selectedConsentType" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                        <option value="">Select a form...</option>
                        <option value="general">General Consent</option>
                        <option value="surgery">Surgery/Hospitalization Consent</option>
                        <option value="non">Non-Consent Form</option>
                    </select>
                </div>

                {{-- This section appears after a consent type is chosen --}}
                <div x-show="selectedConsentType" x-transition class="mt-6 pt-6 border-t">
                    {{-- Step 1: Find the owner record (if not already found) --}}
                    <div x-show="!foundOwner">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Find Owner Record</h2>
                        <div><label for="find_name_consent" class="block font-medium text-sm text-gray-700">Full Name</label><input id="find_name_consent" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="text" x-model.debounce.300ms="findName"></div>
                        <div class="mt-4"><label for="find_phone_consent" class="block font-medium text-sm text-gray-700">Phone Number</label><input id="find_phone_consent" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="tel" x-model.debounce.300ms="findPhone"></div>
                        <div x-show="findErrors.general" x-text="findErrors.general" class="mt-4 p-3 bg-red-100 text-red-700 rounded-md"></div>
                        <div class="mt-4"><button type="button" @click="findOwner()" :disabled="isLoading" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 disabled:opacity-50"><span x-text="isLoading ? 'Searching...' : 'Find Record'"></span></button></div>
                    </div>
                    <div x-show="searchMessage" x-text="searchMessage" class="mt-4 p-4 rounded-md" :class="foundOwner ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"></div>
                    
                    {{-- Step 2: Once owner is found, show the actual consent form fields --}}
                    <div x-show="foundOwner" x-transition>
                        <form id="consentForm" method="POST" :action="selectedPetId ? `{{ url('/') }}/pets/${selectedPetId}/consents` : '#'" @submit.prevent="submitConsentForm">
                            @csrf
                            {{-- These hidden inputs will be populated by Alpine before submission --}}
                            <input type="hidden" name="signature" id="signature-input">
                            <input type="hidden" name="notes" x-model="consentNotes">
                            <input type="hidden" name="consent_type" x-model="selectedConsentType">

                            <div class="mt-4">
                                <label for="pet_id_select" class="block font-medium text-sm text-gray-700">Select Pet for Consent</label>
                                <select id="pet_id_select" name="pet_id" x-model="selectedPetId" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">Choose a pet...</option>
                                    <template x-for="pet in ownerPets" :key="pet.id">
                                        <option :value="pet.id" x-text="pet.name + ' (' + pet.species + ')'"></option>
                                    </template>
                                </select>
                            </div>

                            <div x-show="selectedPetId" x-transition class="mt-4">
                                {{-- For NON-CONSENT, the notes field appears first and is required --}}
                                <div x-show="selectedConsentType === 'non'" class="mb-4">
                                    <label for="notes" class="block font-medium text-sm text-gray-700">Procedure to Decline (Required)</label>
                                    <textarea x-model="consentNotes" id="notes" rows="3" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" :required="selectedConsentType === 'non'"></textarea>
                                </div>

                                {{-- The signature block appears after notes are filled for non-consent, or immediately for others --}}
                                <div x-show="selectedConsentType !== 'non' || (selectedConsentType === 'non' && consentNotes.trim() !== '')" x-transition>
                                    <div class="mb-4">
                                        <button type="button" @click="consentModalOpen = true" class="text-indigo-600 hover:underline font-semibold">
                                            Please Read and Agree to the Consent Terms before signing.
                                        </button>
                                    </div>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block font-medium text-sm text-gray-700">Owner's Signature</label>
                                            <div class="mt-1 border border-gray-300 rounded-md h-48 bg-gray-50">
                                                <canvas id="signature-pad" class="w-full h-full"></canvas>
                                            </div>
                                            <button type="button" id="clear-button" class="mt-2 text-sm text-indigo-600 hover:underline">Clear Signature</button>
                                        </div>
                                        {{-- For regular consent, the notes field is optional and appears here --}}
                                        <div x-show="selectedConsentType !== 'non'">
                                            <label for="notes_optional" class="block font-medium text-sm text-gray-700">Notes (Optional)</label>
                                            <textarea x-model="consentNotes" id="notes_optional" rows="3" class="w-full mt-1 border-gray-300 rounded-md shadow-sm"></textarea>
                                        </div>
                                        <div class="flex justify-end">
                                            <button type="submit" :disabled="!hasAgreedToTerms" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                                Save Consent
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4: Consent Terms Modal --}}
        <div x-show="consentModalOpen" x-transition.opacity x-cloak class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.away="consentModalOpen = false">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4" x-text="selectedConsentType === 'surgery' ? 'Surgery/Hospitalization Consent' : (selectedConsentType === 'non' ? 'Non-Consent Confirmation' : 'General Consent Form')"></h3>
                    <div class="prose max-w-none text-sm">
                        {{-- General Consent Text --}}
                        <div x-show="selectedConsentType === 'general'">
                            <p>I, the undersigned, certify that I am the owner or authorized agent for the pet named on this form. I hereby authorize the veterinarians and staff of Rosevet Animal Clinic to perform necessary examinations, vaccinations, medications, and treatments for this pet.</p>
                            <p>I authorize the veterinarians and their designated assistants to perform diagnostic procedures, administer treatments, and perform emergency surgical procedures as deemed necessary for the health of my pet based on examination findings and in accordance with their professional judgment.</p>
                            <p>I understand that all medical and surgical procedures carry inherent risks, which have been explained to me by the attending veterinarian.</p>
                            <p>I understand that all treatments will be performed with due care and in accordance with prevailing standards of veterinary medicine. I acknowledge that no guarantee has been made regarding the outcome of any treatment or procedure.</p>
                            <p>I agree to seek immediate veterinary care if lameness, inappetence, diarrhea, vomiting, or other adverse clinical signs are observed in my pet following any procedure. Having been informed of the potential risks and precautions, I agree to hold the veterinary staff harmless for such conditions.</p>
                            <p><b>FINANCIAL POLICY:</b> I understand that there are strictly NO RETURNS or REFUNDS for procedures that have been performed or for medicines that have been purchased.</p>
                        </div>
                        {{-- Surgery Consent Text --}}
                        <div x-show="selectedConsentType === 'surgery'">
                            <p>I am the owner or agent for the animal(s) described on this form and have the authority to execute this consent. I request that the veterinarians, agents, and employees of Rosevet Animal Clinic perform the services which are necessary to the examination, medication, and treatment of the animal specifically described and identified on this form.</p>
                            <p>I authorize the veterinarians on duty (and the assistants they designate) to examine the animal(s) and to administer medical treatment or emergency surgical treatment which is considered therapeutically and/or diagnostically necessary on the basis of the findings during the course of examination. Therefore, I hereby consent to and authorize the performance of such procedure(s) as are necessary and in the exercise of the veterinarian's professional judgment.</p>
                            <p>I further understand that any animal found to be infected with either external or internal parasites will be treated for sums at my expense.</p>
                            <p>I understand that the treatment of the patient will be conducted with due care and in accordance with the prevailing standards of competency in Veterinary Medicine. I certify that no guarantee or assurance has been made as to the result that may be obtained through the course of the treatment undertaken by the veterinarians, agents, or employees of Rosevet Animal Clinic.</p>
                            <p>I assume financial responsibility for all charges incurred to the patient for services rendered and understand the full payment is required upon discharge. In case of non-payment, I am aware that Rosevet Animal Clinic will charge the cost of collecting the debt on the amount owed for services. This includes the collections company's charges, attorney's fees and interest of 1.5% per month (18%) annum.</p>
                            <p>I understand that updates on my pet's condition while confined at Rosevet Animal Clinic will be provided at scheduled intervals.</p>
                            <p>I understand that a written estimate of charges is available with reasonable time at my request. I also consent to the release of medical information.</p>
                        </div>
                        {{-- Non-Consent Text --}}
                        <div x-show="selectedConsentType === 'non'">
                            <p>I am the owner/authorized person for the animal described on this form and I do not want my pet(s) to undergo the procedure(s) specified below.</p>
                            <p class="font-bold my-2 p-2 bg-gray-100 rounded">Procedure to Decline: <span x-text="consentNotes"></span></p>
                            <p>I understand the risks and take full responsibility for the possible outcome of this decision.</p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end"><button type="button" @click="hasAgreedToTerms = true; consentModalOpen = false" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700">I Understand and Agree</button></div>
                </div>
            </div>
        </div>
    </div>

    <!-- @push('scripts')
    {{-- This script is required for the signature pad functionality --}}
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    @ebd    
    <script>
        function clientRegisterData(oldPets) {
            return {
                // --- DATA PROPERTIES ---
                clientStatus: @json(old('client_status', 'new')),
                numberOfPets: (oldPets && oldPets.length > 0) ? oldPets.length : 1,
                pets: (oldPets && oldPets.length > 0) ? oldPets : [ { name: '', species: '', breed: '', birth_date: '', gender: 'Male', allergies: '', markings: '', chief_complaint: '' } ],
                
                // State for finding existing owners
                findName: '',
                findPhone: '',
                foundOwner: null,
                searchMessage: '',
                isLoading: false,
                findErrors: {},

                // State for consent form
                ownerPets: [],
                selectedPetId: null,
                selectedConsentType: '',
                consentNotes: '',
                consentModalOpen: false,
                hasAgreedToTerms: false,
                signaturePad: null,

                // --- METHODS ---
                createEmptyPet() {
                    return { name: '', species: '', breed: '', birth_date: '', gender: 'Male', allergies: '', markings: '', chief_complaint: '' };
                },
                addPet() { 
                    this.pets.push(this.createEmptyPet()); 
                    this.numberOfPets = this.pets.length;
                },
                removePet(index) { 
                    if (this.pets.length > 1) {
                        this.pets.splice(index, 1); 
                        this.numberOfPets = this.pets.length; 
                    }
                },
                updatePetCount() {
                    const count = parseInt(this.numberOfPets) || 1;
                    if (count < 1) { this.numberOfPets = 1; }
                    if (count > 10) { this.numberOfPets = 10; }

                    const currentCount = this.pets.length;
                    if (count > currentCount) { 
                        for (let i = currentCount; i < count; i++) this.pets.push(this.createEmptyPet());
                    } else if (count < currentCount) { 
                        this.pets.splice(count); 
                    }
                },
                findOwner() {
                    this.isLoading = true; this.searchMessage = ''; this.findErrors = {};
                    if (!this.findName.trim() && !this.findPhone.trim()) {
                        this.findErrors.general = 'Please enter a name or a phone number to search.';
                        this.isLoading = false; return;
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    fetch('{{ route("client.find") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ name: this.findName, phone_number: this.findPhone })
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.searchMessage = data.message;
                        if (data.success) {
                            this.foundOwner = data.owner;
                            // Fetch pets for this owner
                           fetch(`{{ url('/') }}/owners/${this.foundOwner.id}/pets`)
                                .then(res => res.json())
                                .then(petData => { this.ownerPets = petData; });
                        } else { 
                            this.foundOwner = null; 
                            this.ownerPets = [];
                        }
                    })
                    .catch(() => { this.searchMessage = 'An error occurred during the search. Please try again.'; })
                    .finally(() => { this.isLoading = false; });
                },
                
                // --- SIGNATURE PAD & CONSENT FORM LOGIC ---
                submitConsentForm(event) {
                    if (!this.signaturePad || this.signaturePad.isEmpty()) {
                        alert("Please provide a signature before saving.");
                        return; // Stop the submission
                    }
                    if (!this.hasAgreedToTerms) {
                        alert("Please read and agree to the consent terms before saving.");
                        return;
                    }
                    // All checks passed, populate hidden input and submit the form
                    document.getElementById('signature-input').value = this.signaturePad.toDataURL('image/png');
                    event.target.submit(); // Programmatically submit the form
                },
                initSignaturePad() {
                    const canvas = document.getElementById('signature-pad');
                    if (!canvas) return;
                    this.signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(249, 250, 251)' });
                    this.resizeCanvas();

                    document.getElementById('clear-button').addEventListener('click', () => {
                        if (this.signaturePad) this.signaturePad.clear();
                    });
                },
                resizeCanvas() {
                    const canvas = document.getElementById('signature-pad');
                    if (!canvas || !this.signaturePad) return;
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                    this.signaturePad.fromData(this.signaturePad.toData()); // Redraw signature
                },
                whenVisible(elementId, callback) {
                    // Utility to run a callback when a conditionally rendered element becomes visible
                    const observer = new IntersectionObserver((entries, obs) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                callback();
                                obs.disconnect(); // We only need to run it once
                            }
                        });
                    });
                    const el = document.getElementById(elementId);
                    if (el) observer.observe(el);
                },
                
                // --- MAIN INITIALIZATION & WATCHERS ---
                init() {
    // Watch for changes in the main client status radio buttons
    this.$watch('clientStatus', (newValue, oldValue) => {
        // A. If switching to 'new client', always do a FULL reset.
        if (newValue === 'new') {
            this.foundOwner = null; this.searchMessage = ''; this.ownerPets = [];
            this.selectedPetId = null; this.selectedConsentType = ''; this.hasAgreedToTerms = false;
            this.findName = ''; this.findPhone = ''; this.consentNotes = '';
            if (this.signaturePad) this.signaturePad.clear();
            return; // Stop here
        }

        // B. If switching BETWEEN 'existing' and 'consent', do a PARTIAL reset.
        // This keeps the search terms and the found owner intact.
        if ((newValue === 'existing' && oldValue === 'consent') || (newValue === 'consent' && oldValue === 'existing')) {
            // Only reset things specific to the new task
            this.selectedPetId = null;
            this.hasAgreedToTerms = false;
            this.consentNotes = '';
            if (this.signaturePad) this.signaturePad.clear();
        } else {
            // C. For any other switch (e.g., from 'new' to 'existing'), do a full reset.
            this.foundOwner = null; this.searchMessage = ''; this.ownerPets = [];
            this.selectedPetId = null; this.selectedConsentType = ''; this.hasAgreedToTerms = false;
            this.findName = ''; this.findPhone = ''; this.consentNotes = '';
            if (this.signaturePad) this.signaturePad.clear();
        }
    });

    // Watch for changes in the consent type dropdown (this logic remains the same)
    this.$watch('selectedConsentType', (value) => {
        // Reset parts of the consent form if the type changes
        this.selectedPetId = null; this.hasAgreedToTerms = false;
        this.consentNotes = '';
        if (this.signaturePad) this.signaturePad.clear();
        
        if (value) {
            this.$nextTick(() => {
                this.whenVisible('signature-pad', () => this.initSignaturePad());
            });
        }
    });
    window.addEventListener("resize", () => { if (this.signaturePad) this.resizeCanvas(); });
}
            }
        }
    </script>
    @endpush -->
@endsection