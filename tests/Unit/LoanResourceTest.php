<?php

namespace Tests\Unit;

use App\Http\Resources\LoanResource;
use App\Models\Loan;
use App\Models\Book;
use App\Models\Author;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_loan_resource_has_correct_structure()
    {
        $user = User::factory()->create();
        $authors = Author::factory()->count(2)->create();
        $book = Book::factory()->create();

        $book->authors()->attach($authors);

        $loan = Loan::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        $loan->load('book.authors');

        $resource = new LoanResource($loan);
        $arrayResource = $resource->toArray(request());

        $this->assertEquals([
            'key' => $loan->key,
            'book' => [
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
            ],
            'user' => $user->name,
            'loan_date' => $loan->loan_date,
            'return_date' => $loan->return_date,
            'created_at' => $loan->created_at->toDateTimeString(),
            'updated_at' => $loan->updated_at->toDateTimeString(),
        ], $arrayResource);
    }
}
