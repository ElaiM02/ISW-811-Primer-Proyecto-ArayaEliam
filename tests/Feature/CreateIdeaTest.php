<?php

use App\Models\User;

it('creates a new idea', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/ideas', [
        'title' => 'Some example title',
        'description' => 'An example description',
        'status' => 'completed',
    ])->assertRedirect('/ideas');

    expect($user->ideas()->count())->toBe(1);

    $this->assertDatabaseHas('ideas', [
        'user_id' => $user->id,
        'title' => 'Some example title',
        'description' => 'An example description',
        'status' => 'completed',
    ]);
});

it('requires a title', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/ideas', [
        'description' => 'An example description',
        'status' => 'pending',
    ])->assertSessionHasErrors('title');

    expect($user->ideas()->count())->toBe(0);
});
