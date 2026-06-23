<?php

use Illuminate\Support\Facades\Route;
use App\Models\Idea;

Route::view('/contact', 'contact');
Route::view('/about', 'about');

Route::get('/', function () {
    $ideas = Idea::all();

//    $ideas = session()->get('ideas', []);
//    $ideas = DB::table('ideas')->get();
    return view('ideas', [
        'ideas' => $ideas
    ]);
});

Route::post('/ideas', function () {
    $idea = request('idea');

    $ideas = Idea::all();
    $ideas->push($idea);

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