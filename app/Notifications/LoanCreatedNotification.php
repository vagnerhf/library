<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Loan;

class LoanCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $bookTitle = $this->loan->book->title;

        return (new MailMessage)
            ->greeting('Olá!')
            ->line("Seu empréstimo do livro '{$bookTitle}' foi criado com sucesso.")
            ->line('Detalhes do empréstimo:')
            ->line("Livro: {$bookTitle}")
            ->line("Data do Empréstimo: {$this->loan->loan_date}")
            ->line("Data de Devolução: " . ($this->loan->return_date ? $this->loan->return_date : 'N/A'))
            ->action('Visualizar Empréstimo', url("/loans/{$this->loan->key}"))
            ->line('Obrigado por utilizar nossos serviços!');
    }
}
