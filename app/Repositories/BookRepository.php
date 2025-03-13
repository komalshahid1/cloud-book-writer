<?php

namespace App\Repositories;

use App\Models\Book;

class BookRepository
{
    public function getUserBooks($userId)
    {
        return Book::where('author_id', $userId)->orWhereHas('collaborators', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();
    }

    public function create(array $data)
    {
        return Book::create($data);
    }

    public function update(Book $book, array $data)
    {
        $book->update($data);
        return $book;
    }

    public function delete(Book $book)
    {
        return $book->delete();
    }
}
