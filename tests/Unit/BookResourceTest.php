<?php

namespace Tests\Unit;

use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Models\Author;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_resource_has_correct_structure()
    {
        $authors = Author::factory()->count(2)->create();
        $book = Book::factory()->create();

        // Associar autores ao livro
        $book->authors()->attach($authors);

        $resource = new BookResource($book->load('authors'));
        $arrayResource = $resource->toArray(request());

        $this->assertEquals([
            'key' => $book->key,
            'title' => $book->title,
            'publication_year' => $book->publication_year,
            'authors' => $authors->map(function ($author) {
                return [
                    'key' => $author->key,
                    'name' => $author->name,
                    'birth_date' => $author->birth_date,
                    'created_at' => $author->created_at->toDateTimeString(),
                    'updated_at' => $author->updated_at->toDateTimeString(),
                ];
            })->toArray(),
            'created_at' => $book->created_at->toDateTimeString(),
            'updated_at' => $book->updated_at->toDateTimeString(),
        ], $arrayResource);
    }

}
