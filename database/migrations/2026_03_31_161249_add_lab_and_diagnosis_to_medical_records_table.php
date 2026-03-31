<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('diagnoses', function (Blueprint $table) {
        // Adding the two new columns requested by the client
        $table->text('laboratory_results')->nullable()->after('assessment');
        $table->text('final_diagnosis')->nullable()->after('laboratory_results');
    });
}

public function down()
{
    Schema::table('diagnoses', function (Blueprint $table) {
        $table->dropColumn(['laboratory_results', 'final_diagnosis']);
    });
}
};
