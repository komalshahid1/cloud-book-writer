<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'file_path', 'book_id', 'parent_id'];

    /**
     * Get the parent section (if any).
     */
    public function parent()
    {
        return $this->belongsTo(Section::class, 'parent_id');
    }

    /**
     * Get all subsections (children).
     */
    public function subsections()
    {
        return $this->hasMany(Section::class, 'parent_id')->with('subsections'); // Recursive Relationship
    }

    /**
     * Get the book that owns the section.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }




    public function getContent()
    {
        return $this->file_path && Storage::disk('s3')->exists($this->file_path)
            ? Storage::disk('s3')->get($this->file_path)
            : null;
    }

    // Automatically append file content when retrieving a section
    protected $appends = ['content'];

    public function getContentAttribute()
    {
        return $this->getContent();
    }
}

