<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            // Use the change() method to make the existing 'breed' column nullable
            $table->string('breed')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            // This reverses the change, making it not nullable again
            $table->string('breed')->nullable(false)->change();
        });
    }
};