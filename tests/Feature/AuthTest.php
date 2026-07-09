<?php

use App\Models\User;

it('registers a user', function () {
    $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
    ])->assertRedirect('/ideas');

    $this->assertAuthenticated();
    expect(auth()->user()->email)->toBe('john@example.com');
});

it('requires a valid email to register', function () {
    $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'no-es-email',
        'password' => 'password123',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('logs in a user', function () {
    $user = User::factory()->create([
        'password' => 'password123',
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password123',
    ])->assertRedirect('/ideas');

    $this->assertAuthenticated();
});

it('rejects invalid credentials', function () {
    User::factory()->create([
        'email' => 'jane@example.com',
        'password' => 'password123',
    ]);

    $this->post('/login', [
        'email' => 'jane@example.com',
        'password' => 'contrasena-incorrecta',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('logs out a user', function () {
    $this->actingAs(User::factory()->create())
        ->delete('/logout')
        ->assertRedirect('/ideas');

    $this->assertGuest();
});
