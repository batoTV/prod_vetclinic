<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('diagnoses', function (Blueprint $table) {
            // First, check if 'attending_vet' column is missing and add it.
            // Note: In your model, you are using 'vet_id' to link to the User.
            // 'attending_vet' seems to be a separate text field. If it's already
            // handled by 'vet_id', you can remove this part.
            // For now, assuming you want both based on previous context.
            if (!Schema::hasColumn('diagnoses', 'attending_vet')) {
                // Placing it after 'temperature' as a safe default
                $table->string('attending_vet')->nullable()->after('temperature');
            }

            // Second, check if 'attending_staff' is missing and add it.
            if (!Schema::hasColumn('diagnoses', 'attending_staff')) {
                $table->string('attending_staff')->nullable()->after('attending_vet');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnoses', function (Blueprint $table) {
            if (Schema::hasColumn('diagnoses', 'attending_staff')) {
                $table->dropColumn('attending_staff');
            }
            if (Schema::hasColumn('diagnoses', 'attending_vet')) {
                $table->dropColumn('attending_vet');
            }
        });
    }
};
