@extends('layouts.guest')

@section('content')
    <div class="text-center">
        <div class="mx-auto mb-4 w-24 h-24 flex items-center justify-center bg-green-100 rounded-full">
            <i class="fas fa-check-circle text-5xl text-green-500"></i>
        </div>

        @if (request('action') === 'consent')
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Consent Recorded Successfully!</h2>
            <p class="text-gray-600 mb-6">Thank you. The signed consent form has been added to the pet's record.</p>
            <a href="{{ route('client.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Return to Registration Form
            </a>
        @else
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Registration Successful!</h2>
            <p class="text-gray-600 mb-6">Thank you. Your information has been added to our system.</p>
            <a href="{{ route('client.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Register Another Client
            </a>
        @endif
    </div>
@endsection