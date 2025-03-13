<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Repositories\CollaboratorRepository;

class CollaboratorController extends Controller
{

    protected $collaboratorRepository;

    public function __construct(CollaboratorRepository $collaboratorRepository)
    {
        $this->collaboratorRepository = $collaboratorRepository;
    }
 
    public function grantAccess(Request $request, Book $book)
    {
        $this->authorize('create', $book);

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $this->collaboratorRepository->addCollaborator($book->id,$request->user_id );
        // Ensure the user is not already a collaborator

        return response()->json(['message' => 'Access granted successfully']);
    }


    public function revokeAccess(Request $request, Book $book)
    {
        $this->authorize('create', $book);

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $this->collaboratorRepository->removeCollaborator($book->id,$request->user_id );

        return response()->json(['message' => 'Access revoked successfully']);
    }
}
