<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientRegistrationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConsentController;

// Public Client Registration Routes
Route::get('/client-register', [ClientRegistrationController::class, 'create'])->name('client.create');
Route::post('/client-register', [ClientRegistrationController::class, 'store'])->name('client.store');
Route::post('/client-register/find', [ClientRegistrationController::class, 'findOwner'])->name('client.find');
Route::post('/pets/{pet}/consents', [ConsentController::class, 'store'])->name('consents.store');
Route::get('/register/success/{action?}', [ClientRegistrationController::class, 'success'])->name('client.success');
Route::get('/owners/{owner}/pets', [OwnerController::class, 'getPetsJson'])->name('owners.pets.json');


// Authenticated Staff Routes - NO ROLE MIDDLEWARE
Route::middleware(['auth'])->group(function () {
    
    Route::get('/consents/{consent}/download',[ConsentController::class, 'downloadPdf'])->name('consents.download');	
    // Dashboard and Calendar - will check roles in controller
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/calendar', [AppointmentController::class, 'index'])->name('calendar');
    Route::get('/api/appointments', [AppointmentController::class, 'getEvents']);
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');

    // Routes accessible by ALL staff
    Route::get('/pets', [PetController::class, 'index'])->name('pets.index');
    Route::get('/pets/create', [PetController::class, 'create'])->name('pets.create');
    Route::post('/pets', [PetController::class, 'store'])->name('pets.store');
    Route::get('/pets/{pet}', [PetController::class, 'show'])->name('pets.show');
    Route::get('/pets/{pet}/edit', [PetController::class, 'edit'])->name('pets.edit');
    Route::put('/pets/{pet}', [PetController::class, 'update'])->name('pets.update');
    Route::delete('/pets/{pet}', [PetController::class, 'destroy'])->name('pets.destroy');

    Route::get('/owners', [OwnerController::class, 'index'])->name('owners.index');
    Route::get('/owners/create', [OwnerController::class, 'create'])->name('owners.create');
    Route::post('/owners', [OwnerController::class, 'store'])->name('owners.store');
    Route::get('/owners/{owner}', [OwnerController::class, 'show'])->name('owners.show');
    Route::get('/owners/{owner}/edit', [OwnerController::class, 'edit'])->name('owners.edit');
    Route::put('/owners/{owner}', [OwnerController::class, 'update'])->name('owners.update');
    Route::delete('/owners/{owner}', [OwnerController::class, 'destroy'])->name('owners.destroy');
    
    Route::get('/pets/{pet}/diagnoses/create', [DiagnosisController::class, 'create'])->name('diagnoses.create');
    Route::post('/pets/{pet}/diagnoses', [DiagnosisController::class, 'store'])->name('diagnoses.store');
    Route::get('/diagnoses/{diagnosis}', [DiagnosisController::class, 'show'])->name('diagnoses.show');
    Route::get('/diagnoses/{diagnosis}/edit', [DiagnosisController::class, 'edit'])->name('diagnoses.edit');
    Route::put('/diagnoses/{diagnosis}', [DiagnosisController::class, 'update'])->name('diagnoses.update');
    Route::delete('/diagnoses/{diagnosis}', [DiagnosisController::class, 'destroy'])->name('diagnoses.destroy');
    Route::delete('/diagnosis-images/{image}', [DiagnosisController::class, 'destroyImage'])->name('diagnoses.images.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('users', UserController::class)
    ->middleware(['auth', 'verified']);

    



});

// Default redirect for root URL
Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';