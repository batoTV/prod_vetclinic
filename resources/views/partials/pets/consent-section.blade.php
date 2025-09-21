<div>
    {{-- Section for displaying existing consent records --}}
    <h3 class="text-xl font-semibold mb-4">Consent History</h3>
    <div class="space-y-4">
        @forelse ($consents as $consent)
            <div class="border-b pb-2 mb-2">
                <p class="text-sm text-gray-600">Consent given on: {{ $consent->created_at->format('M d, Y \a\t h:i A') }}</p>
                @if ($consent->file_path)
                    <a href="{{ asset('storage/' . $consent->file_path) }}" target="_blank" class="text-indigo-600 hover:underline">
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

    <!-- {{-- Section for adding a new consent record --}}
    <div class="mt-8 pt-6 border-t">
        <h3 class="text-xl font-semibold mb-4">Record New Consent</h3>
        
        <form id="consentForm" action="{{ route('consents.store', $pet->id) }}" method="POST">
            @csrf
            <div x-data="{ consentType: '' }">
                
                {{-- Dropdown to select consent type --}}
                <div class="mb-6">
                    <label for="consent_type" class="block font-medium text-sm text-gray-700">Type of Consent</label>
                    <select name="consent_type" id="consent_type" x-model="consentType" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select a form...</option>
                        <option value="general">General Consent</option>
                        <option value="surgery">Surgery/Hospitalization Consent</option>
                    </select>
                </div>

                {{-- The rest of the form is hidden until a type is selected --}}
                <div x-show="consentType" x-transition class="space-y-4">
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
</div>