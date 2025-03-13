<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Section;

class SectionSeeder extends Seeder
{
    public function run()
    {
        // Create a book
        $book = Book::firstOrCreate(['title' => 'Nested Sections Book', 'author_id' => 1]);

        // Create a root section
        $rootSection = Section::create([
            'book_id' => $book->id,
            'title' => 'Root Section'
        ]);

        // Generate 10 levels of nested sections
        $this->createNestedSections($book->id, $rootSection->id, 1);
    }

    private function createNestedSections($bookId, $parentId, $level)
    {
        if ($level > 10) return; // Stop at level 10

        // Create a subsection
        $section = Section::create([
            'book_id' => $bookId,
            'parent_section_id' => $parentId,
            'title' => "Nested Level $level"
        ]);

        // Recursively create the next level
        $this->createNestedSections($bookId, $section->id, $level + 1);
    }
}
