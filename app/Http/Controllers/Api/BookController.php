<?php

namespace App\Http\Controllers\api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\BookRepository;
use App\Models\Book;


use App\Http\Controllers\Controller;

class BookController extends Controller
{

    protected $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }
    /**
     * Display a list of the books.
     */
    public function index()
    {

        return response()->json($this->bookRepository->getUserBooks(Auth::id()), 200);
    }

    /**
     * Store a new book.
     */
    public function store(Request $request)
    {

        $this->authorize('create', Book::class);
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $book = $this->bookRepository->create([
            'title' => $request->title,
            'author_id' => Auth::id(),
        ]);

        return response()->json($book, 201);
    }

    /**
     * Display a specific book.
     */
    public function show(Book $book)
    {
        $this->authorize('view', $book);

        return response()->json($book);
    }

    /**
     * Update an existing book.
     */
    public function update(Request $request, Book $book)
    {

        $this->authorize('update', $book);
        $book = $this->bookRepository->update($book, $request->only(['title']));
        return response()->json($book);
    }

    /**
     * Remove a book from storage.
     */
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);
        $this->bookRepository->delete($book);
        return response()->json(['message' => 'Book deleted successfully']);
    }
}
