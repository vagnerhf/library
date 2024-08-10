<?php

namespace Tests\Unit;

use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_can_create_loan()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $loan = Loan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'book_id' => $loan->book_id,
        ]);
    }

    public function test_loan_belongs_to_book_and_user()
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();
        $loan = Loan::factory()->create(['book_id' => $book->id, 'user_id' => $user->id]);

        $this->assertEquals($book->id, $loan->book->id);
        $this->assertEquals($user->id, $loan->user->id);
    }
}
