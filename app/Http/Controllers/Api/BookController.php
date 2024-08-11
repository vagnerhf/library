<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use App\Models\Book;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        return BookResource::collection(Book::with('authors')->get());
    }

    public function show($key)
    {
        $book = Book::with('authors')->where('key', $key)->firstOrFail();
        return new BookResource($book);
    }

    public function store(Request $request)
    {
        $book = Book::create($request->all());
        $authorIds = Author::whereIn('key', $request->input('author_keys', []))->pluck('id')->toArray();
        $book->authors()->sync($authorIds);
        return new BookResource($book->load('authors'));
    }

    public function update(Request $request, $key)
    {
        $book = Book::where('key', $key)->firstOrFail();
        $book->update($request->all());
        $authorIds = Author::whereIn('key', $request->input('author_keys', []))->pluck('id')->toArray();
        $book->authors()->sync($authorIds);
        return new BookResource($book->load('authors'));
    }

    public function destroy($key)
    {
        $book = Book::where('key', $key)->firstOrFail();
        $book->delete();
        return response()->json(null, 204);
    }
}
