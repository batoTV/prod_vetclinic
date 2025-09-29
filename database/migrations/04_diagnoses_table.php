    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('diagnoses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pet_id')->constrained()->onDelete('cascade');
                $table->date('checkup_date');
                $table->decimal('weight', 5, 2)->nullable();
                $table->decimal('temperature', 4, 1)->nullable();
                $table->string('attending_vet')->nullable();
                $table->text('chief_complaints');
                $table->text('assessment')->nullable();
                 $table->text('diagnosis'); 
                $table->text('plan')->nullable();
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('diagnoses');
        }
    };
    