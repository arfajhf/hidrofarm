<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('registers a user with name login fields and nullable profile data', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Petani Satu',
        'password' => 'password-rahasia',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('user.name', 'Petani Satu')
        ->assertJsonPath('user.profile_photo', null)
        ->assertJsonPath('user.phone_number', null)
        ->assertJsonStructure(['token', 'token_expires_at', 'one_tap_token', 'one_tap_expires_at']);

    $user = User::where('name', 'Petani Satu')->first();

    expect($user)->not->toBeNull()
        ->and(Hash::check('password-rahasia', $user->password))->toBeTrue()
        ->and($user->api_token_hash)->not->toBeNull();
});

it('logs in with name and returns an api token', function () {
    User::factory()->create([
        'name' => 'Admin Kebun',
        'password' => 'password-rahasia',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'name' => 'Admin Kebun',
        'password' => 'password-rahasia',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('user.name', 'Admin Kebun')
        ->assertJsonStructure(['token', 'one_tap_token']);

    $token = $response->json('token');

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/auth/me')
        ->assertOk()
        ->assertJsonPath('user.name', 'Admin Kebun');
});

it('can create a new api session from a one tap login token', function () {
    User::factory()->create([
        'name' => 'Operator Nutrisi',
        'password' => 'password-rahasia',
    ]);

    $login = $this->postJson('/api/auth/login', [
        'name' => 'Operator Nutrisi',
        'password' => 'password-rahasia',
    ]);

    $response = $this->postJson('/api/auth/one-tap', [
        'one_tap_token' => $login->json('one_tap_token'),
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('user.name', 'Operator Nutrisi')
        ->assertJsonStructure(['token', 'token_expires_at', 'one_tap_token', 'one_tap_expires_at']);
});
