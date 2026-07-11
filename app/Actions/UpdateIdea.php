<?php

namespace App\Actions;

use App\Models\Idea;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdateIdea
{
    public function handle(array $attributes, Idea $idea): Idea
    {
        return DB::transaction(function () use ($attributes, $idea) {
            $data = collect($attributes)->only(['title', 'description', 'status', 'links'])->toArray();

            if (($attributes['image'] ?? null) instanceof UploadedFile) {
                $data['image_path'] = $attributes['image']->store('ideas', 'public');
            }

            $idea->update($data);

            $idea->steps()->delete();
            $idea->steps()->createMany($attributes['steps'] ?? []);

            return $idea;
        });
    }
}