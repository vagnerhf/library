<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // GET /api/users
    public function index()
    {
        $users = User::all();
        return response()->json(['data' => $users]);
    }

    // GET /api/users/{email}
    public function show($email)
    {
        $user = User::where('email', $email)->firstOrFail();
        return response()->json(['data' => $user]);
    }

    // POST /api/users
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

    // PUT /api/users/{email}
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

    // DELETE /api/users/{email}
    public function destroy($email)
    {
        $user = User::where('email', $email)->firstOrFail();
        $user->delete();

        return response()->json(null, 204);
    }
}
