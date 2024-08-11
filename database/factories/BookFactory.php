<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'key' => Str::uuid()->toString(),
            'title' => $this->faker->sentence,
            'publication_year' => $this->faker->year
        ];
    }

    public function withAuthors($authors = null)
    {
        return $this->afterCreating(function (Book $book) use ($authors) {
            $authors = $authors ?: Author::factory()->count(2)->create();
            $book->authors()->attach($authors->pluck('id')->toArray());
        });
    }
}
