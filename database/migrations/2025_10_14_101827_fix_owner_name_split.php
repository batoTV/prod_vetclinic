<?php

use Illuminate\Database\Migrations\Migration;
// Make sure to import your Owner model!
use App\Models\Owner; 

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Owner::all()->each(function ($owner) {
            // 1. Re-combine the full name from the incorrect split
            $fullName = trim($owner->first_name . ' ' . $owner->last_name);

            // 2. Split the full name into all its parts
            $nameParts = explode(' ', $fullName);

            // 3. Apply the correct logic
            if (count($nameParts) === 1) {
                // Case: "Buddy"
                $owner->first_name = $nameParts[0];
                $owner->last_name = '';
            } else {
                // Case: "Juan Miguel Cruz"
                // Pop the last part off as the last name
                $owner->last_name = array_pop($nameParts);
                // Join the remaining parts as the first name
                $owner->first_name = implode(' ', $nameParts);
            }
            
            // 4. Save the corrected names
            $owner->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a one-way data fix, no reverse logic is needed.
    }
};