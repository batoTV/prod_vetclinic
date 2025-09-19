@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        
        {{-- Profile Information Form --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold border-b pb-4 mb-4">Profile Information</h2>
            <p class="text-gray-600 mb-6">Update your account's profile information and email address.</p>

            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="mb-4">
                    <label for="name" class="block font-medium text-sm text-gray-700">Name</label>
                    <input id="name" name="name" type="text" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                    {{-- You can add error display logic here if needed --}}
                </div>

                <div class="mb-4">
                    <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                    <input id="email" name="email" type="email" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('email', $user->email) }}" required autocomplete="username">
                    {{-- You can add error display logic here if needed --}}
                </div>

                 <div class="mb-4">
                    <label for="role" class="block font-medium text-sm text-gray-700">Role</label>
                    <input id="role" type="text" class="w-full mt-1 border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed" value="{{ ucfirst($user->role) }}" disabled>
                </div>

                <div class="flex items-center gap-4 mt-6">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition-colors duration-300">
                        Save
                    </button>

                    @if (session('status') === 'profile-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600">Saved.</p>
                    @endif
                </div>
            </form>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold border-b pb-4 mb-4">Update Password</h2>
            <p class="text-gray-600 mb-6">Ensure your account is using a long, random password to stay secure.</p>

            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div class="mb-4">
                    <label for="current_password" class="block font-medium text-sm text-gray-700">Current Password</label>
                    <input id="current_password" name="current_password" type="password" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" autocomplete="current-password">
                </div>

                <div class="mb-4">
                    <label for="password" class="block font-medium text-sm text-gray-700">New Password</label>
                    <input id="password" name="password" type="password" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" autocomplete="new-password">
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" autocomplete="new-password">
                </div>

                <div class="flex items-center gap-4 mt-6">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition-colors duration-300">
                        Save
                    </button>

                    @if (session('status') === 'password-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600">Saved.</p>
                    @endif
                </div>
            </form>
        </div>

         <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold border-b pb-4 mb-4">Delete Account</h2>
            <p class="text-gray-600 mb-6">Once your account is deleted, all of its resources and data will be permanently removed. Before deleting your account, please download any data or information that you wish to retain.</p>

            {{-- This button will trigger the confirmation modal --}}
            <button 
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="bg-red-600 text-white px-4 py-2 rounded-lg shadow hover:bg-red-700 transition-colors duration-300"
            >
                Delete Account
            </button>
        </div>

       <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
    <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
        @csrf
        @method('delete')

        <h2 class="text-lg font-medium text-gray-900">
            Are you sure you want to delete your account?
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
        </p>

        <div class="mt-6">
            <label for="password" class="sr-only">Password</label>
            <input
                id="password"
                name="password"
                type="password"
                class="mt-1 block w-3/4 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Password"
            />
            
            {{-- This will display a password error if one occurs --}}
            @error('password', 'userDeletion')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6 flex justify-end">
            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md mr-2 hover:bg-gray-300">
                Cancel
            </button>

            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                Delete Account
            </button>
        </div>
    </form>
</x-modal>

    </div>
@endsection