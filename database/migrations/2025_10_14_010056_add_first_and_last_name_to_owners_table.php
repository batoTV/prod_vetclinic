<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Owner; // Import the Owner model

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            // 1. Add the new columns after the 'id' column
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
        });

        // 2. Move existing data from 'name' to the new columns
        Owner::all()->each(function ($owner) {
            $nameParts = explode(' ', $owner->name, 2);
            $owner->first_name = $nameParts[0];
            $owner->last_name = $nameParts[1] ?? ''; // Use empty string if no last name
            $owner->save();
        });

        Schema::table('owners', function (Blueprint $table) {
            // 3. Drop the old 'name' column
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};