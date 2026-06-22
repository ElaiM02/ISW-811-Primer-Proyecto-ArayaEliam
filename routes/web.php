<?php

use Illuminate\Support\Facades\Route;

Route::view('/contact', 'contact');
Route::view('/about', 'about');

Route::get('/', function () {
    $ideas = session()->get('ideas');

    return view('ideas', [
        'ideas' => $ideas
    ]);
});

Route::post('/ideas', function () {
    $idea = request('idea');

    session()->push('ideas', $idea);

    return redirect('/');
});
#Route::get('/', function () {
#    return view('welcome', [
#        'tasks' => [
#        ],
#    ]);
#});

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