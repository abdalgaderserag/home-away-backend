<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\User\SupportController;
use App\Http\Middleware\VerifiedMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('projects', [ProjectController::class, 'index']);
Route::get('faq', [FaqController::class, 'index']);


Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // verification for registration verification
    Route::post('/email/verify', [VerificationController::class, 'verifyEmail']);
    Route::get('/email/verify/resend', [VerificationController::class, 'emailResend']);
    Route::post('/phone/verify', [VerificationController::class, 'verifyPhone']);
    Route::get('/phone/verify/resend', [VerificationController::class, 'phoneResend']);

    // user related controllers
    Route::get('/user/settings', [SettingsController::class, 'index']);
    Route::put('/user/settings', [SettingsController::class, 'update']);
    Route::get('/user/profile/{id?}', [ProfileController::class, 'profile']);
    Route::put('/user/profile', [ProfileController::class, 'updateProfile']);
    Route::put('/user/profile/bio', [ProfileController::class, 'updateBio']);
    Route::put('/user/profile/update-password', [ProfileController::class, 'changePassword']);
    Route::put('/user/profile/update-email', [ProfileController::class, 'changeEmail']);
    Route::put('/user/profile/update-phone', [ProfileController::class, 'changePhone']);
    Route::put('/user/profile/update-avatar', [ProfileController::class, 'changeAvatar']);
    Route::put('/user/profile/update-name', [ProfileController::class, 'changeName']);

    // notification controllers
    Route::post('/user/notifications', [NotificationController::class, 'index']);
    Route::post('user/notifications/{notificationId}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('user/notifications/{notificationId}', [NotificationController::class, 'destroy']);

    // support and ticket controller
    Route::post('/user/tickets', [SupportController::class, 'store']);

    // file upload
    Route::post('file', [UploadController::class, 'uploadFile']);
    Route::get('file/{id}', [UploadController::class, 'getFile']);
    Route::delete('file/{id}', [UploadController::class, 'removeUploadedFile']);
});

Route::middleware(['auth:sanctum', VerifiedMiddleware::class])->group(function () {

    // project controllers
    Route::get('projects/create', [ProjectController::class, "create"])->name('projects.create');
    Route::put('projects/{project}/save', [ProjectController::class, "save"])->name('projects.save');
    Route::get('projects/{project}', [ProjectController::class, 'show']);
    Route::post('projects', [ProjectController::class, 'store']);
    Route::put('projects/{project}', [ProjectController::class, 'update']);
    Route::delete('projects/{project}', [ProjectController::class, 'destroy']);

    // offer controllers
    Route::get('offers', [OfferController::class, 'index']);
    Route::post('offers', [OfferController::class, 'store']);
    Route::get('offers/{offer}', [OfferController::class, 'show']);
    Route::put('offers/{offer}', [OfferController::class, 'update']);
    Route::get('offers/{offer}/accept', [OfferController::class, 'accept']);
    Route::get('offers/{offer}/invoice', [OfferController::class, 'invoice']);
    Route::delete('offers/{offer}', [OfferController::class, 'destroy']);

    // milestones controllers
    Route::post('milestones/review', [MilestoneController::class, 'acceptOrReject']);
    Route::get('milestones/{offer}', [MilestoneController::class, 'index']);
    Route::post('milestones/{offer}', [MilestoneController::class, 'store']);
    Route::get('milestones/{milestone}', [MilestoneController::class, 'show']);
    Route::put('milestones/{milestone}', [MilestoneController::class, 'update']);
    Route::post('milestones/{milestone}/submit', [MilestoneController::class, 'submit']);
    Route::delete('milestones/{milestone}', [MilestoneController::class, 'destroy']);

    // chat controllers
    Route::get('chats', [MessageController::class, 'index']);
    Route::post('chats/{user}', [MessageController::class, 'store']);
    Route::get('chats/{chat}', [MessageController::class, 'show']);
    // Route::put('messages/{message}', [MessageController::class, 'update']);
    // Route::delete('messages/{message}', [MessageController::class, 'destroy']);

    // rating
    Route::post('rates', [RateController::class, 'index']);
    Route::post('rates/{project}', [RateController::class, 'store']);
    Route::get('rates/{rate}', [RateController::class, 'show']);
    // Route::put('rates/{rate}', [RateController::class, 'update']);
    // Route::delete('rates/{rate}', [RateController::class, 'destroy']);

    // favorite
    Route::get('favorite', [FavoriteController::class, 'index']);
    Route::post('favorite', [FavoriteController::class, 'store']);
});

Route::middleware("guest")->group(function () {
    // login register
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);

    // Social Authentication
    Route::get('social/{provider}/redirect', [SocialAuthController::class, 'redirect']);
    Route::get('social/{provider}/callback', [SocialAuthController::class, 'callback']);
    Route::post('social/token', [SocialAuthController::class, 'socialLogin']);

    // password reset
    Route::post('/password/reset-send', [ResetPasswordController::class, 'sendResetLink']);
    Route::post('/reset-password', [ResetPasswordController::class, 'reset']);
});
