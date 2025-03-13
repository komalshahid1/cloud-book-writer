<?php

namespace App\Http\Controllers\api;

use App\Repositories\SectionRepository;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Book;

use App\Http\Controllers\Controller;


class SectionController extends Controller
{
    protected $sectionRepository;

    /**
     * Inject the SectionRepository.
     */
    public function __construct(SectionRepository $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }



    /**
     * Get all books with their sections and nested subsections.
     */
    public function index()
    {
        $books = $this->sectionRepository->getAllSectionsWithBooks();
        return response()->json($books);
    }

    /**
     * Display all sections and their subsections for a book.
     */
    public function getSectionsByBook($bookId)
    {
        $sections = $this->sectionRepository->getSectionsByBook($bookId);
        return response()->json($sections);
    }

    /**
     * Store a new section or subsection.
     */
    public function store(Request $request)
    {
        try {

            $this->authorize('create', Book::class);
            // Validate input data
            $validatedData = $request->validate([
                'book_id' => 'required|exists:books,id',
                'parent_id' => 'nullable|exists:sections,id',
                'title' => 'required|string|max:255',
                'content' => 'nullable|string'
            ]);

            // Create section using repository
            $section = $this->sectionRepository->createSection($validatedData);
            return response()->json([
                'message' => 'Section created successfully',
                'data' => $section
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create section',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function show(Section $section)
    {
        $this->authorize('view', $section);

        return response()->json($section);
    }

    public function update(Request $request, Section $section)
    {
        try {

            $validatedData = $request->validate([
                'title' => 'string|max:255',
                'content' => 'nullable|string'
            ]);

            return response()->json($this->sectionRepository->updateSection($validatedData, $section));

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Failed to update section',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Section $section)
    {

        $this->sectionRepository->deleteSection($section);
        return response()->json(['message' => 'Section deleted successfully']);
    }
}
