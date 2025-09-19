<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- This title tag prevents duplicates like "VetClinic - VetClinic" --}}
    <title>@hasSection('title')@yield('title') - @endif{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-200">
        
        <!-- START: Mobile Menu Overlay -->
        <!-- This overlay is shown when the sidebar is open on mobile, and clicking it will close the sidebar -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black bg-opacity-50 transition-opacity lg:hidden" x-cloak></div>
        <!-- END: Mobile Menu Overlay -->

        <aside :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }" class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-white shadow-lg lg:translate-x-0 lg:static lg:inset-0">
            <div class="flex items-center justify-center mt-8">
                <a href="{{ url('/dashboard') }}">
                     <x-vetclinic-logo class="w-56 h-56" />
                </a>
            </div>

            <nav class="mt-10">
                @if(auth()->user()->isVet() || auth()->user()->isReceptionist())
                <a class="{{ request()->is('dashboard*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200' }} flex items-center mt-4 py-2 px-6" href="{{ url('/dashboard') }}">
                    <i class="fas fa-tachometer-alt w-6 h-6"></i>
                    <span class="mx-3">Dashboard</span>
                </a>
                @endif

                <a class="{{ request()->is('pets*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200' }} flex items-center mt-4 py-2 px-6" href="{{ url('/pets') }}">
                    <i class="fas fa-paw w-6 h-6"></i>
                    <span class="mx-3">Pets</span>
                </a>

                <a class="{{ request()->is('owners*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200' }} flex items-center mt-4 py-2 px-6" href="{{ url('/owners') }}">
                    <i class="fas fa-users w-6 h-6"></i>
                    <span class="mx-3">Owners</span>
                </a>

                  @if(auth()->user()->role === 'admin' || auth()->user()->role === 'receptionist' ||  auth()->user()->role === 'vet')
                <a class="{{ request()->is('users*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200' }} flex items-center mt-4 py-2 px-6" href="{{ route('users.index') }}">
                    <i class="fas fa-user-cog w-6 h-6"></i>
                    <span class="mx-3">Manage Users</span>
                </a>
                 @endif
                
                @if(auth()->user()->isVet() || auth()->user()->isReceptionist())
                <a class="{{ request()->is('calendar*') || request()->is('appointments*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200' }} flex items-center mt-4 py-2 px-6" href="{{ url('/calendar') }}">
                    <i class="fas fa-calendar-alt w-6 h-6"></i>
                    <span class="mx-3">Calendar</span>
                </a>
                @endif
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex justify-between items-center py-4 px-6 bg-white">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>

                   <div class="relative mx-4 lg:mx-0">
                        <h1 class="text-2xl font-semibold text-gray-700">@yield('title')</h1>
                    </div>
                </div>
                
                <div class="flex items-center">
                    @yield('header-actions')

                    <div class="relative ml-4" x-data="{ dropdownOpen: false }">
                        <button @click="dropdownOpen = !dropdownOpen" class="relative block focus:outline-none text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-user-circle fa-2x"></i>
                        </button>
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-xl z-10" x-cloak>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Logout</a>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md shadow-md" role="alert">
                        <div class="flex justify-between">
                            <div>
                                <p class="font-bold">Success!</p>
                                <p>{{ session('success') }}</p>
                            </div>
                            <button @click="show = false" class="text-green-700">&times;</button>
                        </div>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
     <div id="deleteModal" style="display: none;" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white text-center">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600 fa-lg"></i>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete Record</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to delete this record? This action cannot be undone.
                    </p>
                </div>
                <form id="deleteForm" method="POST" class="items-center px-4 py-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" id="cancelDeleteBtn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md mr-2 hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Confirm Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('deleteModal');
            const cancelBtn = document.getElementById('cancelDeleteBtn');
            const deleteForm = document.getElementById('deleteForm');

            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function () {
                    const url = this.getAttribute('data-url');
                    deleteForm.setAttribute('action', url);
                    modal.style.display = 'block';
                });
            });

            cancelBtn.addEventListener('click', function () {
                modal.style.display = 'none';
            });
            
            window.addEventListener('click', function (event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
</body>
</html>
