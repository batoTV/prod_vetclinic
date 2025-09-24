<div>
    {{-- Section for displaying existing consent records --}}
    <h3 class="text-xl font-semibold mb-4">Consent History</h3>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4 font-semibold">Consent Date</th>
                    <th class="p-4 font-semibold">Type</th>
                    <th class="p-4 font-semibold">Notes</th>
                    <th class="p-4 font-semibold text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($consents as $consent)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 whitespace-nowrap">{{ $consent->created_at->format('M d, Y h:i A') }}</td>
                        <td class="p-4">{{ ucfirst($consent->consent_type) }}</td>
                        <td class="p-4">{{ $consent->notes ?? 'N/A' }}</td>
                        <td class="p-4 text-center whitespace-nowrap">
                            @if ($consent->file_path)
                                <a href="{{ asset('storage/' . $consent->file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 mr-2" title="View PDF">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endif
                            
                            {{-- This button triggers your existing delete modal --}}
                            <button type="button" class="text-red-600 hover:text-red-800 delete-consent-button" 
                                    data-url="{{ route('consents.destroy', $consent->id) }}" title="Delete Record">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">No consent records found for this pet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
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