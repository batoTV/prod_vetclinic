<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('diagnoses', function (Blueprint $table) {
        // Add the new column for the vet's ID
        $table->foreignId('vet_id')->nullable()->constrained('users')->after('temperature');
        // Remove the old text column
        $table->dropColumn('attending_vet');
    });
}

public function down(): void
{
    Schema::table('diagnoses', function (Blueprint $table) {
        $table->dropForeign(['vet_id']);
        $table->dropColumn('vet_id');
        $table->string('attending_vet')->nullable();
    });
}
};
