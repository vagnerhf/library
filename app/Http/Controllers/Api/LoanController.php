<?php

namespace App\Http\Controllers\Api;

use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use Illuminate\Http\Request;
use App\Notifications\LoanCreatedNotification;

/**
 * @OA\Schema(
 *     schema="Loan",
 *     type="object",
 *     title="Loan",
 *     required={"key", "loan_date", "book", "user"},
 *     @OA\Property(property="key", type="string", example="loan-key-1"),
 *     @OA\Property(property="loan_date", type="string", format="date", example="2024-08-09"),
 *     @OA\Property(property="return_date", type="string", format="date", example="2024-08-19"),
 *     @OA\Property(property="book", ref="#/components/schemas/Book"),
 *     @OA\Property(property="user", ref="#/components/schemas/User")
 * )
 */
class LoanController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/loans",
     *     tags={"Loans"},
     *     summary="Listar todos os empréstimos",
     *     description="Retorna uma lista de todos os empréstimos cadastrados, incluindo informações sobre os livros e usuários associados.",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empréstimos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Loan")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        return LoanResource::collection(Loan::with(['book.authors', 'user'])->get());
    }

    /**
     * @OA\Get(
     *     path="/api/loans/{key}",
     *     tags={"Loans"},
     *     summary="Exibir um empréstimo específico",
     *     description="Retorna os detalhes de um empréstimo, incluindo informações sobre o livro e o usuário associado, baseado em sua chave única.",
     *     @OA\Parameter(
     *         name="key",
     *         in="path",
     *         description="Chave única do empréstimo",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do empréstimo",
     *         @OA\JsonContent(ref="#/components/schemas/Loan")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empréstimo não encontrado"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show($key)
    {
        $loan = Loan::with(['book.authors', 'user'])->where('key', $key)->firstOrFail();
        return new LoanResource($loan);
    }

    /**
     * @OA\Post(
     *     path="/api/loans",
     *     tags={"Loans"},
     *     summary="Criar um novo empréstimo",
     *     description="Registra um novo empréstimo no sistema, associando um livro a um usuário.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"book_key","user_email","loan_date"},
     *             @OA\Property(property="book_key", type="string", example="book-key-1"),
     *             @OA\Property(property="user_email", type="string", example="user@example.com"),
     *             @OA\Property(property="loan_date", type="string", format="date", example="2024-08-09")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empréstimo criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Loan")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados inválidos"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_key' => 'required|string|exists:books,key',
            'user_email' => 'required|string|exists:users,email',
            'loan_date' => 'required|date',
        ]);

        $book = Book::where('key', $validated['book_key'])->firstOrFail();
        $user = User::where('email', $validated['user_email'])->firstOrFail();

        $loan = Loan::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'loan_date' => $validated['loan_date'],
        ]);

        $user->notify(new LoanCreatedNotification($loan));

        return new LoanResource($loan->load(['book.authors', 'user']));
    }

    /**
     * @OA\Put(
     *     path="/api/loans/{key}",
     *     tags={"Loans"},
     *     summary="Atualizar um empréstimo existente",
     *     description="Atualiza os detalhes de um empréstimo existente, incluindo a data de devolução, baseado em sua chave única.",
     *     @OA\Parameter(
     *         name="key",
     *         in="path",
     *         description="Chave única do empréstimo",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="return_date", type="string", format="date", example="2024-08-19")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empréstimo atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Loan")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empréstimo não encontrado"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados inválidos"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function update(Request $request, $key)
    {
        $loan = Loan::where('key', $key)->firstOrFail();

        $validated = $request->validate([
            'return_date' => 'sometimes|required|date',
        ]);

        $loan->update($validated);

        return new LoanResource($loan->load(['book.authors', 'user']));
    }

    /**
     * @OA\Delete(
     *     path="/api/loans/{key}",
     *     tags={"Loans"},
     *     summary="Deletar um empréstimo",
     *     description="Remove um empréstimo existente do sistema com base em sua chave única.",
     *     @OA\Parameter(
     *         name="key",
     *         in="path",
     *         description="Chave única do empréstimo",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Empréstimo deletado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empréstimo não encontrado"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy($key)
    {
        $loan = Loan::where('key', $key)->firstOrFail();
        $loan->delete();
        return response()->json(null, 204);
    }
}
