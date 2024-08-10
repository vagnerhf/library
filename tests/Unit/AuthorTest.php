<?php

namespace Tests\Unit;

use App\Models\Author;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_author()
    {
        $author = Author::factory()->create();

        $this->assertDatabaseHas('authors', [
            'id' => $author->id,
            'name' => $author->name,
        ]);
    }

    public function test_author_has_books_relation()
    {
        $author = Author::factory()->hasBooks(3)->create();

        $this->assertCount(3, $author->books);
    }
}
