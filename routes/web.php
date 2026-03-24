<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarController;
use App\Http\Controllers\EventController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/bar/create', [BarController::class, 'create'])
    ->middleware('auth');//



Route::post('/bar', [BarController::class, 'store'])
    ->middleware('auth')
    ->name('bar.store');

Route::post('/events', [EventController::class, 'store'])
    ->middleware('auth');


Route::get('/events/create', [EventController::class, 'create'])
    ->middleware('auth');


require __DIR__.'/auth.php';
