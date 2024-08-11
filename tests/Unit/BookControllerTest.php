<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_books()
    {
        Book::factory()->count(3)->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')  // Verifica se há 3 itens dentro do nó "data"
            ->assertJsonStructure([
                'data' => [
                    '*' => ['key', 'title', 'publication_year', 'authors']
                ]
            ]);
    }

    public function test_can_show_a_book()
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/books/{$book->key}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['key', 'title', 'publication_year', 'authors']
            ])
            ->assertJson([
                'data' => [
                    'key' => $book->key,
                    'title' => $book->title,
                    'publication_year' => $book->publication_year,
                ]
            ]);
    }

    public function test_can_create_a_book()
    {
        $authors = Author::factory()->count(2)->create();
        $data = [
            'title' => 'New Book',
            'publication_year' => 2023,
            'author_keys' => $authors->pluck('key')->toArray(),
        ];

        $response = $this->postJson('/api/books', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['key', 'title', 'publication_year', 'authors']
            ])
            ->assertJson([
                'data' => ['title' => 'New Book']
            ]);

        $this->assertDatabaseHas('books', ['title' => 'New Book']);
    }

    public function test_can_update_a_book()
    {
        $book = Book::factory()->create();
        $authors = Author::factory()->count(2)->create();
        $data = [
            'title' => 'Updated Book',
            'author_ids' => $authors->pluck('id')->toArray(),
        ];

        $response = $this->putJson("/api/books/{$book->key}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['key', 'title', 'publication_year', 'authors']
            ])
            ->assertJson([
                'data' => ['title' => 'Updated Book']
            ]);

        $this->assertDatabaseHas('books', ['title' => 'Updated Book']);
    }

    public function test_can_delete_a_book()
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/books/{$book->key}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('books', ['key' => $book->key]);
    }
}
