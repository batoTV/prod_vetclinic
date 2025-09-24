<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Rosevet System') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
    </head>
    <body class="font-sans text-gray-900 antialiased">
        
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 x-transition:leave="transition ease-in duration-300" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0"
                 class="absolute top-5 right-5 p-4 text-sm text-green-700 bg-green-100 rounded-lg shadow-md z-50" 
                 role="alert">
                {{ session('success') }}
            </div>
        @endif

       <div class="min-h-screen flex flex-col items-center pt-12 bg-gray-100">

    {{-- Logo Container --}}
    <div class="mb-6">
     
                <x-banner-logo class="w-96 h-auto" />
        </div>

        {{-- Form Card Container --}}
        <div class="w-full sm:max-w-4xl px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{-- This renders your page content --}}
            @if (isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </div>

    </div>
        
        <div id="confirmationModal" style="display: none;" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white text-center">
                <div class="mt-3">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                        <i class="fas fa-info-circle text-blue-600 fa-lg"></i>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Confirm Submission</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Are you sure all the information you provided is correct?
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button id="cancel-btn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md mr-2 hover:bg-gray-300">
                            Cancel
                        </button>
                        <button id="confirm-btn" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('confirmationModal');
                if (modal) {
                    const cancelBtn = document.getElementById('cancel-btn');
                    const confirmBtn = document.getElementById('confirm-btn');
                    const form = document.getElementById('registrationForm');

                    // Find the main "Register" button on the form
                    const registerButton = document.querySelector('#registrationForm button[type="submit"], #registrationForm button:not([type])');
                    
                    if (registerButton) {
                        registerButton.addEventListener('click', (event) => {
                            event.preventDefault(); // Stop the form from submitting immediately
                            modal.style.display = 'block';
                        });
                    }

                    if(cancelBtn) {
                        cancelBtn.addEventListener('click', () => {
                            modal.style.display = 'none';
                        });
                    }
                    
                    if(confirmBtn && form) {
                        confirmBtn.addEventListener('click', () => {
                            form.submit();
                        });
                    }
                }
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
         
    </body>
</html>