<?php

namespace App\Repositories;

use App\Models\Section;
use Illuminate\Support\Facades\Storage;

class SectionRepository
{

    /**
     * Get all books with their sections and nested subsections.
     */
    public function getAllSectionsWithBooks()
    {
        // return Book::with(['sections' => function ($query) {
        //     $query->whereNull('parent_id')->with('subsections');
        // }])->get();
        return Section::whereNull('parent_id')->with('subsections')->get();
    }
    /**
     * Get all sections and their nested subsections for a book.
     */
    public function getSectionsByBook($bookId)
    {
        return Section::where('book_id', $bookId)
            ->whereNull('parent_id') // Get only root sections
            ->with('subsections') // Load nested sections
            ->get();
    }
    /**
     * Create a new section.
     */
    public function createSection(array $data)
    {

        $section = Section::create([
            'book_id' => $data['book_id'],
            'parent_id' => $data['parent_id'] ?? null,
            'title' => $data['title']
        ]);


        if (isset($data['content'])) {
            $this->saveContentToFile($section, $data['content']);
        }

        return $section;
    }

    /**
     * Update a section.
     */
    public function updateSection(array $data, Section $section)
    {
        if (isset($data['title'])) {
            $section->title = $data['title'];
        }

        if (isset($data['content'])) {
            $this->saveContentToFile($section, $data['content']);
        }

        $section->save();
        return $section;
    }
    /**
     * Delete a section and its child sections.
     */
    public function deleteSection(Section $section)
    {
        $this->deleteChildSections($section);

        if (Storage::disk('s3')->exists($section->file_path)) {
            Storage::disk('s3')->delete($section->file_path);
        }

        $section->delete();
    }


    /**
     * Recursively delete child sections.
     */
    private function deleteChildSections(Section $section)
    {
        if ($section->children) {
            foreach ($section->children as $child) {
                $this->deleteChildSections($child);

                if (Storage::disk('s3')->exists($child->file_path)) {
                    Storage::disk('s3')->delete($child->file_path);
                }

                $child->delete();
            }
        }
    }



    private function saveContentToFile(Section $section, $content)
    {
        $filePath = "books/{$section->book_id}/sections/{$section->id}.txt";
        Storage::disk('s3')->put($filePath, $content);
        $section->file_path = $filePath;
        $section->save();
    }
}
