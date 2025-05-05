<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SettingsController;
use Illuminate\Support\Facades\Route;

Route::apiResource('projects', ProjectController::class)->only(['index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [LoginController::class, "logout"])->name('logout');

    Route::post('/password/reset/send', [ResetPasswordController::class, 'sendResetLink']);
    Route::post('/password/reset', [ResetPasswordController::class, 'reset']);

    // verification for registration verification
    Route::post('/email/verify', [VerificationController::class, 'verifyEmail']);
    Route::post('/email/verify/resend', [VerificationController::class, 'emailResend']);
    Route::post('/phone/verify', [VerificationController::class, 'verifyPhone']);
    Route::post('/phone/verify/resend', [VerificationController::class, 'phoneResend']);

    Route::get('/user/settings', [SettingsController::class, 'index']);
    Route::put('/user/settings', [SettingsController::class, 'update']);
    Route::get('/user/profile/{id?}', [ProfileController::class, 'profile']);
    Route::put('/user/profile', [ProfileController::class, 'updateProfile']);

    Route::post('/user/notifications', NotificationController::class, 'index');
    Route::post('user/notifications/{notificationId}/read', [NotificationController::class, 'markAsRead']);


    Route::get('projects/create', [ProjectController::class, "create"]);
    Route::put('projects/{project}/save', [ProjectController::class, "save"]);
    Route::apiResource('projects', ProjectController::class)->except(['index', 'create']);

    Route::get('offers', [OfferController::class, 'index']);
    Route::post('offers', [OfferController::class, 'store']);
    Route::get('offers/{project}', [OfferController::class, 'show']);
    Route::put('offers/{offer}', [OfferController::class, 'update']);
    Route::get('offers/{offer}/accept', [OfferController::class, 'accept']);
    Route::get('offers/{offer}/invoice', [OfferController::class, 'invoice']);
    Route::delete('offers/{offer}', [OfferController::class, 'destroy']);

    Route::post('milestones/review', [MilestoneController::class, 'acceptOrReject']);
    Route::get('milestones/{offer}', [MilestoneController::class, 'index']);
    Route::post('milestones/{offer}', [MilestoneController::class, 'store']);
    Route::get('milestones/{milestone}', [MilestoneController::class, 'show']);
    Route::put('milestones/{milestone}', [MilestoneController::class, 'update']);
    Route::post('milestones/{milestone}/submit', [MilestoneController::class, 'submit']);
    Route::delete('milestones/{milestone}', [MilestoneController::class, 'destroy']);

    Route::apiResource('messages', MessageController::class);
    Route::apiResource('rates', RateController::class);
    Route::apiResource('favorite', FavoriteController::class)->only(['index', 'store']);
});


Route::post('/login', [LoginController::class, "login"])->name('login');
Route::post('/register', [RegisterController::class, "register"])->name('register');
Route::middleware('social.auth')->group(function () {
    // for web application social authentication
    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect']);
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);
    // for mobile application social authentication
    Route::post('/auth/social', [SocialAuthController::class, 'handleSocialLogin']);
});
