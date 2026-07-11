<?php

namespace App\Actions;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Facades\DB;

class CreateIdea
{
    public function __construct(
        #[CurrentUser] protected User $user
    ) {}

    public function handle(array $attributes): Idea
    {
        return DB::transaction(function () use ($attributes) {
            $data = collect($attributes)->only(['title', 'description', 'status', 'links'])->toArray();

            // si viene imagen, guardarla y agregar la ruta
            if ($attributes['image'] ?? false) {
                $data['image_path'] = $attributes['image']->store('ideas', 'public');
            }

            $idea = $this->user->ideas()->create($data);

            // crear los steps relacionados (cada step es un objeto {description, completed})
            $idea->steps()->createMany($attributes['steps'] ?? []);

            return $idea;
        });
    }
}