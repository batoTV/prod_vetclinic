<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Owner;
use App\Models\Pet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Petseeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create the specific users with roles
        User::create([
            'name' => 'Dr. Vet',
            'email' => 'vet@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'vet',
        ]);

        User::create([
            'name' => 'Reception Staff',
            'email' => 'receptionist@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'receptionist',
        ]);

        User::create([
            'name' => 'Assistant Staff',
            'email' => 'staff@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'assistant',
        ]);

        // Create 10 owners with 1 pet each
        Owner::factory(10)->create()->each(function ($owner) {
            Pet::factory()->create(['owner_id' => $owner->id]);
        });

        // Create 5 owners with 2 pets each
        Owner::factory(5)->create()->each(function ($owner) {
            Pet::factory(2)->create(['owner_id' => $owner->id]);
        });
    }
}
