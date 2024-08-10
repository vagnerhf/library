<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition()
    {
        return [
            'key' => Str::uuid()->toString(),
            'book_id' => Book::factory(),
            'user_id' => User::factory(),
            'loan_date' => $this->faker->date,
            'return_date' => null,
        ];
    }
}
