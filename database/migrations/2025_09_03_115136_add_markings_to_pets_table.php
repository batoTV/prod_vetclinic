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
    Schema::table('pets', function (Blueprint $table) {
        $table->string('markings')->nullable()->after('gender');
    });
}

// Also add the reverse operation for good practice
public function down(): void
{
    Schema::table('pets', function (Blueprint $table) {
        $table->dropColumn('markings');
    });
}
};
