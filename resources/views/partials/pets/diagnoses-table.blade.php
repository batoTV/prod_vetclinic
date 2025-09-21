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
                        <a href="{{ route('diagnoses.show', $diagnosis->id) }}" class="text-indigo-600 hover:text-indigo-800 mr-2" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('diagnoses.edit', $diagnosis->id) }}" class="text-green-600 hover:text-green-800 mr-2" title="Edit Record">
                            <i class="fas fa-pen"></i>
                        </a>
                        <button type="button" class="text-red-600 hover:text-red-800 delete-button" data-url="{{ route('diagnoses.destroy', $diagnosis->id) }}" title="Delete Record">
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