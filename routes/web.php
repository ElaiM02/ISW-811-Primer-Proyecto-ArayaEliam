<?php

use Illuminate\Support\Facades\Route;
use App\Models\Idea;

Route::view('/contact', 'contact');
Route::view('/about', 'about');

//Index
Route::get('/ideas', function () {
    $ideas = Idea::all();
    return view('ideas.index', [
        'ideas' => $ideas
    ]);
});

//Show
Route::get('/ideas/{id}', function ($id) {
    $idea = Idea::find($id);

    if (is_null($idea)) {
        abort(404);
    }

    return view('ideas.show', [
        'idea' => $idea
    ]);
});

Route::post('/ideas', function () {
    $idea = request('idea');

    Idea::create([
        'description' => $idea,
        'status' => 'new'
    ]);

    return redirect('/ideas');
});

/*Route::get('/', function () {
    $ideas = Idea::all();
    $ideas = session()->get('ideas', []);
    $ideas = DB::table('ideas')->get();
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
Route::get('/', function () {
    return view('welcome', [
        'tasks' => [
        ],
    ]);
});

Route::view('/', 'welcome', [
    'greeting' => 'Hello, welcome to our website!',
    'person' => request('person')
]);

Route::get('/', function () {
    return view('welcome', [
        'greeting' => 'Hello, welcome to our website!',
        'person' => request('person')
    ]);
});*/