<?php

namespace Tests\Feature;

use Illuminate\Http\Response;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanRegister()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function testCannotRegisterWithExistingEmail()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'Duplicate User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUserCanLogin()
    {
        User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['token']);
    }

    public function testCannotLoginWithWrongPassword()
    {
        User::factory()->create([
            'email' => 'wrongpass@example.com',
            'password' => bcrypt('correctpassword'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'wrongpass@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testAuthenticatedUserCanAccessProtectedRoute()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/user');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['email' => $user->email]);
    }

    public function testUnauthenticatedUserCannotAccessProtectedRoute()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUserCanBeDeleted()
    {
        $user = User::factory()->create(['email' => 'delete@example.com']);

        $this->actingAs($user)->deleteJson("/api/user");

        $this->assertDatabaseMissing('users', ['email' => 'delete@example.com']);
    }
}
