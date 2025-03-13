<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    /**
     * Determine if the user can view the book.
     */
    public function view(User $user, Book $book)
    {
        return $user->id === $book->author_id || $book->collaborators->contains($user);
    }

    /**
     * Determine if the user can create a book.
     */
    public function create(User $user)
    {
        return $user->role === 'author'; // Only authors can create books
    }

    /**
     * Determine if the user can update the book.
     */
    public function update(User $user, Book $book)
    {
        return $user->id === $book->author_id;
    }

    /**
     * Determine if the user can delete the book.
     */
    public function delete(User $user, Book $book)
    {
        return $user->id === $book->author_id;
    }
}
