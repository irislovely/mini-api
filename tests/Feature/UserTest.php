<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('DatabaseSeeder');
    }

    public function testGetUser()
    {
        $user = User::first();
        $this->actingAs($user);

        $response = $this->getJson('/api/users/');
        $response->assertStatus(200)->assertJsonFragment(["id" => $user->id]);
    }

    public function testLoginWithEmail()
    {
        $user = User::factory()->create([
            'email' => "trung@trung.com",
            'password' => bcrypt($password = 'Abcd@12345'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)->assertJsonFragment(['token_type' => 'Bearer']);
    }

    public function testLoginWithInvalidEmail()
    {
        $user = User::factory()->create([
            'email' => "trung@trung.com",
            'password' => bcrypt($password = 'Abcd@12345'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'xya@a.com',
            'password' => $password,
        ]);

        $response->assertStatus(500)->assertJsonFragment(['message' => 'Unauthorized']);
    }

    public function testLoginWithIncorrectPassword()
    {
        $user = User::factory()->create([
            'email' => "trung@trung.com",
            'password' => bcrypt($password = 'Abcd@12345'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'trung@trung.com',
            'password' => 'ABCD123',
        ]);

        $response->assertStatus(500)->assertJsonFragment(['message' => 'Unauthorized']);
    }
}
