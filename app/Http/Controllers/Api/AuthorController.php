<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index()
    {
        return AuthorResource::collection(Author::all());
    }

    public function show($key)
    {
        $author = Author::where('key', $key)->firstOrFail();
        return new AuthorResource($author);
    }

    public function store(Request $request)
    {
        $author = Author::create($request->all());
        return new AuthorResource($author);
    }

    public function update(Request $request, $key)
    {
        $author = Author::where('key', $key)->firstOrFail();
        $author->update($request->all());
        return new AuthorResource($author);
    }

    public function destroy($key)
    {
        $author = Author::where('key', $key)->firstOrFail();
        $author->delete();
        return response()->json(null, 204);
    }
}
