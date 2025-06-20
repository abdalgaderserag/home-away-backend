<?php

use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'test'], function () {

    Route::get('/register', fn() => view('test.auth.register'))
        ->name('register.form');

    // Route::get('login');units

    Route::get('projects', fn() => view('test.project.index'));

    Route::get('/projects/{id}', function ($id) {
        return view('test.project.show')->with(['id' => $id]);
    });

    Route::get('/projects/create', function () {
        $u = DB::table('unit_types')->get('*');
        $loc = DB::table('locations')->get('*');
        $s = DB::table('skills')->get('*');
        return view('test.project.create')->with(['units' => $u,'locations' => $loc,'skills' => $s]);
    });

    Route::get('notifications', fn() => view('test.notification'));
    Route::get('verify', fn() => view('test.auth.verify'));

});

Route::get('/pulse',fn()=> abort(404));