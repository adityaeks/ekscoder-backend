<?php

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

test('login screen displays Google sign-in button', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Masuk dengan Google');
    $response->assertSee(route('auth.google'));
});

test('google redirect returns error when credentials are not configured', function () {
    config(['services.google.client_id' => '']);

    $response = $this->get(route('auth.google'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
});

test('unregistered google account cannot login and is rejected', function () {
    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getId')->andReturn('google-12345');
    $socialiteUser->shouldReceive('getEmail')->andReturn('unregistered@example.com');
    $socialiteUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');

    $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    // Must redirect back to login with error message
    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('registered google account can successfully login and link google_id', function () {
    $user = User::factory()->create([
        'email' => 'registered@example.com',
        'google_id' => null,
    ]);

    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getId')->andReturn('google-99999');
    $socialiteUser->shouldReceive('getEmail')->andReturn('registered@example.com');
    $socialiteUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');

    $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    // Must authenticate and redirect to dashboard
    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard', absolute: false));

    // Must link google_id
    expect($user->fresh()->google_id)->toBe('google-99999');
});
