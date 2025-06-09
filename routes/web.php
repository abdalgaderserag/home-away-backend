<?php

use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'test'], function () {

    Route::get('/register', fn() => view('test.auth.register'))
        ->name('register.form');
});
