<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LoanCreatedNotification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoanIntegrationTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_can_create_loan_with_book_and_user_keys()
    {
        // Criação de um usuário e um livro
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // Dados para a requisição de criação de empréstimo
        $loanData = [
            'book_key' => $book->key,
            'user_email' => $user->email,
            'loan_date' => now()->toDateString(),
        ];

        // Envia a requisição para criar o empréstimo
        $response = $this->postJson('/api/loans', $loanData);

        // Verifica se a requisição foi bem sucedida
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'key', 'loan_date', 'return_date', 'created_at', 'updated_at', 'user',
                    'book' => ['key', 'title', 'publication_year']
                ]
            ]);

        // Verifica se o empréstimo foi salvo corretamente no banco de dados
        $this->assertDatabaseHas('loans', [
            'book_id' => $book->id,
            'user_id' => $user->id,
            'loan_date' => now()->toDateString(),
        ]);

        // Verifica se o relacionamento entre loan, book e user está correto
        $loan = Loan::where('book_id', $book->id)
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($loan);
        $this->assertEquals($loan->book->key, $book->key);
        $this->assertEquals($loan->user->email, $user->email);
    }

    public function test_user_can_view_loan_details()
    {
        // Criação de um usuário, livro e empréstimo
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $loan = Loan::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'loan_date' => now()->toDateString(),
        ]);

        // Envia a requisição para visualizar o empréstimo
        $response = $this->getJson("/api/loans/{$loan->key}");

        // Verifica se a requisição foi bem sucedida e se a estrutura está correta
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'key', 'loan_date', 'return_date', 'created_at', 'updated_at', 'user',
                    'book' => ['key', 'title', 'publication_year']
                ]
            ]);
    }

    public function test_loan_creation_sends_notification_via_queue()
    {
        Notification::fake();
        Queue::fake();

        $user = User::factory()->create();
        $book = Book::factory()->create();
        $data = [
            'book_key' => $book->key,
            'user_email' => $user->email,
            'loan_date' => now()->toDateString(),
        ];

        $response = $this->postJson('/api/loans', $data);

        $response->assertStatus(201);

        // Verifica se a notificação foi enviada para a fila
        Notification::assertSentTo(
            [$user], LoanCreatedNotification::class
        );

        // Verifica se o trabalho foi enfileirado
        Queue::assertPushed(LoanCreatedNotification::class);

    }
}
