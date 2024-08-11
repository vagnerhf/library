<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_users()
    {
        User::factory()->count(3)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')  // Verifica se há 3 itens dentro do nó "data"
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'email', 'created_at', 'updated_at']
                ]
            ]);
    }

    public function test_can_show_a_user_by_email()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->email}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['name', 'email', 'created_at', 'updated_at']
            ])
            ->assertJson([
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ]);
    }

    public function test_can_create_a_user()
    {
        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/users', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['name', 'email', 'created_at', 'updated_at']
            ])
            ->assertJson([
                'data' => [
                    'name' => 'New User',
                    'email' => 'newuser@example.com',
                ]
            ]);

        // Verifica se o usuário foi criado no banco de dados
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_can_update_a_user()
    {
        $user = User::factory()->create();
        $data = ['name' => 'Updated User'];

        $response = $this->putJson("/api/users/{$user->email}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['name', 'email', 'created_at', 'updated_at']
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Updated User',
                    'email' => $user->email,
                ]
            ]);

        $this->assertDatabaseHas('users', ['name' => 'Updated User']);
    }

    public function test_can_delete_a_user_by_email()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->email}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', ['email' => $user->email]);
    }
}
