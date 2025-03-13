<?php

namespace App\Repositories;

use App\Models\Collaborator;

class CollaboratorRepository
{
    public function addCollaborator($bookId,$user_id)
    {
        return Collaborator::updateOrCreate(
            ['book_id' => $bookId, 'user_id' => $user_id],
            ['role' => 'collaborator']
        );
    }

    public function removeCollaborator($bookId,$user_id)
    {
        return Collaborator::where([
            'book_id' => $bookId,
            'user_id' => $user_id
        ])->delete();
    }
}
