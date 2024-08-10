<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Author;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_book()
    {
        $book = Book::factory()->create();

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => $book->title,
        ]);
    }

    public function test_book_belongs_to_many_authors()
    {
        $authors = Author::factory()->count(3)->create();
        $book = Book::factory()->create();

        $book->authors()->attach($authors);

        $this->assertCount(3, $book->authors);
        $this->assertTrue($book->authors->contains($authors[0]));
        $this->assertTrue($book->authors->contains($authors[1]));
        $this->assertTrue($book->authors->contains($authors[2]));
    }
}
