<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [LoginController::class, "logout"])->name('logout');
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('offers', OfferController::class);
    Route::apiResource('offers/{offer}/milestones', MilestoneController::class)
        ->parameters(['offer' => 'offer'])
        ->names('offers.milestones');
});


Route::post('/login', [LoginController::class, "login"])->name('login');
Route::post('/register', [RegisterController::class, "register"])->name('register');
