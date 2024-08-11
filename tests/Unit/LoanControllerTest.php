<?php

namespace Tests\Unit;

use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_loans()
    {
        Loan::factory()->count(3)->create();

        $response = $this->getJson('/api/loans');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')  // Verifica se há 3 itens dentro do nó "data"
            ->assertJsonStructure([
                'data' => [
                    '*' => ['key', 'book', 'user', 'loan_date', 'return_date', 'created_at', 'updated_at']
                ]
            ]);
    }

    public function test_can_show_a_loan()
    {
        $loan = Loan::factory()->create();

        $response = $this->getJson("/api/loans/{$loan->key}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['key', 'book', 'user', 'loan_date', 'return_date', 'created_at', 'updated_at']
            ])
            ->assertJson([
                'data' => [
                    'key' => $loan->key,
                    'loan_date' => $loan->loan_date,
                    'return_date' => $loan->return_date ? $loan->return_date : null,
                ]
            ]);
    }

    public function test_can_create_a_loan()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $data = [
            'book_key' => $book->key,
            'user_email' => $user->email,
            'loan_date' => now()->toDateString(),
        ];

        $response = $this->postJson('/api/loans', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['key', 'book', 'user', 'loan_date', 'return_date', 'created_at', 'updated_at']
            ])
            ->assertJson([
                'data' => [
                    'loan_date' => now()->toDateString(),
                ]
            ]);

        // Verifica se o empréstimo foi criado no banco de dados
        $this->assertDatabaseHas('loans', ['book_id' => $book->id, 'user_id' => $user->id]);
    }

    public function test_can_update_a_loan()
    {
        $loan = Loan::factory()->create();
        $data = ['return_date' => now()->addDays(10)->toDateString()];

        $response = $this->putJson("/api/loans/{$loan->key}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['key', 'book', 'user', 'loan_date', 'return_date', 'created_at', 'updated_at']
            ])
            ->assertJson([
                'data' => [
                    'return_date' => $data['return_date'],
                ]
            ]);

        $this->assertDatabaseHas('loans', ['key' => $loan->key, 'return_date' => $data['return_date']]);
    }

    public function test_can_delete_a_loan()
    {
        $loan = Loan::factory()->create();

        $response = $this->deleteJson("/api/loans/{$loan->key}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('loans', ['key' => $loan->key]);
    }
}
