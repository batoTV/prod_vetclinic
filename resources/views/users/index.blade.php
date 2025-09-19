@extends('layouts.app')

@section('title', 'Manage Users')

@section('header-actions')
    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'receptionist' || auth()->user()->role === 'vet')
        <a href="{{ route('users.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700">
            <i class="fas fa-plus mr-2"></i> Add New User
        </a>
    @endif
@endsection

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <table class="w-full text-left">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-4 font-semibold">Name</th>
                <th class="p-4 font-semibold">Email</th>
                <th class="p-4 font-semibold">Role</th>
                <th class="p-4 font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-4">{{ $user->name }}</td>
                    <td class="p-4">{{ $user->email }}</td>
                    <td class="p-4">{{ $user->role_name }}</td>
                    <td class="p-4">
                        <a href="{{ route('users.edit', $user->id) }}" class="text-green-600 hover:text-green-800 mr-4" title="Edit">
                            <i class="fas fa-pen"></i>
                        </a>
                        <button type="button" class="text-red-600 hover:text-red-800 delete-button" data-url="{{ route('users.destroy', $user->id) }}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection