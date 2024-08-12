<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     required={"name", "email"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Listar todos os usuários",
     *     description="Retorna uma lista de todos os usuários cadastrados.",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuários",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $users = User::all();
        return response()->json(['data' => $users]);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{email}",
     *     tags={"Users"},
     *     summary="Exibir um usuário específico",
     *     description="Retorna os detalhes de um usuário específico com base em seu email.",
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         description="Email do usuário",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do usuário",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show($email)
    {
        $user = User::where('email', $email)->firstOrFail();
        return response()->json(['data' => $user]);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Criar um novo usuário",
     *     description="Cria um novo usuário no sistema.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/User")
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return response()->json(['data' => $user], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{email}",
     *     tags={"Users"},
     *     summary="Atualizar um usuário existente",
     *     description="Atualiza os detalhes de um usuário existente com base em seu email.",
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         description="Email do usuário",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="password", type="string", format="password", example="newsecret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados inválidos"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function update(Request $request, $email)
    {
        $user = User::where('email', $email)->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'password' => 'sometimes|required|string|min:8',
        ]);

        $user->update(array_filter([
            'name' => $validated['name'] ?? $user->name,
            'password' => isset($validated['password']) ? bcrypt($validated['password']) : $user->password,
        ]));

        return response()->json(['data' => $user]);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{email}",
     *     tags={"Users"},
     *     summary="Deletar um usuário",
     *     description="Remove um usuário existente do sistema com base em seu email.",
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         description="Email do usuário",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Usuário deletado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy($email)
    {
        $user = User::where('email', $email)->firstOrFail();
        $user->delete();

        return response()->json(null, 204);
    }
}
