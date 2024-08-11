<?php

namespace Tests\Unit;

use App\Models\Author;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_authors()
    {
        Author::factory()->count(3)->create();

        $response = $this->getJson('/api/authors');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['key', 'name', 'birth_date']
                ]
            ]);
    }

    public function test_can_show_an_author()
    {
        $author = Author::factory()->create();

        $response = $this->getJson("/api/authors/{$author->key}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['key', 'name', 'birth_date']
            ])
            ->assertJson(
                ['data' =>
                    [
                        'key' => $author->key,
                        'name' => $author->name,
                        'birth_date' => $author->birth_date,
                    ]
                ]);
    }

    public function test_can_create_an_author()
    {
        $data = [
            'name' => 'New Author',
            'birth_date' => '1970-01-01',
        ];

        $response = $this->postJson('/api/authors', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['key', 'name', 'birth_date']
            ])
            ->assertJson([
                'data' => [
                    'name' => 'New Author',
                    'birth_date' => '1970-01-01',
                ]
            ]);

        $this->assertDatabaseHas('authors', ['name' => 'New Author']);
    }

    public function test_can_update_an_author()
    {
        $author = Author::factory()->create();
        $data = ['name' => 'Updated Author'];

        $response = $this->putJson("/api/authors/{$author->key}", $data);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['key', 'name', 'birth_date']
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Updated Author',
                ]
            ]);

        $this->assertDatabaseHas('authors', ['name' => 'Updated Author']);
    }

    public function test_can_delete_an_author()
    {
        $author = Author::factory()->create();

        $response = $this->deleteJson("/api/authors/{$author->key}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('authors', ['key' => $author->key]);
    }
}
