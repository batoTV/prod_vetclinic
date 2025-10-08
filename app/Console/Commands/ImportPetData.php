<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Owner;
use App\Models\Pet;
use App\Models\Diagnosis;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class ImportPetData extends Command
{
    /**
     * The name and signature of the console command.
     * This is what you will type to run the command.
     *
     * @var string
     */
    protected $signature = 'import:pet-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import owners, pets, and their initial diagnosis from CSV files, skipping duplicates.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting the import process... 🚀');

        // Define paths to the CSV files
        $ownersPath = storage_path('app/imports/owners.csv');
        $petsPath = storage_path('app/imports/pets.csv');

        // Use a database transaction. If any part fails, the whole process is rolled back.
        DB::beginTransaction();

        try {
            // Step 1: Import Owners
            $this->importOwners($ownersPath);

            // Step 2: Import Pets and their Diagnoses
            $this->importPetsAndDiagnoses($petsPath);

            DB::commit(); // If everything is successful, commit the changes to the database.
            $this->info('✅ All data has been successfully processed!');

        } catch (Exception $e) {
            DB::rollBack(); // If an error occurred, undo all database changes.
            $this->error('An error occurred: ' . $e->getMessage());
            $this->error('Import failed. The database has been rolled back.');
        }

        return 0;
    }

    /**
     * Import or update owners from a CSV file.
     *
     * @param string $path
     * @return void
     */
    private function importOwners($path)
    {
        $this->line('Processing owners...');
        if (!file_exists($path)) {
            throw new Exception("Owner CSV file not found at: $path");
        }
        $file = fopen($path, 'r');
        
        // Skip header row
        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            // Use updateOrCreate to avoid creating duplicate owners.
            // It checks for a matching phone_number and either updates or creates a record.
            Owner::updateOrCreate(
                ['phone_number' => $row[1]], // The unique key to check
                [
                    'name'         => $row[0],
                    'email'        => $row[2],
                    'address'      => $row[3],
                ]
            );
        }
        fclose($file);
        $this->info('Owners processed successfully.');
    }

    /**
     * Import or update pets and their initial diagnosis from a CSV file.
     *
     * @param string $path
     * @return void
     */
    private function importPetsAndDiagnoses($path)
    {
        $this->line('Processing pets and initial diagnoses...');
        if (!file_exists($path)) {
            throw new Exception("Pet CSV file not found at: $path");
        }
        $file = fopen($path, 'r');

        // Skip header row
        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            $owner = Owner::where('phone_number', $row[0])->first();

            if (!$owner) {
                $this->warn("Skipping pet '{$row[1]}' because owner with phone '{$row[0]}' was not found.");
                continue;
            }

            // Use updateOrCreate for pets. It will find a pet with this owner_id and name.
            // If found, it updates the record. If not found, it creates a new one.
            $pet = Pet::updateOrCreate(
                [
                    'owner_id' => $owner->id,
                    'name'     => $row[1], // We assume owner_id + name makes a pet unique
                ],
                [
                    'species'    => $row[2],
                    'breed'      => $row[3],
                    'birth_date' => Carbon::parse($row[4])->toDateString(),
                    'gender'     => $row[5],
                    'allergies'  => $row[6],
                    'markings'   => $row[7],
                ]
            );

            // IMPORTANT: Only create a diagnosis if the pet was NEWLY created in this run.
            // This prevents adding duplicate diagnoses to pets that already existed.
            if ($pet->wasRecentlyCreated) {
                Diagnosis::create([
                    'pet_id'       => $pet->id,
                    'checkup_date' => Carbon::parse($row[8])->toDateTimeString(),
                    'weight'       => (float) $row[9],
                    'temperature'  => (float) $row[10],
                    'diagnosis'    => $row[11],
                ]);
                $this->info("Created new pet '{$pet->name}' and its diagnosis.");
            } else {
                $this->warn("Updated or skipped existing pet '{$pet->name}'.");
            }
        }
        fclose($file);
        $this->info('Pets and diagnoses processing finished.');
    }
}