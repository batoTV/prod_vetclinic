@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto">
    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="name" class="block font-medium text-sm text-gray-700">Name</label>
            <input type="text" name="name" id="name" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div class="mb-4">
            <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
            <input type="email" name="email" id="email" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div class="mb-4">
            <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
            <input type="password" name="password" id="password" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div class="mb-4">
            <label for="role" class="block font-medium text-sm text-gray-700">Role</label>
            <select name="role" id="role" class="w-full mt-1 ..." required>
                <option value="receptionist">Receptionist</option>
                <option value="vet">Veterinarian</option>
                <option value="staff">Assistant Staff</option>
                <option value="admin">Administrator</option>
            </select>
        </div>

        <div class="flex justify-end mt-6">
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md mr-2 hover:bg-gray-300">
                Cancel
            </a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700">
                Create User
            </button>
        </div>
    </form>
</div>
@endsection