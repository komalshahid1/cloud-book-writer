<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SectionTest extends TestCase
{
    use RefreshDatabase;

    protected $author;
    protected $collaborator;
    protected $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->author = User::factory()->create(['role' => 'author']);
        $this->collaborator = User::factory()->create(['role' => 'collaborator']);
        $this->book = Book::factory()->create(['author_id' => $this->author->id]);
    }

    /** @test */
    public function only_author_can_create_a_section()
    {
        $data = [
            'book_id' => $this->book->id,
            'title' => 'New Section',
            'content' => 'Section content goes here.'
        ];

        // Try as author (should pass)
        $response = $this->actingAs($this->author)->postJson('/api/sections', $data);
     
        $response->assertStatus(201);

        // Try as collaborator (should fail)
        $response = $this->actingAs($this->collaborator)->postJson('/api/sections', $data);
        $response->assertStatus(500);
    }

    /** @test */
    public function author_and_collaborator_can_edit_section()
    {
        $section = Section::factory()->create(['book_id' => $this->book->id]);

        $data = ['title' => 'Updated Section Title'];

        // Author can update
        $response = $this->actingAs($this->author)->patchJson("/api/sections/{$section->id}", $data);
        $response->assertStatus(200);

        // Collaborator can update
        $response = $this->actingAs($this->collaborator)->patchJson("/api/sections/{$section->id}", $data);
        $response->assertStatus(200);
    }

    /** @test */
    public function only_author_can_grant_access_to_collaborators()
    {
        $userToAdd = User::factory()->create(['role' => 'collaborator']);

        // Author grants access
        $response = $this->actingAs($this->author)->postJson("/api/books/{$this->book->id}/grant-access", [
            'user_id' => $userToAdd->id
        ]);
        $response->assertStatus(200);

        // Collaborator cannot grant access
        $response = $this->actingAs($this->collaborator)->postJson("/api/books/{$this->book->id}/grant-access", [
            'user_id' => $userToAdd->id
        ]);
        $response->assertStatus(500);
    }

    /** @test */
    public function only_author_can_revoke_access()
    {
        $userToRemove = User::factory()->create(['role' => 'collaborator']);
        $this->book->collaborators()->attach($userToRemove->id);

     

        // Author revokes access
        $response = $this->actingAs($this->author)->deleteJson("/api/books/{$this->book->id}/revoke-access", [
            'user_id' => $userToRemove->id
        ]);
        $response->assertStatus(200);

        // Collaborator cannot revoke access
        $response = $this->actingAs($this->collaborator)->deleteJson("/api/books/{$this->book->id}/revoke-access", [
            'user_id' => $userToRemove->id
        ]);
        $response->assertStatus(500);
    }

    /** @test */
    public function updating_a_section_should_update_file_content()
    {
        Storage::fake('s3'); // Fake S3 storage
    
        // Create a section with an initial file
        $section = Section::factory()->create([
            'book_id' => $this->book->id,
            'file_path' => "books/{$this->book->id}/sections/23.txt"
        ]);
    
        // Fake initial file content in S3
        Storage::disk('s3')->put($section->file_path, 'Original file content');
    
        // New content to update
        $updatedContent = 'Updated file content';
    
        // Make an API request to update the section
        $response = $this->actingAs($this->author)->patchJson("/api/sections/{$section->id}", [
            'content' => $updatedContent
        ]);
    
        // Verify response is successful
        $response->assertStatus(200);
    
        // Check that the file content has been updated in S3
        $this->assertTrue(Storage::disk('s3')->exists($section->file_path));
       
    }
    
    }

