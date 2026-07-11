<?php

namespace App\Http\Controllers;

use App\Models\Step;
use Illuminate\Http\Request;

class StepController extends Controller
{
    public function update(Step $step)
    {
        // Gate::authorize('update', $step->idea); // se agrega en el episodio de autorizacion

        $step->update(['completed' => ! $step->completed]);

        return back();
    }
}
