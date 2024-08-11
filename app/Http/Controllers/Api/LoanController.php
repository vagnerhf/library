<?php

namespace App\Http\Controllers\Api;

use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use Illuminate\Http\Request;
use App\Notifications\LoanCreatedNotification;

class LoanController extends Controller
{
    public function index()
    {
        return LoanResource::collection(Loan::with(['book.authors', 'user'])->get());
    }

    public function show($key)
    {
        $loan = Loan::with(['book.authors', 'user'])->where('key', $key)->firstOrFail();
        return new LoanResource($loan);
    }

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

    public function update(Request $request, $key)
    {
        $loan = Loan::where('key', $key)->firstOrFail();

        $validated = $request->validate([
            'return_date' => 'sometimes|required|date',
        ]);

        $loan->update($validated);

        return new LoanResource($loan->load(['book.authors', 'user']));
    }

    public function destroy($key)
    {
        $loan = Loan::where('key', $key)->firstOrFail();
        $loan->delete();
        return response()->json(null, 204);
    }
}
