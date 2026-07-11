<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIdeaRequest;
use App\Models\Idea;
use App\Actions\CreateIdea;
use App\IdeaStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class IdeaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $ideas = Auth::user()->ideas()
            ->when(in_array($request->status, IdeaStatus::values()),fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->get();

    return view('ideas.index', [
        'ideas' => $ideas,
        'statusCounts' => Idea::statusCounts(Auth::user()),
    ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ideas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIdeaRequest $request, CreateIdea $action)
    {
        $action->handle($request->validated());

        return redirect()->route('idea.index')->with('success', 'Idea created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Idea $idea)
    {
        Gate::authorize('workWith', $idea);

        return view('ideas.show', [
            'idea' => $idea,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Idea $idea)
    {
        Gate::authorize('workWith', $idea);

        return view('ideas.edit', [
            'idea' => $idea,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreIdeaRequest $request, Idea $idea)
    {
        Gate::authorize('workWith', $idea);

        // actualizar todo EXCEPTO steps e image
        $data = $request->safe()->except(['steps', 'image']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('ideas', 'public');
        }

        $idea->update($data);

        // wipe & rebuild de los steps
        $idea->steps()->delete();
        $idea->steps()->createMany(
            collect($request->steps ?? [])->map(fn ($step) => ['description' => $step])
        );

        return redirect()->route('idea.show', $idea)->with('success', 'Idea updated');
    }

    public function destroy(Idea $idea)
    {
        Gate::authorize('workWith', $idea);

        $idea->delete();

        return redirect('/ideas');
    }
}
