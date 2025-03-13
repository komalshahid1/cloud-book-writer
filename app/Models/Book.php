<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'author_id'];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'collaborators')->withTimestamps();
    }
}
