<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagnoses', function (Blueprint $table) {
            // Use change() to modify existing columns
            $table->text('diagnosis')->nullable()->change();
            $table->text('chief_complaint')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('diagnoses', function (Blueprint $table) {
            // This reverses the change if you ever need to rollback
            $table->text('diagnosis')->nullable(false)->change();
            $table->text('chief_complaint')->nullable(false)->change();
        });
    }
};