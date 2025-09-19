<?php

namespace App\Console\Commands;

use App\Models\Owner;
use App\Models\Pet; // <-- Import the Pet model
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB; // <-- Import DB for transactions
use App\Models\Diagnosis;

class ImportClinicData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:import-clinic-data';

    /**
     * The console command description.
     */
    protected $description = 'Imports old clinic data from CSV files for owners and pets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting the data import process...');

        // Use a database transaction to ensure data integrity
        DB::transaction(function () {
            $this->importOwners();
            $this->importPets();
        });

        $this->info("\nData import process completed successfully!");
        return 0;
    }

    protected function importOwners()
    {
        $this->line('Importing owners...');
        $filePath = storage_path('app/import/owners.csv');
        $this->importData($filePath, Owner::class);
    }

    protected function importPets()
{
    $this->line('Importing pets...');
    $filePath = storage_path('app/import/pets.csv');

    if (!file_exists($filePath)) {
        $this->error('Import file not found for pets: ' . $filePath);
        return;
    }

    $fileHandle = fopen($filePath, 'r');
    $header = fgetcsv($fileHandle);

    $rowCount = count(file($filePath)) - 1;
    $progressBar = $this->output->createProgressBar($rowCount);
    $progressBar->start();

    while (($row = fgetcsv($fileHandle)) !== false) {
        $data = array_combine($header, $row);
        $owner = Owner::where('email', $data['owner_email'])->first();

        if ($owner) {
            // Create the pet record
            $pet = Pet::create([
                'owner_id' => $owner->id,
                'name' => $data['name'],
                'species' => $data['species'],
                'breed' => $data['breed'],
                'birth_date' => $data['birth_date'],
                'gender' => $data['gender'],
                'allergies' => $data['allergies'],
                'markings' => $data['markings'],
            ]);

            // --- NEW CODE ---
            // Check if initial medical data exists in the CSV
            if (!empty($data['checkup_date'])) {
                // Create the corresponding diagnosis record
                Diagnosis::create([
                    'pet_id' => $pet->id,
                    'vet_id' => null, // Or assign a default vet ID if you have one
                    'checkup_date' => $data['checkup_date'],
                    'weight' => $data['weight'] ?: null,
                    'temperature' => $data['temperature'] ?: null,
                    'chief_complaints' => 'Initial record from data import.',
                    'diagnosis' => 'Initial record from data import.',
                ]);
            }
            // --- END OF NEW CODE ---

        } else {
            $this->warn("\nWarning: Owner not found for pet '{$data['name']}'. Skipping pet.");
        }
        $progressBar->advance();
    }

    fclose($fileHandle);
    $progressBar->finish();
}

    // Helper function to handle generic CSV import
    protected function importData($filePath, $modelClass)
    {
        if (!file_exists($filePath)) {
            $this->error('Import file not found: ' . $filePath);
            return;
        }
        $fileHandle = fopen($filePath, 'r');
        $header = fgetcsv($fileHandle);
        $rowCount = count(file($filePath)) - 1;
        $progressBar = $this->output->createProgressBar($rowCount);
        $progressBar->start();

        while (($row = fgetcsv($fileHandle)) !== false) {
            $data = array_combine($header, $row);
            $modelClass::create($data);
            $progressBar->advance();
        }
        fclose($fileHandle);
        $progressBar->finish();
    }
}