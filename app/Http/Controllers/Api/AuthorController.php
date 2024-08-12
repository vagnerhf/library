<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="API Biblioteca",
 *     version="1.0.0",
 *          description="Esta é a documentação da API RESTful do sistema de gerenciamento de biblioteca.",
 *      @OA\Contact(
 *          email="ferreira.henrique.vagner@gmail.com"
 *      ),
 * )
 */

/**
 * @OA\Schema(
 *      schema="Author",
 *      type="object",
 *      title="Author",
 *      required={"key", "name", "birth_date"},
 *      @OA\Property(property="key", type="string", example="author-key-1"),
 *      @OA\Property(property="name", type="string", example="J.K. Rowling"),
 *      @OA\Property(property="birth_date", type="string", format="date", example="1965-07-31"),
 *  )
 */
class AuthorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/authors",
     *     tags={"Authors"},
     *     summary="Listar todos os autores",
     *     description="Retorna uma lista de todos os autores cadastrados.",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de autores",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Author")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        return AuthorResource::collection(Author::all());
    }

    /**
     * @OA\Get(
     *     path="/api/authors/{key}",
     *     tags={"Authors"},
     *     summary="Exibir um autor específico",
     *     description="Retorna os detalhes de um autor baseado em sua chave única.",
     *     @OA\Parameter(
     *         name="key",
     *         in="path",
     *         description="Chave única do autor",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do autor",
     *         @OA\JsonContent(ref="#/components/schemas/Author")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Autor não encontrado"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show($key)
    {
        $author = Author::where('key', $key)->firstOrFail();
        return new AuthorResource($author);
    }

    /**
     * @OA\Post(
     *     path="/api/authors",
     *     tags={"Authors"},
     *     summary="Criar um novo autor",
     *     description="Cria um novo autor no sistema.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","birth_date"},
     *             @OA\Property(property="name", type="string", example="J.K. Rowling"),
     *             @OA\Property(property="birth_date", type="string", format="date", example="1965-07-31")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Autor criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Author")
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
        $author = Author::create($request->all());
        return new AuthorResource($author);
    }

    /**
     * @OA\Put(
     *     path="/api/authors/{key}",
     *     tags={"Authors"},
     *     summary="Atualizar um autor existente",
     *     description="Atualiza os detalhes de um autor existente com base em sua chave única.",
     *     @OA\Parameter(
     *         name="key",
     *         in="path",
     *         description="Chave única do autor",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="J.K. Rowling"),
     *             @OA\Property(property="birth_date", type="string", format="date", example="1965-07-31")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Autor atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Author")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Autor não encontrado"
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
        $author = Author::where('key', $key)->firstOrFail();
        $author->update($request->all());
        return new AuthorResource($author);
    }

    /**
     * @OA\Delete(
     *     path="/api/authors/{key}",
     *     tags={"Authors"},
     *     summary="Deletar um autor",
     *     description="Remove um autor existente do sistema com base em sua chave única.",
     *     @OA\Parameter(
     *         name="key",
     *         in="path",
     *         description="Chave única do autor",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Autor deletado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Autor não encontrado"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy($key)
    {
        $author = Author::where('key', $key)->firstOrFail();
        $author->delete();
        return response()->json(null, 204);
    }
}
