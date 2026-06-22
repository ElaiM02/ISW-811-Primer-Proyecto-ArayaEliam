<?php

use Illuminate\Support\Facades\Route;

Route::view('/contact', 'contact');
Route::view('/about', 'about');

Route::get('/', function () {
    return view('welcome', [
        'tasks' => [

        ],
    ]);
});

#Route::view('/', 'welcome', [
#    'greeting' => 'Hello, welcome to our website!',
#    'person' => request('person')
#]);

#Route::get('/', function () {
#    return view('welcome', [
#        'greeting' => 'Hello, welcome to our website!',
#        'person' => request('person')
#    ]);
#});