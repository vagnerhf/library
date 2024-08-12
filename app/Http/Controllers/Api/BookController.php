<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use App\Models\Book;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="Book",
 *     type="object",
 *     title="Book",
 *     required={"key", "title", "publication_year"},
 *     @OA\Property(property="key", type="string", example="book-key-1"),
 *     @OA\Property(property="title", type="string", example="Harry Potter and the Philosopher's Stone"),
 *     @OA\Property(property="publication_year", type="integer", example=1997),
 *     @OA\Property(
 *         property="authors",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Author")
 *     )
 * )
 */
class BookController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/books",
     *     tags={"Books"},
     *     summary="Listar todos os livros",
     *     description="Retorna uma lista de todos os livros cadastrados, incluindo os autores associados.",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de livros",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Book")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        return BookResource::collection(Book::with('authors')->get());
    }

    /**
     * @OA\Get(
     *     path="/api/books/{key}",
     *     tags={"Books"},
     *     summary="Exibir um livro específico",
     *     description="Retorna os detalhes de um livro, incluindo seus autores, baseado em sua chave única.",
     *     @OA\Parameter(
     *         name="key",
     *         in="path",
     *         description="Chave única do livro",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do livro",
     *         @OA\JsonContent(ref="#/components/schemas/Book")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Livro não encontrado"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show($key)
    {
        $book = Book::with('authors')->where('key', $key)->firstOrFail();
        return new BookResource($book);
    }

    /**
     * @OA\Post(
     *     path="/api/books",
     *     tags={"Books"},
     *     summary="Criar um novo livro",
     *     description="Cria um novo livro no sistema, associando-o a um ou mais autores.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","publication_year","author_keys"},
     *             @OA\Property(property="title", type="string", example="Harry Potter and the Philosopher's Stone"),
     *             @OA\Property(property="publication_year", type="integer", example=1997),
     *             @OA\Property(
     *                 property="author_keys",
     *                 type="array",
     *                 @OA\Items(type="string", example="author-key-1")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Livro criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Book")
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
        $book = Book::create($request->all());
        $authorIds = Author::whereIn('key', $request->input('author_keys', []))->pluck('id')->toArray();
        $book->authors()->sync($authorIds);
        return new BookResource($book->load('authors'));
    }

    /**
     * @OA\Put(
     *     path="/api/books/{key}",
     *     tags={"Books"},
     *     summary="Atualizar um livro existente",
     *     description="Atualiza os detalhes de um livro existente, incluindo os autores associados, baseado em sua chave única.",
     *     @OA\Parameter(
     *         name="key",
     *         in="path",
     *         description="Chave única do livro",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Harry Potter and the Chamber of Secrets"),
     *             @OA\Property(property="publication_year", type="integer", example=1998),
     *             @OA\Property(
     *                 property="author_keys",
     *                 type="array",
     *                 @OA\Items(type="string", example="author-key-2")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Livro atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Book")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Livro não encontrado"
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
        $book = Book::where('key', $key)->firstOrFail();
        $book->update($request->all());
        $authorIds = Author::whereIn('key', $request->input('author_keys', []))->pluck('id')->toArray();
        $book->authors()->sync($authorIds);
        return new BookResource($book->load('authors'));
    }

    /**
     * @OA\Delete(
     *     path="/api/books/{key}",
     *     tags={"Books"},
     *     summary="Deletar um livro",
     *     description="Remove um livro existente do sistema com base em sua chave única.",
     *     @OA\Parameter(
     *         name="key",
     *         in="path",
     *         description="Chave única do livro",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Livro deletado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Livro não encontrado"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy($key)
    {
        $book = Book::where('key', $key)->firstOrFail();
        $book->delete();
        return response()->json(null, 204);
    }
}
