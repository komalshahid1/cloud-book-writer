<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Book;
use App\Models\Section;
use  App\Models\Collaborator;
class SectionPolicy
{
    /**
     * Determine if the user can create a new section.
     * Only the author can create new sections.
     */
    public function create(User $user, Book $book)
    {

        return $book->author->user_id === $user->id;
    }

    /**
     * Determine if the user can update a section.
     * Both author and collaborator can edit.
     */
    public function update(User $user, Section $section)
    {
        $collaborator = Collaborator::where('book_id', $section->book_id)
            ->where('user_id', $user->id)
            ->first();

        return $collaborator && in_array($collaborator->role, ['author', 'collaborator']);
    }
}
